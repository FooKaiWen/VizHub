from sklearn.externals import joblib
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer
import pandas
from sklearn import model_selection, metrics

dataset = pandas.read_csv('250_5000.csv',encoding='ISO-8859-1')
oriset = pandas.read_csv('250_5000.csv',encoding='ISO-8859-1')

labels = dataset['likes']
datacolumn = dataset['orimessage']

train_x, valid_x, train_y, valid_y = model_selection.train_test_split(datacolumn, labels, random_state=42)
# print(valid_x)
valid_x = ["If Sen. John Kerry is nominated as Secretary of State, then out-going Sen. Scott Brown is widely expected to seek out his old job."]

loaded_vect = TfidfVectorizer(decode_error="ignore",vocabulary=joblib.load('feature.joblib'))
loadedmodel = joblib.load('model.joblib')
xtrain_tfidf =  loaded_vect.fit_transform(train_x)
xvalid_tfidf =  loaded_vect.fit_transform(valid_x)

def train_model(classifier, feature_vector_train, label, feature_vector_valid):
    # fit the training dataset on the classifier

    # predict the labels on validation dataset
    predictions = classifier.predict(feature_vector_valid)
    return predictions
#     return metrics.classification_report(valid_y, predictions)

# Linear Classifier on Word Level TF IDF Vectors
accuracy = train_model(loadedmodel, xtrain_tfidf, train_y, xvalid_tfidf)
print("LR, WordLevel TF-IDF: ", accuracy)

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
#     for suffix in ['ing', 'ly', 'ed', 'ious', 'ies', 'ive', 'es', 's', 'ment']:
#         if word.endswith(suffix):
#             return word[:-len(suffix)]
#     return word

# for i in range(dataset.shape[0]): # This is where messages are cleaned and stemmed to make them uniform.
#     if(pd.notna(dataset.iloc[i,2])):
#         message = dataset.iloc[i,2]
#         #remove non alphabetic characters
#         message = re.sub('[^A-Za-z]',' ',message)
#         #make words lowercase
#         message = message.lower()
#         tokenized_message = wt(message)
#         message_processed = []

#         for word in tokenized_message:
#             stem(word)
#             if word not in set(stopwords.words('english')):
#                 message_processed.append(spell(stemmer.stem(word)))
        
#         message_text = " ".join(message_processed)
#         data.append(message_text)
    
# from sklearn.feature_extraction.text import CountVectorizer
# from sklearn.model_selection import train_test_split
# from sklearn import linear_model # Linear regression is used
# from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
# import numpy as np

# matrix = CountVectorizer()
# matrix.fit(data)
# countVec = matrix.transform(data)
# y = dataset['likes']

# x_train, x_test, y_train, y_test = train_test_split(countVec,y)

# regr = linear_model.LinearRegression()
# regr.fit(x_train, y_train)
# y_pred = regr.predict(x_test)

# # The coefficients
# print('countVec Coefficients: \n', regr.coef_)
# # The mean squared error
# mse = mean_squared_error(y_pred, y_test)
# print("countVec MSE: %.2f" % mse)
# rmse = np.sqrt(mse)
# print('countVec RMSE: %.4f' % rmse)
# lin_mae = mean_absolute_error(y_pred, y_test)
# print('countVec MAE: %.4f' % lin_mae)
# # Explained variance score: 1 is perfect prediction
# print('countVec variance score: %.2f' % r2_score(y_test, y_pred))
# print()
# print("==================================================================")
# from sklearn.feature_extraction.text import TfidfVectorizer #This method counts how many times a word is used.

# tfidf = TfidfVectorizer()
# tfidfVec = tfidf.fit_transform(data).toarray()

# tfidfVec_train, tfidfVec_test, tfidfVec_train1, tfidfVec_test1 = train_test_split(tfidfVec,y)

# regr = linear_model.LinearRegression()
# regr.fit(tfidfVec_train, tfidfVec_train1)
# tfidfVec_pred = regr.predict(tfidfVec_test)

# # The coefficients
# print('tfidfVec Coefficients: \n', regr.coef_)
# # The mean squared error
# mse = mean_squared_error(tfidfVec_pred, tfidfVec_test1)
# print("tfidfVec MSE: %.2f" % mse)
# rmse = np.sqrt(mse)
# print('tfidfVec RMSE: %.4f' % rmse)
# lin_mae = mean_absolute_error(tfidfVec_pred, tfidfVec_test1)
# print('tfidfVec MAE: %.4f' % lin_mae)
# # Explained variance score: 1 is perfect prediction
# print('tfidfVec variance score: %.2f' % r2_score(tfidfVec_test1, tfidfVec_pred))
# print()
# print("==================================================================")


# tfidf2 = TfidfVectorizer()
# tfidfVec2 = tfidf2.fit_transform(data).toarray()

