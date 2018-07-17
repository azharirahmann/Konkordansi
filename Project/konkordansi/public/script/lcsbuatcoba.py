import math, operator
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
    x, y = len(source), len(query)
    while x != 0 and y != 0:
        if lcsMatrix[x][y] == lcsMatrix[x-1][y]:
            x -= 1
        elif lcsMatrix[x][y] == lcsMatrix[x][y-1]:
            y -= 1
        else:
            assert source[x-1] == query[y-1]
            result = source[x-1] + result
            x -= 1
            y -= 1
    return result

#Panggil fungsi LCS
query = 'paradise of Allah dont'
source = 'a party of those who had been given the Scripture threw the Scripture of Allah behind their backs as if they did not know [what it contained]'
callLcs = lcs(source, query)
Maxlen = max(int(len(source)), int(len(query)))
# Perhitungan buat persentase LCS nya baiknya gimana
distance = (float (len(callLcs))/ float(Maxlen))
distance = float("{0:.4f}".format(distance))
percent = distance * 100

print callLcs
print 'distance : ' + repr(distance)
print 'Maxlen : ' + repr(Maxlen) + ' dan lcs : ' + repr(len(callLcs))
print 'percent ' + repr(percent) + '%'