import pymongo
import re
import nltk
from nltk.tokenize import word_tokenize as wt
from nltk.corpus import stopwords
from nltk.stem.porter import PorterStemmer
from autocorrect import spell
import numpy as np
import pandas
from sklearn import model_selection, linear_model, metrics
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer

import warnings
warnings.filterwarnings("ignore", category=FutureWarning)

def stem(word):
    for suffix in ['ing', 'ly', 'ed', 'ious', 'ies', 'ive', 'es', 's', 'ment']:
        if word.endswith(suffix):
            return word[:-len(suffix)]
    return word

myclient = pymongo.MongoClient("mongodb://localhost:27017/")
mydb = myclient["fb"]
mycol = mydb["predictMessage"]

getMessage = mycol.find_one()
message = getMessage['pmessage']
selection = getMessage['selection']

message = re.sub('[^A-Za-z]',' ',message)
message = message.lower()
tokenized_message = wt(message)
message_processed = []
# data = []
stemmer = PorterStemmer()
for word in tokenized_message:
    stem(word)
    if word not in set(stopwords.words('english')):
        message_processed.append(spell(stemmer.stem(word)))
        
message_text = " ".join(message_processed)
data = [message_text]
print("here1")
dataset = pandas.read_csv('250_5000.csv',encoding='ISO-8859-1')

if(selection == "2000"): #0.60
        train_x, test_x, train_y, test_y = model_selection.train_test_split(dataset['message'], dataset['likes'])
elif(selection == "5000"): #0.84
        train_x, test_x, train_y, test_y = model_selection.train_test_split(dataset['message'], dataset['likes'])
        print("here2")
elif(selection == "2500"): #0.60
        train_x, test_x, train_y, test_y = model_selection.train_test_split(dataset['message'], dataset['likes'])

tfidf_vect_ngram_chars = TfidfVectorizer(analyzer='char', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=5000)
tfidf_vect_ngram_chars.fit(dataset['message'])
xtrain_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(train_x)
xtest_tfidf_ngram_chars = tfidf_vect_ngram_chars.transform(test_x)
lrmodel = linear_model.LogisticRegression()
lrmodel.fit(xtrain_tfidf_ngram_chars,train_y)
prediction = lrmodel.predict(xtest_tfidf_ngram_chars)
accuracy = metrics.accuracy_score(prediction, test_y)

mycol.update_one({"pmessage":getMessage['pmessage']},{"$set":{"accuracy":accuracy*100}})

xtest_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(data) 
lrmodel = linear_model.LogisticRegression()
lrmodel.fit(xtrain_tfidf_ngram_chars,train_y)
prediction = lrmodel.predict(xtest_tfidf_ngram_chars)
print(prediction)
for x in np.nditer(prediction):
        value = x

mycol.update_one({"pmessage":getMessage['pmessage']},{"$set":{"likesRange":int(prediction)}})


#         from sklearn.externals import joblib
#         joblib.dump(forest_reg,'test.joblib')
#         loadedmodel = joblib.load('test.joblib')
#         likes_pred = loadedmodel.predict(tfidf_text_test)
#         forest_mse = mean_squared_error(likes_pred, likes_test)
#         forest_rmse = np.sqrt(forest_mse)
#         print('Random Forest RMSE: %.4f' % forest_rmse)