# from sklearn.decomposition import TruncatedSVD
# tsvd = TruncatedSVD(n_components=1998)
# sparse_tsvd = tsvd.fit(tfidfVec2).transform(tfidfVec2)
# # print('Reduced number of features:', sparse_tsvd.shape[1], sparse_tsvd.shape[0])

# tsvd_tfidf_train, tsvd_tfidf_test, tsvd_tfidf_train1, tsvd_tfidf_test1 = train_test_split(sparse_tsvd,y)

# regr = linear_model.LinearRegression()
# regr.fit(tsvd_tfidf_train, tsvd_tfidf_train1)
# tsvd_tfidf_pred = regr.predict(tsvd_tfidf_test)

# # The coefficients
# print('tsvd_tfidf Coefficients: \n', regr.coef_)
# # The mean squared error
# mse = mean_squared_error(tsvd_tfidf_pred, tsvd_tfidf_test1)
# print("tsvd_tfidf MSE: %.2f" % mse)
# rmse = np.sqrt(mse)
# print('tsvd_tfidf RMSE: %.4f' % rmse)
# lin_mae = mean_absolute_error(tsvd_tfidf_pred, tsvd_tfidf_test1)
# print('tsvd_tfidf MAE: %.4f' % lin_mae)
# # Explained variance score: 1 is perfect prediction
# print('tsvd_tfidf variance score: %.2f' % r2_score(tsvd_tfidf_test1, tsvd_tfidf_pred))
# print()
# print("==================================================================")


# #This method balance out the number of times of used words with others
# from sklearn.feature_extraction.text import TfidfTransformer
# matrix2 = CountVectorizer()
# matrix2.fit(data)
# countVec2 = matrix2.transform(data)

# tfidf_transformer = TfidfTransformer().fit(countVec2)

# tfidf_countVec2 = tfidf_transformer.transform(countVec2).toarray()

# # print (tfidf_countVec2)
# # print (tfidf_countVec2.shape)

# likes = dataset['likes']

# tfidf_countVec2_train, tfidf_countVec2_test, likes_train, likes_test = \
# train_test_split(tfidf_countVec2, likes, test_size=0.3)

# # print (len(tfidf_text_train), len(tfidf_text_test), len(tfidf_text_train) + len(tfidf_text_test))

# regr = linear_model.LinearRegression()
# regr.fit(tfidf_countVec2_train, likes_train)
# likes_pred = regr.predict(tfidf_countVec2_test)

# # The coefficients
# print('tfidf_countVec2 Coefficients: \n', regr.coef_)
# # The mean squared error
# tfidf_mse = mean_squared_error(likes_pred, likes_test)
# print("tfidf_countVec2 MSE: %.2f" % tfidf_mse)
# tfidf_rmse = np.sqrt(tfidf_mse)
# print('tfidf_countVec2 RMSE: %.4f' % tfidf_rmse)
# lin_mae = mean_absolute_error(likes_pred, likes_test)
# print('tfidf_countVec2 MAE: %.4f' % lin_mae)
# # Explained variance score: 1 is perfect prediction
# print('tfidf_countVec2 variance score: %.2f' % r2_score(likes_test, likes_pred))
# print()
# print("==================================================================")


# from sklearn.ensemble import RandomForestRegressor # Random Forest Regressor is used

# forest_reg = RandomForestRegressor(random_state=42)
# forest_reg.fit(x_train, y_train)
# likes_pred = forest_reg.predict(x_test)
# forest_mse = mean_squared_error(y_pred, y_test)
# forest_rmse = np.sqrt(forest_mse)
# print('countVec Random Forest RMSE: %.4f' % forest_rmse)

# from sklearn import ensemble
# from sklearn.ensemble import GradientBoostingRegressor # Gradient Boosting Regressor is used
# model = ensemble.GradientBoostingRegressor()

# model.fit(x_train, y_train)
# likes_pred = model.predict(x_test)
# gb_mse = mean_squared_error(y_pred, y_test)
# gb_rmse = np.sqrt(gb_mse)
# print('countVec Gradient Boosting RMSE: %.4f' % gb_rmse)
# print()
# print("==================================================================")

# forest_reg = RandomForestRegressor(random_state=42)
# forest_reg.fit(tfidf_countVec2_train, likes_train)
# likes_pred = forest_reg.predict(tfidf_countVec2_test)
# forest_mse = mean_squared_error(likes_pred, likes_test)
# forest_rmse = np.sqrt(forest_mse)
# print('tfidf_countVec2 Random Forest RMSE: %.4f' % forest_rmse)

# model = ensemble.GradientBoostingRegressor()
# model.fit(tfidf_countVec2_train, likes_train)
# likes_pred = model.predict(tfidf_countVec2_test)
# gb_mse = mean_squared_error(likes_pred, likes_test)
# gb_rmse = np.sqrt(gb_mse)
# print('tfidf_countVec2 Gradient Boosting RMSE: %.4f' % gb_rmse)