import sys, json, base64
from nltk.stem import WordNetLemmatizer
from nltk.stem.porter import PorterStemmer
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
import string, time
import numpy as np
from string import digits
import mysql.connector

# Load data dari php ke python
# try:
#     query = json.loads(base64.b64decode(sys.argv[1]))
# except:
#     print "ERROR"
#     sys.exit(1)

def preprocessData(data):
    with open(data) as f:
        dataset = np.genfromtxt(f, delimiter="|", dtype=str)

    stop_words = set(stopwords.words('english'))
    porter_stemmer = PorterStemmer()
    for i in range(0, len(dataset)):
        print 'now processing ' + repr(dataset[i][0]) + ':' + repr(dataset[i][1])
        dataset[i][2] = dataset[i][2].lower()
        dataset[i][2] = dataset[i][2].translate(None, string.punctuation)
        word_tokens = word_tokenize(dataset[i][2])

        hasilPemotongan = []
        for f in word_tokens:
            hasilPemotongan.append(porter_stemmer.stem(f))    

        dataset[i][2] = ""
        for x in hasilPemotongan:
            dataset[i][2] = dataset[i][2] + " " + x

    a = np.asarray(dataset)
    np.savetxt('testing_clean_nostopwords.csv', a, fmt='%3s', delimiter="|")

# processTes = "en.sahih.txt"
# preprocessData(processTes)

start_time = time.time()

conn = mysql.connector.connect(user='root',password='',host='localhost',database='konkordansi')

cur = conn.cursor()

cur.execute("SELECT w.*, l.XlatAr, l.text FROM qword w JOIN qline l ON (w.SId = l.SId AND w.VId = l.VID) WHERE w.Translation LIKE '%relative%'")

# for row in cur.fetchall():
#     print row
print("--- %s seconds ---" % (time.time() - start_time))

# clean = "testing_clean_nostopwords.csv"
# with open(clean) as f:
#     dataset = np.genfromtxt(f, delimiter="|", dtype=str)

# row = cur.fetchall()
# # updateQuery = "UPDATE qline SET clean_text='" + dataset[0][2] + "' WHERE Id=" + str(row[0][0])
# # cur.execute(updateQuery)

# for i in range(0, len(row)):
#     print "Updating " + str(row[i][0]) + ":" + str(row[i][1])
#     updateQuery = "UPDATE qline SET clean_text='" + dataset[i][2] + "' WHERE Id=" + str(row[i][0])
#     cur.execute(updateQuery)
#     conn.commit()
    
conn.close()

#Panggil fungsi preprocess
# query = '(are) the successful ones.'
# finalQuery = preprocessData(query)

# # Generate data Json utk dikirim ke PHP
# result = {'lcs': finalQuery}

# # Mengirimkan stdout (ke PHP)
# # json.dumps(result)

# for x in finalQuery:
#     print x