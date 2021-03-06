import pandas as pd
import re
import nltk
from nltk.tokenize import word_tokenize  # nltk.download('punkt') #nltk.download('averaged_perceptron_tagger')
from nltk.corpus import wordnet as wn # nltk.download('wordnet')

<<<<<<< HEAD
dataset = pd.read_csv('250_5000.csv',encoding='ISO-8859-1')
=======
dataset = pd.read_csv('.csv',encoding='ISO-8859-1')
>>>>>>> 34c43f3e104a382171c88869249d7eef823dbe85

is_noun = lambda pos: pos[:2] == 'NN'


data =[]
noun_count = []

for i in range(dataset.shape[0]): # This is where messages are cleaned and stemmed to make them uniform.
    if(pd.notna(dataset.iloc[i,1])):
        message = dataset.iloc[i,1]
        tokenized = nltk.word_tokenize(message.lower())
        nouns = [word for (word, pos) in nltk.pos_tag(tokenized) if is_noun(pos)] 
        noun_count.append(len(nouns))
        general =[]
        for noun in nouns:
            general.append(noun)
            # #look up the noun in wordnet dictionary
            # found = wn.synsets(noun) 
            # if found :     
            #     asd = found[0]
            #     hype = asd.hypernyms()               
            #     if hype:
                    # general.append(hype[0].lemma_names()[0])
                  
        message_text = ' '.join(general) 
        data.append (message_text)                  
 
# print (noun_count) 
# print (data)        

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.model_selection import train_test_split
from sklearn import linear_model # Linear regression is used
from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
import matplotlib.pyplot as plt
import numpy as np
import scipy as sp

y = dataset['likes']

count_vect = CountVectorizer(max_features = 1000, min_df = 3, max_df = 10)
tfidf_vect = TfidfVectorizer(max_features = 1000, min_df = 3, max_df = 10)

count = count_vect.fit_transform(data) 
tfidf = tfidf_vect.fit_transform(data) 

print (count.shape)
print (tfidf.shape)

# ww_count = count_vect.fit(noun_count)
# w_count = count_vect.transform(ww_count) 

# Take a sequence of arrays and stack them horizontally to make a single array. 
# Rebuild arrays divided by scipy.sparse.hstack. 
# Note that matrices are sparse. 
# Icd n numerical analysis, a sparse matrix is a matrix in which most of the elements are zero. 

# x = sp.sparse.hstack([tfidf,w_count])
from sklearn.linear_model import Ridge
from sklearn.linear_model import RidgeCV
from sklearn.compose import TransformedTargetRegressor
from sklearn.preprocessing import QuantileTransformer, quantile_transform

from sklearn import svm
x_train, x_test, y_train, y_test = train_test_split(count,y,test_size=0.3,train_size=0.7,random_state=0)






from sklearn import linear_model


print("COUNT_VECTORIZER")
print("----------------")




from sklearn.kernel_ridge import KernelRidge

from sklearn.ensemble import GradientBoostingRegressor # Gradient Boosting Regressor is used

model = TransformedTargetRegressor(regressor=GradientBoostingRegressor(),transformer=QuantileTransformer(output_distribution='normal'))
model.fit(x_train,y_train) 
y_pred = model.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x Gradient RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")
print(model.predict(count_vect.transform(["could connect"])))


clf = TransformedTargetRegressor(regressor=KernelRidge(alpha=1.0),transformer=QuantileTransformer(output_distribution='normal'))
clf.fit(x_train,y_train) 
y_pred = clf.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x Kernel RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")


# clf = svm.SVR()
clf = TransformedTargetRegressor(regressor=svm.SVR(),transformer=QuantileTransformer(output_distribution='normal'))
clf.fit(x_train,y_train) 
y_pred = clf.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x SVR RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")

# reg = linear_model.Lasso(alpha = 1.0)
reg = TransformedTargetRegressor(regressor=linear_model.Lasso(alpha = 1.0),transformer=QuantileTransformer(output_distribution='normal'))
reg.fit(x_train, y_train)

y_pred = reg.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x lasso RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")



# clf = RidgeCV(alphas=1.0)
clf =TransformedTargetRegressor(regressor=Ridge(alpha=1.0, random_state=241),transformer=QuantileTransformer(output_distribution='normal'))
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

# forest_reg = RandomForestRegressor(random_state=42)
forest_reg = TransformedTargetRegressor(regressor=RandomForestRegressor(random_state=42),transformer=QuantileTransformer(output_distribution='normal'))
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

# sgd = SGDRegressor()
sgd = TransformedTargetRegressor(regressor=SGDRegressor(),transformer=QuantileTransformer(output_distribution='normal'))
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
print(sgd.predict(count_vect.transform(["abc news full story cover Cheney weekend transplant"])))


# from sklearn.linear_model import LogisticRegression

