import pandas as pd
import sys, json, base64, mysql.connector
from nltk.stem.porter import PorterStemmer
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from operator import itemgetter
import string, re, time
import numpy as np

# Load data dari php ke python
# try:
#     query = json.loads(base64.b64decode(sys.argv[1]))
# except:
#     print "ERROR"
#     sys.exit(1)

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
    porter_stemmer = PorterStemmer()
    word_tokens = word_tokenize(preQuery)

    filtered_sentence = []
    for w in word_tokens:
        if w not in stop_words:
            filtered_sentence.append(w)

    hasilPemotongan = []
    for f in filtered_sentence:
        hasilPemotongan.append(porter_stemmer.stem(f))

    return hasilPemotongan

def checkTheTruth(query, source):
    stop_words = set(stopwords.words('english'))
    preSource = re.sub(r'[^a-zA-Z0-9 ]',r'',source)
    porter_stemmer = PorterStemmer()
    word_tokens = word_tokenize(preSource)

    filtered_sentence = []
    for w in word_tokens:
        if w not in stop_words:
            filtered_sentence.append(w)

    hasilPemotongan = []
    for f in filtered_sentence:
        hasilPemotongan.append(porter_stemmer.stem(f))

    result = []
    for h in hasilPemotongan:
        if h in query:
            result.append(h)

    return result

def readData(hQ):
    with open("readtest.csv") as f:
        data = np.genfromtxt(f, delimiter=" ", dtype=str)

    a = []
    b = []
    for x in range(len(hQ)):
        # TP
        if hQ[x][0] in data:
            a.append(hQ[x])
        elif hQ[x][0] not in data:
            # FP, di hq ada, di gs tdk ada
            b.append(hQ[x])

    c = []
    count = 0
    # FN, di gs ada, di hq tidak ada
    for y in range(len(data)):
        for z in range(len(hQ)):
            if(data[y] != hQ[z][0]):
                count = count + 1
        if count == len(hQ):
            c.append(data[y])
        count = 0

    return a,b,c

def differ(tp, fp, fn):
    if tp:
        print "===TP==="
        for x in tp:
            print "Indeks => " + repr(x[0]) + " Similarity => " + repr(x[1])    
        print "======== \n"

    print "===FP==="    
    if fp:
        for x in fp:
            print "Indeks => " + repr(x[0]) + " Similarity => " + repr(x[1])
        print "======== \n"
    else:
        print "Kosong"        
        print "======== \n"

    print "===FN==="    
    if fn:
        for x in fn:
            print "Indeks => " + repr(x)              
        print "======== \n"     
    else:
        print "Kosong"    
        print "======== \n"

def markTP(hQ):
    with open("readtest.csv") as f:
        data = np.genfromtxt(f, delimiter=" ", dtype=str)

    a = []
    count = 1
    for x in range(len(hQ)):
        # TP
        if hQ[x][0] in data:
            a.append([count, hQ[x][0], hQ[x][1], 'Benar'])
        else:
            a.append([count, hQ[x][0], hQ[x][1], 'x'])
        count = count + 1
    return a               

# start_time = time.time()
# Main
# Inisialiasi koneksi ke database
conn = mysql.connector.connect(user='root',password='',host='localhost',database='konkordansi')

cur = conn.cursor()

query = 'they who disbelieve in the verses'
query = query.lower()
query = re.sub(r'[^a-zA-Z0-9 ]',r'',query)
preQuery = preprocessData(query)

# inisialisasi query
queryCmd = "SELECT * FROM qline"
z = 0

# membuat query
if len(preQuery) == 0:
    queryCmd = queryCmd + " WHERE clean_text LIKE '%" + query + "%'"
else:
    for x in preQuery:
        if z == 0:
            queryCmd = queryCmd + " WHERE clean_text LIKE '%" + x + "%'"
        else:
            #  sesuaikan nanti bagusnya pake AND atau OR
            queryCmd = queryCmd + " AND clean_text LIKE '%" + x + "%'"
        z += 1

# Menjalankan query dari variabel string queryCmd
cur.execute(queryCmd)

# Inisialisasi array hasil lcs
arr = []

# Set lower and upper values
lower   = 30
upper   = 100

for row in cur.fetchall():
    source  = row[8].lower()
    splitt  = filter(None, re.split("[,\-!?.;]+", source))
    for y in splitt:
        x   = y.lower()
        if query in x:
            percent     = float("{0:.4f}".format(90))
        
            indeksAyat  = repr(row[1]) + ":" + repr(row[2])
            arr.append([indeksAyat, percent])
        else:    
            check = checkTheTruth(preQuery, x)
            if (len(check) > 0):
                callLcs, rs = lcs(x, query)
                rs.sort()

                # hitung metric lcs nya
                Maxlen      = max(int(len(x)), int(len(query)))
                distance    = (float (len(callLcs))/ float(Maxlen))
                distance    = float("{0:.4f}".format(distance))
                percent     = distance * 100
                percent     = float("{0:.4f}".format(percent))

                # bikin kondisi buat filter persentase, misal diantara 30-60%, 60-80%, 80-100%
                if (percent>=lower and percent<=upper):
                    indeksAyat  = repr(row[1]) + ":" + repr(row[2])
                    arr.append([indeksAyat, percent])


# row = cur.fetchall()

conn.close()
# print 'done'
# print("--- %s seconds ---" % (time.time() - start_time))

# Generate data Json utk dikirim ke PHP
# result = {'lcs': 'sesadas'}

# Sorting array berdasarkan persentase kemiripan
arr = sorted(arr, key=itemgetter(1), reverse=True)
tp, fp, fn = readData(arr)
differ(tp, fp, fn)

print len(arr)
# arr = sorted(arr, key=lambda arrs: arrs[8])

# Mengirimkan stdout (ke PHP)
# print json.dumps(arr)

arr = markTP(arr)

## convert your array into a dataframe
df = pd.DataFrame(arr)

## save to xlsx file

filepath = 'kataFrasa - ' + query + '.xlsx'

df.to_excel(filepath, index=False)