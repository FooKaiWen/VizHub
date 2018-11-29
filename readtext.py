import pymongo
import re
import pandas
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.externals import joblib

myclient = pymongo.MongoClient("mongodb://localhost:27017/")
mydb = myclient["fb"]
mycol = mydb["predictMessage"]

getMessage = mycol.find_one()
message = getMessage['pmessage']

message = re.sub('[^A-Za-z]',' ',message)
message = message.lower()
data = [message]

loaded_vect = TfidfVectorizer(decode_error="ignore",vocabulary=joblib.load('feature.joblib'))
loadedmodel = joblib.load('model.joblib')
xvalid_tfidf =  loaded_vect.fit_transform(data)
prediction = loadedmodel.predict(xvalid_tfidf)

mycol.update_one({"pmessage":getMessage['pmessage']},{"$set":{"likesRange":int(prediction)}})