# # lgr = LogisticRegression(random_state=0, solver='lbfgs', multi_class='multinomial').fit(x_train, y_train)
# lgr = TransformedTargetRegressor(regressor=LogisticRegression(random_state=0, solver='lbfgs', multi_class='multinomial'),transformer=QuantileTransformer(output_distribution='normal'))
# lgr.fit(x_train, y_train)
# likes_pred = lgr.predict(x_test)
# forest_mse = mean_squared_error(likes_pred, y_test)
# forest_rmse = np.sqrt(forest_mse)
# print('Logistic RMSE: %.4f' % forest_rmse)
# lin_mae = mean_absolute_error(likes_pred, y_test)
# print('x MAE: %.4f' % lin_mae)
# print('x variance score: %.2f' % r2_score(y_test, likes_pred))
# print()
# print("==================================================================")

x_train, x_test, y_train, y_test = train_test_split(tfidf,y,test_size=0.3,train_size=0.7,random_state=0)

print("TFIDF_VECTORIZER")
print("----------------")
from sklearn.linear_model import Ridge

clf =TransformedTargetRegressor(regressor=Ridge(alpha=1.0, random_state=241),transformer=QuantileTransformer(output_distribution='normal'))
# clf = Ridge(alpha=1.0, random_state=241)
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

# forest_reg = RandomForestRegressor(random_state=42)
forest_reg = TransformedTargetRegressor(regressor=RandomForestRegressor(random_state=42),transformer=QuantileTransformer(output_distribution='normal'))
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

# sgd = SGDRegressor()
sgd = TransformedTargetRegressor(regressor=SGDRegressor(),transformer=QuantileTransformer(output_distribution='normal'))
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

reg = linear_model.Lasso(alpha = 1.0)
reg.fit(x_train, y_train)

y_pred = reg.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x lasso RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")

# clf = svm.SVR()
clf = TransformedTargetRegressor(regressor=svm.SVR(),transformer=QuantileTransformer(output_distribution='normal'))
clf.fit(x_train,y_train) 
y_pred = clf.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x SVR RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")


clf = TransformedTargetRegressor(regressor=KernelRidge(alpha=1.0),transformer=QuantileTransformer(output_distribution='normal'))
clf.fit(x_train,y_train) 
y_pred = clf.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x Kernel RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")

model = TransformedTargetRegressor(regressor=GradientBoostingRegressor(),transformer=QuantileTransformer(output_distribution='normal'))

model.fit(x_train,y_train) 
y_pred = model.predict(x_test)
mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
rmse = np.sqrt(mse)
print('x Gradient RMSE: %.4f' % rmse)
lin_mae = mean_absolute_error(y_pred, y_test)
print('x MAE: %.4f' % lin_mae)
# Explained variance score: 1 is perfect prediction
print('x variance score: %.2f' % r2_score(y_test, y_pred))
print()
print("==================================================================")


# from sklearn.linear_model import LogisticRegression

# # lgr = LogisticRegression(random_state=0, solver='lbfgs', multi_class='multinomial').fit(x_train, y_train)
# lgr = TransformedTargetRegressor(regressor=LogisticRegression(random_state=0, solver='lbfgs', multi_class='multinomial'),transformer=QuantileTransformer(output_distribution='normal'))
# lgr.fit(x_train, y_train)
# likes_pred = lgr.predict(x_test)
# forest_mse = mean_squared_error(likes_pred, y_test)
# forest_rmse = np.sqrt(forest_mse)
# print('Logistic RMSE: %.4f' % forest_rmse)
# lin_mae = mean_absolute_error(likes_pred, y_test)
# print('x MAE: %.4f' % lin_mae)
# print('x variance score: %.2f' % r2_score(y_test, likes_pred))
# print()
# print("==================================================================")


# Plot outputs
# plt.scatter(x_test, y_test,  color='black')
# plt.plot(x_test, likes_pred, color='blue', linewidth=3)

# plt.xticks(())
# plt.yticks(())

# plt.show()

# regr = linear_model.LinearRegression()
# regr.fit(x_train, y_train)
# y_pred = regr.predict(x_test)

# # # The coefficients
# # print('x Coefficients: \n', regr.coef_)
# # The mean squared error
# mse = mean_squared_error(y_pred, y_test)
# print("x MSE: %.2f" % mse)
# rmse = np.sqrt(mse)
# print('x RMSE: %.4f' % rmse)
# lin_mae = mean_absolute_error(y_pred, y_test)
# print('x MAE: %.4f' % lin_mae)
# # Explained variance score: 1 is perfect prediction
# print('x variance score: %.2f' % r2_score(y_test, y_pred))
# print()
# print("==================================================================")

# print(regr.predict(vectorizer.transform(["An inside look at how Tom Hanks transformed into Walt Disney:"])))
