# -*- coding: utf8 -*-
import re
import nltk

sentence = 'مصادمات عنيفه في'

wordsArray = nltk.word_tokenize(sentence)
print " ".join(wordsArray)