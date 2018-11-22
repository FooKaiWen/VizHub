import pymongo
import re
from sklearn.metrics import mean_squared_error
import numpy as np
import nltk
from nltk.tokenize import word_tokenize as wt
from nltk.corpus import stopwords
from nltk.stem.porter import PorterStemmer
from autocorrect import spell

import pandas
from sklearn import model_selection
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer

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
# message = "Roberts took the unusual step of devoting the majority of  his annual  report to the issue of judicial ethics."
message = re.sub('[^A-Za-z]',' ',message)
message = message.lower()
tokenized_message = wt(message)
message_processed = []
data = []
stemmer = PorterStemmer()
for word in tokenized_message:
    stem(word)
    if word not in set(stopwords.words('english')):
        message_processed.append(spell(stemmer.stem(word)))
        
message_text = " ".join(message_processed)
data.append(message_text)

def train_model(classifier, feature_vector_train, label, feature_vector_valid):
    # fit the training dataset on the classifier
    classifier.fit(feature_vector_train, label)
    # predict the labels on validation dataset
    prediction = classifier.predict(feature_vector_valid)
    return prediction
#     return metrics.accuracy_score(predictions, valid_y)

dataset = pandas.read_csv('savefile.csv',encoding='ISO-8859-1')

if(selection == "2000"): #0.60
        train_x, valid_x, train_y, valid_y = model_selection.train_test_split(dataset['message'], dataset['two_thousand_likes'])
        tfidf_vect_ngram_chars = TfidfVectorizer(analyzer='char', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=5000)
        tfidf_vect_ngram_chars.fit(dataset['message'])
        xtrain_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(train_x) 
        xvalid_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(valid_x) 

if(selection == "5000"): #0.84
        train_x, valid_x, train_y, valid_y = model_selection.train_test_split(dataset['message'], dataset['five_thousand_likes'])
if(selection == "2500"): #0.60
        train_x, valid_x, train_y, valid_y = model_selection.train_test_split(dataset['message'], dataset['twentyfive_hundred_likes'])

xtrain_count =  count_vect.transform(train_x)
xvalid_count = count_vect.transform(data)
accuracy = train_model(linear_model.LogisticRegression(), xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)


# mycol.update_one({"pmessage":getMessage['pmessage']},{"$set":{"likesNum":"123"}})
# import pandas as pd
# import re
# import nltk
# from nltk.tokenize import word_tokenize as wt # nltk.download('punkt')
# from nltk.corpus import stopwords # nltk.download('stopwords')
# from nltk.stem.porter import PorterStemmer
# from autocorrect import spell #spell correction

# dataset = pd.read_csv('test.csv',encoding='ISO-8859-1')

# stemmer = PorterStemmer()

# data = []

# def stem(word):
#         for suffix in ['ing', 'ly', 'ed', 'ious', 'ies', 'ive', 'es', 's', 'ment']:
#                 if word.endswith(suffix):
#                         return word[:-len(suffix)]
#         return word

# def process():
#         for i in range(dataset.shape[0]): # This is where messages are cleaned and stemmed to make them uniform.
#                 if(pd.notna(dataset.iloc[i,1])):
#                         message = dataset.iloc[i,1]
#                         # print ("Ori Message")
#                         # print (message)
#                         #remove non alphabetic characters
#                         message = re.sub('[^A-Za-z]',' ',message)
#                         # print ("Alphabetic Message")
#                         # print (message)
#                         #make words lowercase
#                         message = message.lower()
#                         # print ("LowCase Message")
#                         # print (message)
#                         tokenized_message = wt(message)
#                         # print ("Tokenized Message")
#                         # print (tokenized_message)
#                         message_processed = []

#                         for word in tokenized_message:
#                                 stem(word)
#                                 if word not in set(stopwords.words('english')):
#                                         message_processed.append(spell(stemmer.stem(word)))
                        
#                         message_text = " ".join(message_processed)
#                         # print("Stemmed Message")
#                         # print(message_text)
#                         data.append(message_text)
    
#         from sklearn.feature_extraction.text import CountVectorizer    
#         # from sklearn.feature_extraction.text import TfidfVectorizer #This method counts how many times a word is used.
#         from sklearn.model_selection import train_test_split
#         # from sklearn import linear_model # Linear regression is used
#         from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
#         import numpy as np

#         matrix2 = CountVectorizer()
#         matrix2.fit(data)
#         x = matrix2.transform(data)

#         # #This method balance out the number of times of used words with others
#         from sklearn.feature_extraction.text import TfidfTransformer
#         tfidf_transformer = TfidfTransformer().fit(x)

#         from sklearn.externals import joblib
#         joblib.dump(tfidf_transformer,'tfidf.joblib')
#         loadedvec = joblib.load('tfidf.joblib')

#         loadedvec.transform(x).toarray()
#         tfidf2_text = tfidf_transformer.transform(x).toarray()
#         print(tfidf2_text)
#         likes = dataset['likes']

#         tfidf2_text_train, tfidf2_text_test, likes2_train, likes2_test = \
#         train_test_split(tfidf2_text, likes, test_size=0.3)

#         tfidf_text = tfidf_transformer.transform(x).toarray()
#         print(tfidf_text)

#         tfidf_text_train, tfidf_text_test, likes_train, likes_test = \
#         train_test_split(tfidf_text, likes, test_size=0.3)

#         from sklearn.ensemble import RandomForestRegressor # Random Forest Regressor is used

#         forest_reg = RandomForestRegressor(random_state=42)
#         forest_reg.fit(tfidf_text_train, likes_train)
#         likes_pred = forest_reg.predict(tfidf_text_test)
#         forest_mse = mean_squared_error(likes_pred, likes_test)
#         forest_rmse = np.sqrt(forest_mse)
#         print('Random Forest RMSE: %.4f' % forest_rmse)

#         from sklearn.externals import joblib
#         joblib.dump(forest_reg,'test.joblib')
#         loadedmodel = joblib.load('test.joblib')
#         likes_pred = loadedmodel.predict(tfidf_text_test)
#         forest_mse = mean_squared_error(likes_pred, likes_test)
#         forest_rmse = np.sqrt(forest_mse)
#         print('Random Forest RMSE: %.4f' % forest_rmse)