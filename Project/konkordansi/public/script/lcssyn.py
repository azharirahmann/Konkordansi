import sys, json, base64
import requests, time, re
import json
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize

# Load data dari php ke python
try:
    query = json.loads(base64.b64decode(sys.argv[1]))
except:
    print "ERROR"
    sys.exit(1)

def preprocessData(query):
    stop_words = set(stopwords.words('english'))
    preQuery = query.lower()
    preQuery = re.sub(r'[^a-zA-Z0-9 ]',r'',preQuery)
    word_tokens = word_tokenize(preQuery)

    filtered_sentence = []
    for w in word_tokens:
        if w not in stop_words:
            filtered_sentence.append(w)

    sentence = ' '.join(word for word in filtered_sentence)

    return sentence

def checkNoun(data):
	i = 0
	for entry in data['results'][0]['lexicalEntries']:
		if (entry['lexicalCategory'] == 'Noun'):
			return i
		i+=1
	return -1	

app_id = '4360f570'
app_key = '9c6ec13b52f2ecbad5a6ef74d880a9ab'

language = 'en'
# query = 'grave.'
word_id = preprocessData(query)

# start_time = time.time()

url = 'https://od-api.oxforddictionaries.com:443/api/v1/entries/' + language + '/' + word_id + '/synonyms'

r = requests.get(url, headers = {'app_id': app_id, 'app_key': app_key})

# print("code {}\n".format(r.status_code))
# print("text \n" + r.text)
# print("json \n" + json.dumps(r.json()['metadata']['provider']))
# print word_id
syns = []
if (r.status_code != 404):
	meta = r.content			# error kalo misalkan sinonimnya ga ada
	data = json.loads(meta)
	idx	 = checkNoun(data)
	if (idx != -1):
		for row in data['results'][0]['lexicalEntries'][0]['entries'][idx]['senses'][0]['synonyms']:
			syns.append(row['text'])
	else:
		syns.append('No results')
else:
	syns.append('No results')				

# print 'word : ' + word_id
# print syns
# print("--- %s seconds ---" % (time.time() - start_time))
print json.dumps(syns)