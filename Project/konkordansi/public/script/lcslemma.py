import sys, json, base64, mysql.connector
from nltk.stem import WordNetLemmatizer
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from operator import itemgetter
import string, re, time
import numpy as np

# Load data dari php ke python
try:
    query = json.loads(base64.b64decode(sys.argv[1]))
    lower = json.loads(base64.b64decode(sys.argv[2]))
except:
    print "ERROR"
    sys.exit(1)

# Fungsi LCS
def lcs(source, query):
    # inisialisasi matriks, dimana semua baris 0 dan kolom 0 diisi dengan angka 0
    lcsMatrix = [[0 for j in range(len(query)+1)] for i in range(len(source)+1)]
    
    #penerapan algoritma lcs
    for i, x in enumerate(source):
        for j, y in enumerate(query):
            if x == y:
                lcsMatrix[i+1][j+1] = lcsMatrix[i][j] + 1
            else:
                lcsMatrix[i+1][j+1] = max(lcsMatrix[i+1][j], lcsMatrix[i][j+1])

    # membaca string yang dihasilkan oleh matriks
    result = ""
    r = []
    x, y = len(source), len(query)
    while x != 0 and y != 0:
        if lcsMatrix[x][y] == lcsMatrix[x-1][y]:
            x -= 1
        elif lcsMatrix[x][y] == lcsMatrix[x][y-1]:
            y -= 1
        else:
            assert source[x-1] == query[y-1]
            result = source[x-1] + result
            r.append(int(x)-1)
            x -= 1
            y -= 1

    rs = np.asarray(r)        
    return result, rs

#Mark the string
def markingString(source, index):
    first = -1
    last = -1
    for i in range(0, len(index)):        
        if (i <= len(index)):
            if (first == -1):
                first = index[i]
                if (i+1 < len(index) and index[i]+1 != index[i+1]):
                    source = source[:first] + '<b>' + source[first] + '</b>' + source[first+1:]
                    # tambah nilai semua array sesuai dgn panjang string (first)+7
                    index += 7
                    first = -1
                elif (i == len(index)-1):
                    source = source[:first] + '<b>' + source[first] + '</b>' + source[first+1:]    
            else:    
                if (i+1 < len(index) and index[i]+1 == index[i+1]):
                    # untuk menentukan awal dan akhir penebalan string
                    last = index[i]
                else:
                    last = index[i]
                    source = source[:first] + '<b>' + source[first:last+1] + '</b>' + source[last+1:]
                    # tambah nilai semua array sesuai dgn panjang string (last-first)+7
                    index += 7 
                    first = -1
                    last = -1

    return source

def preprocessData(query):
    stop_words = set(stopwords.words('english'))
    preQuery = query.lower()
    # preQuery = re.sub(r'[^a-zA-Z0-9 ]',r'',preQuery)
    # preQuery = preQuery.translate(None, string.punctuation) 
    wordnet_lemmatizer = WordNetLemmatizer()
    word_tokens = word_tokenize(preQuery)

    filtered_sentence = []
    for w in word_tokens:
        if w not in stop_words:
            filtered_sentence.append(w)

    hasilPemotongan = []
    for f in filtered_sentence:
        hasilPemotongan.append(wordnet_lemmatizer.lemmatize(f))

    return hasilPemotongan

# start_time = time.time()
# Main
# Inisialiasi koneksi ke database
conn = mysql.connector.connect(user='root',password='',host='localhost',database='konkordansi')

cur = conn.cursor()

# query = 'gives charity'
query = query.lower()
query = re.sub(r'[^a-zA-Z0-9 ]',r'',query)
preQuery = preprocessData(query)

# inisialisasi query
queryCmd = "SELECT * FROM qword"
z = 0

# membuat query
if len(preQuery) == 0:
    queryCmd = queryCmd + " WHERE Translation LIKE '%" + query + "%'"
else:
    for x in preQuery:
        if z == 0:
            queryCmd = queryCmd + " WHERE Translation LIKE '%" + x + "%'"
        else:
            #  sesuaikan nanti bagusnya pake AND atau OR
            queryCmd = queryCmd + " OR Translation LIKE '%" + x + "%'"
        z += 1

# Menjalankan query dari variabel string queryCmd
cur.execute(queryCmd)

# Inisialisasi array hasil lcs
arr = []

# Set lower and upper values
lower   = int(lower)
upper   = 100

row = cur.fetchall()
for i in range(len(row)):
    source = row[i][7]
    x = source.lower()
    x = re.sub(r'[^a-zA-Z0-9 ]',r'',x)
        
    callLcs, rs = lcs(x, query)
    rs.sort()

    # hitung metric lcs nya
    Maxlen      = max(int(len(x)), int(len(query)))
    distance    = (float (len(callLcs))/ float(Maxlen))
    distance    = float("{0:.4f}".format(distance))
    percent     = distance * 100
    percent     = float("{0:.4f}".format(percent))

    distance2= (float (len(callLcs))/ float(len(query)))
    percent2= distance2 * 100
    if int(len(preQuery)) > 1:
        gain = 60
    else:
        gain = 0 

    # bikin kondisi buat filter persentase, misal diantara 30-60%, 60-80%, 80-100%
    if (percent>=lower and percent<=upper and percent2>=gain):
        marked = markingString(x, rs)
        joinQuery = "SELECT XlatAr,text FROM qline WHERE (SId=" + str(row[i][2]) + " AND VID=" + str(row[i][3]) + ")"
        cur.execute(joinQuery)
        res = cur.fetchone()

        # print repr(callLcs) + repr(percent) + " Indeks => " + repr(row[i][2]) + ":" + repr(row[i][3]) + ":" + repr(row[i][4])
        # print marked
        # # print joinedStr
        # print y
        # print check
        # arr.append([row[i][0], row[i][2], row[i][3], row[i][4], row[i][7], callLcs, marked, percent])
        arr.append([row[i][0], row[i][2], row[i][3], row[i][4], row[i][7], res[0], res[1], callLcs, marked, percent, row[i][5]])

# # row = cur.fetchall()

conn.close()

# # Sorting array berdasarkan persentase kemiripan
arr = sorted(arr, key=itemgetter(9), reverse=True)
# print len(arr)

# Mengirimkan stdout (ke PHP)
print json.dumps(arr)