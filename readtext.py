import pymongo
import re
import pandas
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.externals import joblib

Client = pymongo.MongoClient("mongodb://localhost:27017/")
userDB = Client["fb"]
userCol = userDB["predictMessage"]

getMessage = userCol.find_one()
message = getMessage['pmessage']

message = re.sub('[^A-Za-z]',' ',message)
message = message.lower()
data = [message]

loadedVect = TfidfVectorizer(decode_error="ignore",vocabulary=joblib.load('feature.joblib'))
loadedModel = joblib.load('model.joblib')
transformedData =  loadedVect.fit_transform(data)
predictedValue = loadedModel.predict(transformedData)

userCol.update_one({"pmessage":getMessage['pmessage']},{"$set":{"likesRange":int(predictedValue)}})



