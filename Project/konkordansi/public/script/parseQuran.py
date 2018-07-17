import sys, json, base64
import requests, time, re
import json
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize
import numpy as np
import pandas as pd

# start_time = time.time()

# print("code {}\n".format(r.status_code))
# print("text \n" + r.text)
# print("json \n" + json.dumps(r.json()['metadata']['provider']))
# print word_id
syns = []
count 		= 1

query 			= 'those who were arrogant'
searchQuery		= query.replace(" ", "%20")

#tiap ganti query, ganti nilai totalPage juga
totalPage 	= 22
for i in range(1,totalPage+1):
	print "Now processing page " + repr(i)
	url = 'http://staging.quran.com:3000/api/v3/search?q=' + searchQuery + '&size=20&page=' + str(i) + '&language=en'
	r = requests.get(url)	
	if (r.status_code != 404):
		meta = r.content
		data = json.loads(meta)
		for row in data['results']:
			syns.append([count, str(row['verse_key'])])
			count = count + 1

# kodingan buat ngitung TP, FP, FN, TN ; nandain yang benar sama salah (x)
def readData(hQ):
    with open("readtest.csv") as f:
        data = np.genfromtxt(f, delimiter=" ", dtype=str)

    a = []
    b = []
    for x in range(len(hQ)):
        # TP
        if hQ[x][1] in data:
            a.append(hQ[x])
        elif hQ[x][1] not in data:
            # FP, di hq ada, di gs tdk ada
            b.append(hQ[x])

    c = []
    count = 0
    # FN, di gs ada, di hq tidak ada
    for y in range(len(data)):
        for z in range(len(hQ)):
            if(data[y] != hQ[z][1]):
                count = count + 1
        if count == len(hQ):
            c.append(data[y])
        count = 0

    return a,b,c

def differ(tp, fp, fn):
    if tp:
        print "===TP==="
        for x in tp:
            print "Indeks => " + x[1]

        print "len TP : " + repr(len(tp))    
        print "======== \n"

    print "===FP==="    
    if fp:
        print "len FP : " + repr(len(fp))
        print "======== \n"
    else:
        print "Kosong"        
        print "======== \n"

    print "===FN==="    
    if fn:
        for x in fn:
            print "Indeks => " + x

        print "len FN : " + repr(len(fn))
        print "======== \n"     
    else:
        print "Kosong"    
        print "======== \n"    

def markTP(hQ):
    with open("readtest.csv") as f:
        data = np.genfromtxt(f, delimiter=" ", dtype=str)

    a = []
    for x in range(len(hQ)):
        # TP
        if hQ[x][1] in data:
            a.append([hQ[x][0], hQ[x][1], 'Benar'])
        else:
            a.append([hQ[x][0], hQ[x][1], 'x'])
    return a

tp, fp, fn = readData(syns)
differ(tp, fp, fn)

print len(syns)


syns = markTP(syns)
## convert your array into a dataframe
df = pd.DataFrame(syns)

## save to xlsx file

filepath = 'compQuran.com - ' + query + '.xlsx'

df.to_excel(filepath, index=False)
print "DONE !!!"