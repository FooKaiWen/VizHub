import pandas as pd
import re
import nltk
from nltk.tokenize import word_tokenize as wt # nltk.download('punkt')
from nltk.corpus import stopwords # nltk.download('stopwords')
from nltk.stem.porter import PorterStemmer
from autocorrect import spell #spell correction

dataset = pd.read_csv('250_5000.csv',encoding='ISO-8859-1')
dataset2 = pd.read_csv('250_5000.csv',encoding='ISO-8859-1')

stemmer = PorterStemmer()

data = []

def stem(word):
    for suffix in ['ing', 'ly', 'ed', 'ious', 'ies', 'ive', 'es', 's', 'ment']:
        if word.endswith(suffix):
            return word[:-len(suffix)]
    return word

for i in range(dataset.shape[0]): # This is where messages are cleaned and stemmed to make them uniform.
    if(pd.notna(dataset.iloc[i,1])):
        message = dataset.iloc[i,1]
        # print ("Ori Message")
        # print (message)
        #remove non alphabetic characters
        message = re.sub('[^A-Za-z]',' ',message)
        # print ("Alphabetic Message")
        # print (message)
        #make words lowercase
        message = message.lower()
        # print ("LowCase Message")
        # print (message)
        tokenized_message = wt(message)
        # print ("Tokenized Message")
        # print (tokenized_message)
        message_processed = []

        for word in tokenized_message:
            stem(word)
            if word not in set(stopwords.words('english')):
                message_processed.append(spell(stemmer.stem(word)))
        
        message_text = " ".join(message_processed)
        # print("Stemmed Message")
        # print(message_text)
        data.append(message_text)
        dataset2.iloc[i,2] = message_text

dataset2.to_csv('250_5000.csv', encoding='utf-8', index=False)

# for i in range(dataset.shape[0]):
#     if(pd.notna(dataset.iloc[i,0])):
#         num = dataset.iloc[i,0]
#         # print("start")
#         # print(num)
#         label = num
#         # label = int(label)
#         if(label <= 5):
#             label = 50
#         elif(label <= 100):
#             label = 100
#         elif(label <= 150):
#             label = 150  
#         elif(label <= 200):
#             label = 200  
#         elif(label <= 250):
#             label = 250
#         else:
#             label = 9999           
#         dataset.iloc[i,1] = label

# dataset.to_csv('250_5000.csv', encoding='utf-8', index=False)

# dataset = pd.read_csv('savefile.csv',encoding='ISO-8859-1')

# x = dataset['message']

# y = dataset['likes']

# from sklearn.feature_extraction.text import CountVectorizer
# from sklearn.feature_extraction.text import TfidfVectorizer

# from sklearn import linear_model
# from sklearn.ensemble import RandomForestRegressor
# from sklearn.ensemble import GradientBoostingRegressor

# from sklearn.model_selection import train_test_split
# from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
# import numpy as np

# from sklearn_pandas import DataFrameMapper

# cmatrix = CountVectorizer()
# tfidfmatrix = TfidfVectorizer()

# cvocab = cmatrix.fit_transform(x)
# tfidfvocab = tfidfmatrix.fit_transform(x)

# mapper = DataFrameMapper([
#     ('message',CountVectorizer()),
#     ('numWord',None),
# ])

# cvocab = mapper.fit_transform(dataset)

# mapper = DataFrameMapper([
#     ('message',TfidfVectorizer()),
#     ('numWord',None),
# ])

# tfidfvocab = mapper.fit_transform(dataset)

# cx_train, cx_test, cy_train, cy_test = train_test_split(cvocab,y,test_size=0.3,train_size=0.7,random_state=0)
# tfidf_x_train, tfidf_x_test, tfidf_y_train, tfidf_y_test = train_test_split(tfidfvocab,y,test_size=0.3,train_size=0.7,random_state=0)

# cregr = linear_model.LinearRegression()
# cregr.fit(cx_train, cy_train)
# print(cregr.score(cx_test,cy_test))
# cy_pred = cregr.predict(cx_test)

# mse = mean_squared_error(cy_pred, cy_test)
# print("CountVec Linear MSE: %.10f" % mse)
# rmse = np.sqrt(mse)
# print('CountVec Linear RMSE: %.10f' % rmse)
# c_mae = mean_absolute_error(cy_pred, cy_test)
# print('CountVec Linear MAE: %.10f' % c_mae)

# # from sklearn.externals import joblib
# # joblib.dump(cregr,'test.joblib')

# tfidfregr = linear_model.LinearRegression()
# tfidfregr.fit(tfidf_x_train, tfidf_y_train)
# tfidf_y_pred = tfidfregr.predict(tfidf_x_test)

# mse = mean_squared_error(tfidf_y_pred, tfidf_y_test)
# print("tfidf Linear MSE: %.2f" % mse)
# rmse = np.sqrt(mse)
# print('tfidf Linear RMSE: %.4f' % rmse)
# tfidf_mae = mean_absolute_error(tfidf_y_pred, tfidf_y_test)
# print('tfidf Linear MAE: %.4f' % tfidf_mae)

# c_forest = RandomForestRegressor(random_state=42)
# c_forest.fit(cx_train, cy_train)
# cy_pred = c_forest.predict(cx_test)
# mse = mean_squared_error(cy_pred, cy_test)
# print("CountVec Forest MSE: %.2f" % mse)
# rmse = np.sqrt(mse)
# print('CountVec Forest RMSE: %.4f' % rmse)
# c_mae = mean_absolute_error(cy_pred, cy_test)
# print('CountVec Forest MAE: %.4f' % c_mae)

# tfidfforest = RandomForestRegressor(random_state=42)
# tfidfforest.fit(tfidf_x_train, tfidf_y_train)
# tfidf_y_pred = tfidfforest.predict(tfidf_x_test)
# mse = mean_squared_error(tfidf_y_pred, tfidf_y_test)
# print("tfidf Forest MSE: %.2f" % mse)
# rmse = np.sqrt(mse)
# print('tfidf Forest RMSE: %.4f' % rmse)
# tfidf_mae = mean_absolute_error(tfidf_y_pred, tfidf_y_test)
# print('tfidf Forest MAE: %.4f' % tfidf_mae)
