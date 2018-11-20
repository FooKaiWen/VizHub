
import pandas as pd
import re
import nltk
import textblob
from nltk.tokenize import word_tokenize  # nltk.download('punkt') #nltk.download('averaged_perceptron_tagger')
from nltk.corpus import wordnet as wn # nltk.download('wordnet')

from sklearn import model_selection, preprocessing, linear_model, naive_bayes, metrics, svm
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer
from sklearn import decomposition, ensemble




dataset = pd.read_csv('test.csv',encoding='ISO-8859-1')

df = pd.DataFrame()
feature = pd.DataFrame()


pos_family = {
    'noun' : ['NN','NNS','NNP','NNPS'],
    'pron' : ['PRP','PRP$','WP','WP$'],
    'verb' : ['VB','VBD','VBG','VBN','VBP','VBZ'],
    'adj' :  ['JJ','JJR','JJS'],
    'adv' : ['RB','RBR','RBS','WRB']
}

# function to check and get the part of speech tag count of a words in a given sentence
def check_pos_tag(x, flag):
    cnt = 0
    try:
        wiki = textblob.TextBlob(x)
        for tup in wiki.tags:
            ppo = list(tup)[1]
            if ppo in pos_family[flag]:
                cnt += 1
    except:
        pass
    return cnt




df['likes'] = dataset['likes']
df['message'] = dataset['message']
df['char_count'] = df['message'].apply(len)
df['word_count'] = df['message'].apply(lambda x: len(x.split()))


feature['word_density'] = df['char_count'] / (df['word_count']+1)
feature['noun_count'] = df['message'] .apply(lambda x: check_pos_tag(x, 'noun'))
feature['verb_count'] = df['message'].apply(lambda x: check_pos_tag(x, 'verb'))
feature['adj_count'] = df['message'].apply(lambda x: check_pos_tag(x, 'adj'))
feature['adv_count'] = df['message'].apply(lambda x: check_pos_tag(x, 'adv'))
feature['pron_count'] = df['message'].apply(lambda x: check_pos_tag(x, 'pron'))

tfidf_vect = TfidfVectorizer(max_features = 1000)

feature1 = pd.DataFrame()
# feature1['text'] = tfidf_vect.fit_transform(df['message']) 
feature1['noun_count'] = feature['noun_count']
feature1['word_density'] = feature['word_density']

print (df.head(20))


print (feature.head(20))

# from sklearn.feature_extraction import DictVectorizer
# vec = DictVectorizer()

# dataa = vec.fit_transform(dataset['word_density'] ).toarray()

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.model_selection import train_test_split
from sklearn import linear_model # Linear regression is used
from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
import matplotlib.pyplot as plt
import numpy as np
import scipy as sp

# from sklearn.model_selection import train_test_split

# # split the dataset into training and validation datasets 
# x_train, x_test, y_train, y_test = model_selection.train_test_split(dataa , dataset['likes'],test_size=0.3,train_size=0.7,random_state=0)


# # from sklearn.feature_extraction.text import TfidfVectorizer
# # from sklearn.feature_extraction.text import CountVectorizer

# # # create a count vectorizer object 
# # count_vect = CountVectorizer(analyzer='word', token_pattern=r'\w{1,}')
# # count_vect.fit(dataset['text'])

# # # transform the training and validation data using count vectorizer object
# # xtrain_count =  count_vect.transform(train_x)
# # xvalid_count =  count_vect.transform(valid_x)


y = dataset['likes']
x_train, x_test, y_train, y_test = train_test_split(feature,y,test_size=0.3,train_size=0.7,random_state=0)

print("Feature")
print("----------------")
from sklearn.linear_model import Ridge

clf = Ridge(alpha=1.0, random_state=241)
clf.fit(x_train,y_train)

y_pred = clf.predict(x_test)

# # The coefficients
# print('x Coefficients: \n', regr.coef_)
# The mean squared error
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x Ridge RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")


from sklearn.ensemble import RandomForestRegressor # Random Forest Regressor is used

