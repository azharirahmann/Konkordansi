import sys, json, base64
import numpy as np

# Load data dari php ke python
try:
    source = json.loads(base64.b64decode(sys.argv[1]))
    query = json.loads(base64.b64decode(sys.argv[2]))
except:
    print "ERROR"
    sys.exit(1)

#Fungsi LCS
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
    return result

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



#Panggil fungsi LCS
source = 'it is those who will be the successful'
query = 'those who are the successful'
callLcs = lcs(source, query)
# callLcs.sort()
# marked = markingString(source, callLcs)
# print marked

# Generate data Json utk dikirim ke PHP
result = {'lcs': callLcs}

# # Mengirimkan stdout (ke PHP)
print json.dumps(result)