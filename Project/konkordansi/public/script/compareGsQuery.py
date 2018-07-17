import numpy as np
from nltk.tokenize import word_tokenize

hQ = ['22:28:14', '22:34:12', '80:5:2']

def readData():
    with open("readtest.csv") as f:
        data = np.genfromtxt(f, delimiter=" ", dtype=str)

    performansi = []
    a = []
    b = []
    for x in range(len(hQ)):
        # TP
        if hQ[x] in data:
            a.append(hQ[x])
        elif hQ[x] not in data:
            # FP, di hq ada, di gs tdk ada
            b.append(hQ[x])

    c = []
    # FN, di gs ada, di hq tidak ada
    for y in range(len(data)):
        if data[y] not in hQ:
            c.append(data[y])

    performansi.append(a)
    performansi.append(b)
    performansi.append(c)

    return performansi


r = readData()
print r