forest_reg = RandomForestRegressor(random_state=42)
forest_reg.fit(x_train, y_train)
likes_pred = forest_reg.predict(x_test)
forest_mse = mean_squared_error(likes_pred, y_test)
forest_rmse = np.sqrt(forest_mse)
print('Random Forest RMSE: %.4f' % forest_rmse)
lin_mae = mean_absolute_error(likes_pred, y_test)
print('x MAE: %.4f' % lin_mae)
print('x variance score: %.2f' % r2_score(y_test, likes_pred))
print()
print("==================================================================")


from sklearn.linear_model import SGDRegressor

sgd = SGDRegressor()
sgd.fit(x_train, y_train)
likes_pred = sgd.predict(x_test)
forest_mse = mean_squared_error(likes_pred, y_test)
forest_rmse = np.sqrt(forest_mse)
print('SGD  RMSE: %.4f' % forest_rmse)
lin_mae = mean_absolute_error(likes_pred, y_test)
print('x MAE: %.4f' % lin_mae)
print('x variance score: %.2f' % r2_score(y_test, likes_pred))
print()
print("==================================================================")



from sklearn.linear_model import LogisticRegression

lgr = LogisticRegression(random_state=0, solver='lbfgs', multi_class='multinomial').fit(x_train, y_train)
likes_pred = lgr.predict(x_test)
forest_mse = mean_squared_error(likes_pred, y_test)
forest_rmse = np.sqrt(forest_mse)
print('Logistic RMSE: %.4f' % forest_rmse)
lin_mae = mean_absolute_error(likes_pred, y_test)
print('x MAE: %.4f' % lin_mae)
print('x variance score: %.2f' % r2_score(y_test, likes_pred))
print()
print("==================================================================")

x_train, x_test, y_train, y_test = train_test_split(feature1,y,test_size=0.3,train_size=0.7,random_state=0)

print("Feature 1")
print("----------------")
from sklearn.linear_model import Ridge

clf = Ridge(alpha=1.0, random_state=241)
clf.fit(x_train,y_train)

y_pred = clf.predict(x_test)

# # The coefficients
# print('x Coefficients: \n', regr.coef_)
# The mean squared error
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x Ridge RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")


from sklearn.ensemble import RandomForestRegressor # Random Forest Regressor is used

forest_reg = RandomForestRegressor(random_state=42)
forest_reg.fit(x_train, y_train)
likes_pred = forest_reg.predict(x_test)
forest_mse = mean_squared_error(likes_pred, y_test)
forest_rmse = np.sqrt(forest_mse)
print('Random Forest RMSE: %.4f' % forest_rmse)
lin_mae = mean_absolute_error(likes_pred, y_test)
print('x MAE: %.4f' % lin_mae)
print('x variance score: %.2f' % r2_score(y_test, likes_pred))
print()
print("==================================================================")


from sklearn.linear_model import SGDRegressor

sgd = SGDRegressor()
sgd.fit(x_train, y_train)
likes_pred = sgd.predict(x_test)
forest_mse = mean_squared_error(likes_pred, y_test)
forest_rmse = np.sqrt(forest_mse)
print('SGD  RMSE: %.4f' % forest_rmse)
lin_mae = mean_absolute_error(likes_pred, y_test)
print('x MAE: %.4f' % lin_mae)
print('x variance score: %.2f' % r2_score(y_test, likes_pred))
print()
print("==================================================================")



from sklearn.linear_model import LogisticRegression

lgr = LogisticRegression(random_state=0, solver='lbfgs', multi_class='multinomial').fit(x_train, y_train)
likes_pred = lgr.predict(x_test)
forest_mse = mean_squared_error(likes_pred, y_test)
forest_rmse = np.sqrt(forest_mse)
print('Logistic RMSE: %.4f' % forest_rmse)
lin_mae = mean_absolute_error(likes_pred, y_test)
print('x MAE: %.4f' % lin_mae)
print('x variance score: %.2f' % r2_score(y_test, likes_pred))
print()
print("==================================================================")
