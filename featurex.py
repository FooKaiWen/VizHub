import pandas as pd
import re
import nltk
from nltk.tokenize import word_tokenize as wt # nltk.download('punkt')
from nltk.corpus import stopwords # nltk.download('stopwords')
from nltk.stem.porter import PorterStemmer
from autocorrect import spell #spell correction

dataset = pd.read_csv('test.csv',encoding='ISO-8859-1')

stemmer = PorterStemmer()

data = []

def stem(word):
    for suffix in ['ing', 'ly', 'ed', 'ious', 'ies', 'ive', 'es', 's', 'ment']:
        if word.endswith(suffix):
            return word[:-len(suffix)]
    return word

for i in range(dataset.shape[0]): # This is where messages are cleaned and stemmed to make them uniform.
    if(pd.notna(dataset.iloc[i,2])):
        message = dataset.iloc[i,2]
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
    
from sklearn.feature_extraction.text import CountVectorizer    
from sklearn.feature_extraction.text import TfidfVectorizer #This method counts how many times a word is used.
from sklearn.model_selection import train_test_split
from sklearn import linear_model # Linear regression is used
from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
import numpy as np

matrix = TfidfVectorizer()
x = matrix.fit_transform(data).toarray()
from sklearn.decomposition import TruncatedSVD
tsvd = TruncatedSVD(n_components=1998)
sparse_tsvd = tsvd.fit(x).transform(x)
print('Reduced number of features:', sparse_tsvd.shape[1], sparse_tsvd.shape[0])
y = dataset['likes']
    
x_train, x_test, y_train, y_test = train_test_split(sparse_tsvd,y)

regr = linear_model.LinearRegression()
regr.fit(x_train, y_train)
y_pred = regr.predict(x_test)
print(x_test)
# The coefficients
print('Coefficients: \n', regr.coef_)

# The mean squared error
mse = mean_squared_error(y_pred, y_test)
print("MSE Mean squared error: %.2f" % mse)

rmse = np.sqrt(mse)
print('Linear RMSE: %.4f' % rmse)

lin_mae = mean_absolute_error(y_pred, y_test)
print('Linear Regression MAE: %.4f' % lin_mae)

# Explained variance score: 1 is perfect prediction
print('Variance score: %.2f' % r2_score(y_test, y_pred))

import matplotlib.pyplot as plt
# # Plot outputs
# plt.scatter(x_test, y_test,  color='black')
# plt.plot(y_test, y_pred, color='blue', linewidth=1)
# plt.plot(y_test, y_test, color='black', linewidth=1)
# plt.xticks(())
# plt.yticks(())
# plt.show()

matrix2 = CountVectorizer()
matrix2.fit(data)
x = matrix2.transform(data)

# print ('Shape of Sparse Matrix: ', x.shape)
# print ('Amount of Non-Zero occurences: ', x.nnz)
# print ('Sparsity: %.2f%%' % (100.0 * x.nnz / (x.shape[0] * x.shape[1])))

#This method balance out the number of times of used words with others
from sklearn.feature_extraction.text import TfidfTransformer
tfidf_transformer = TfidfTransformer().fit(x)

tfidf_text = tfidf_transformer.transform(x).toarray()

print (tfidf_text)
print (tfidf_text.shape)

likes = dataset['likes']

tfidf_text_train, tfidf_text_test, likes_train, likes_test = \
train_test_split(tfidf_text, likes, test_size=0.3)

# print (len(tfidf_text_train), len(tfidf_text_test), len(tfidf_text_train) + len(tfidf_text_test))

regr = linear_model.LinearRegression()
regr.fit(tfidf_text_train, likes_train)
likes_pred = regr.predict(tfidf_text_test)

# # The coefficients
# print('Coefficients: \n', regr.coef_)

# # The mean squared error
tfidf_mse = mean_squared_error(likes_pred, likes_test)
# print("MSE Mean squared error: %.2f" % tfidf_mse)

tfidf_rmse = np.sqrt(tfidf_mse)
print('TFIDF RMSE: %.4f' % tfidf_rmse)

# lin_mae = mean_absolute_error(likes_pred, likes_test)
# print('Liner Regression MAE: %.4f' % lin_mae)

# # Explained variance score: 1 is perfect prediction
# print('Variance score: %.2f' % r2_score(likes_test, likes_pred))

# # import matplotlib.pyplot as plt2
# # Plot outputs
# #plt.scatter(x_test, y_test,  color='black')
# # plt2.plot(tfidf_text_test, likes_pred, color='blue', linewidth=1)
# # plt2.plot(tfidf_text_test, likes_test, color='black', linewidth=1)

# # plt2.xticks(())
# # plt2.yticks(())

# # plt2.show()

from sklearn.ensemble import RandomForestRegressor # Random Forest Regressor is used

# forest_reg = RandomForestRegressor(random_state=42)
# forest_reg.fit(x_train, y_train)
# likes_pred = forest_reg.predict(x_test)
# forest_mse = mean_squared_error(y_pred, y_test)
# forest_rmse = np.sqrt(forest_mse)
# print('Random Forest RMSE: %.4f' % forest_rmse)

# from sklearn import ensemble
# from sklearn.ensemble import GradientBoostingRegressor # Gradient Boosting Regressor is used
# model = ensemble.GradientBoostingRegressor()
# model.fit(x_train, y_train)
# likes_pred = model.predict(x_test)
# gb_mse = mean_squared_error(y_pred, y_test)
# gb_rmse = np.sqrt(gb_mse)
# print('Gradient Boosting RMSE: %.4f' % gb_rmse)



forest_reg = RandomForestRegressor(random_state=42)
forest_reg.fit(tfidf_text_train, likes_train)
likes_pred = forest_reg.predict(tfidf_text_test)
forest_mse = mean_squared_error(likes_pred, likes_test)
forest_rmse = np.sqrt(forest_mse)
print('Random Forest RMSE: %.4f' % forest_rmse)

plt.plot(likes_test, likes_pred, color='black', linewidth=1)
plt.plot(likes_test,likes_test,color='blue',linewidth=1)
plt.xticks(())
plt.yticks(())
plt.show()

# model = ensemble.GradientBoostingRegressor()
# model.fit(tfidf_text_train, likes_train)
# likes_pred = model.predict(tfidf_text_test)
# gb_mse = mean_squared_error(likes_pred, likes_test)
# gb_rmse = np.sqrt(gb_mse)
# print('Gradient Boosting RMSE: %.4f' % gb_rmse)