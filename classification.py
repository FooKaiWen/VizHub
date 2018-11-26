import pandas as pd
oriset = pd.read_csv('250_5000.csv',encoding='ISO-8859-1')
dataset = pd.read_csv('250_5000.csv',encoding='ISO-8859-1')
# for i in range(oriset.shape[0]):
#     if(pd.notna(oriset.iloc[i,0])):
#         num = oriset.iloc[i,0]
#         # print("start")
#         # print(num)
#         label = num
#         # label = int(label)
#         if(label < 2500):
#             label = 2500
#         elif(label < 5000):
#             label = 5000
#         elif(label < 7500):
#             label = 7500  
#         # elif(label < 8000):
#         #     label = 8000  
#         elif(label < 10000):
#             label = 10000
#         else:
#             label = 9999           
#         dataset.iloc[i,1] = label

# dataset.to_csv('dataset.csv', encoding='utf-8', index=False)

from sklearn import model_selection, preprocessing, linear_model, naive_bayes, metrics, svm
from sklearn.model_selection import cross_val_score
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer
from sklearn import decomposition, ensemble
import pandas, numpy, textblob, string
import numpy as np
import warnings
warnings.filterwarnings("ignore", category=FutureWarning)
from sklearn.externals import joblib

def train_model(classifier, feature_vector_train, label, feature_vector_valid):
    # fit the training dataset on the classifier
    classifier.fit(feature_vector_train, label)

    joblib.dump(classifier,'model.joblib')

    # predict the labels on validation dataset
    predictions = classifier.predict(feature_vector_valid)
    # return predictions
    return metrics.classification_report(valid_y, predictions)

dataset = pandas.read_csv('250_5000.csv',encoding='ISO-8859-1')
oriset = pandas.read_csv('250_5000.csv',encoding='ISO-8859-1')

labels = dataset['likes']
datacolumn = dataset['orimessage']

train_x, valid_x, train_y, valid_y = model_selection.train_test_split(datacolumn, labels, random_state=42)

count_vect = CountVectorizer(analyzer='word', token_pattern=r'\w{1,}',max_features=1000)
count_vect.fit(datacolumn)

# transform the training and validation data using count vectorizer object
xtrain_count =  count_vect.transform(train_x)
xvalid_count =  count_vect.transform(valid_x)

# import numpy as np
# test = ['new chief staff']
# test_count = count_vect.transform(test)

nb = naive_bayes.MultinomialNB() #suitable for word count.
lr = linear_model.LogisticRegression(random_state=42, solver='lbfgs',max_iter=300)
rf = ensemble.RandomForestClassifier(random_state=42)
svc = svm.SVC(kernel='linear',random_state=42)
gb = ensemble.GradientBoostingClassifier(random_state=42)
ab = ensemble.AdaBoostClassifier(random_state=42)

# Naive Bayes on Count Vectors
accuracy = train_model(nb, xtrain_count, train_y, xvalid_count)
print("NB, Count Vectors: \n", accuracy)

accuracy = train_model(gb, xtrain_count, train_y, xvalid_count)
print("GB, Count Vectors: \n", accuracy)

accuracy = train_model(ab, xtrain_count, train_y, xvalid_count)
print("AB, Count Vectors: \n", accuracy)

# Linear Classifier on Count Vectors
accuracy = train_model(lr, xtrain_count, train_y, xvalid_count)
print("LR, Count Vectors: ", accuracy)

# RF on Count Vectors
accuracy = train_model(rf, xtrain_count, train_y, xvalid_count)
print("RF, Count Vectors: ", accuracy)

# SVM on Count Vectors
accuracy = train_model(svc, xtrain_count, train_y, xvalid_count)
print("SVM, Count Vectors: ", accuracy)

# word level tf-idf
tfidf_vect = TfidfVectorizer(analyzer='word', token_pattern=r'\w{1,}', max_features=1000)
tfidf_vect.fit(datacolumn)

joblib.dump(tfidf_vect.vocabulary_,'feature.joblib')

xtrain_tfidf =  tfidf_vect.transform(train_x)
xvalid_tfidf =  tfidf_vect.transform(valid_x)

# Naive Bayes on Word Level TF IDF Vectors
accuracy = train_model(nb, xtrain_tfidf, train_y, xvalid_tfidf)
print("NB, WordLevel TF-IDF: ", accuracy)

accuracy = train_model(ab, xtrain_tfidf, train_y, xvalid_tfidf)
print("AB, WordLevel TF-IDF: ", accuracy)

accuracy = train_model(nb, xtrain_tfidf, train_y, xvalid_tfidf)
print("GB, WordLevel TF-IDF: ", accuracy)

# Linear Classifier on Word Level TF IDF Vectors
accuracy = train_model(lr, xtrain_tfidf, train_y, xvalid_tfidf)
print("LR, WordLevel TF-IDF: ", accuracy)

# RF on Word Level TF IDF Vectors
accuracy = train_model(rf, xtrain_tfidf, train_y, xvalid_tfidf)
print("RF, WordLevel TF-IDF: ", accuracy)

# SVM on Word Level TF IDF Vectors
accuracy = train_model(svc, xtrain_tfidf, train_y, xvalid_tfidf)
print("SVM, WordLevel TF-IDF: ", accuracy)

# ngram level tf-idf 
tfidf_vect_ngram = TfidfVectorizer(analyzer='word', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=1000)
tfidf_vect_ngram.fit(datacolumn)

xtrain_tfidf_ngram =  tfidf_vect_ngram.transform(train_x)
xvalid_tfidf_ngram =  tfidf_vect_ngram.transform(valid_x)

# Naive Bayes on Ngram Level TF IDF Vectors
accuracy = train_model(nb, xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("NB, N-Gram Vectors: ", accuracy)

accuracy = train_model(ab, xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("AB, N-Gram Vectors: ", accuracy)

accuracy = train_model(gb, xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("GB, N-Gram Vectors: ", accuracy)

# Linear Classifier on Ngram Level TF IDF Vectors
accuracy = train_model(lr, xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("LR, N-Gram Vectors: ", accuracy)

# RF on Ngram Level TF IDF Vectors
accuracy = train_model(rf, xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("RF, N-Gram Vectors: ", accuracy)

# SVM on Ngram Level TF IDF Vectors
accuracy = train_model(svc, xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("SVM, N-Gram Vectors: ", accuracy)

# characters level tf-idf
tfidf_vect_ngram_chars = TfidfVectorizer(analyzer='char', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=1000)
tfidf_vect_ngram_chars.fit(datacolumn)

xtrain_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(train_x) 
xvalid_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(valid_x) 

# Naive Bayes on Character Level TF IDF Vectors
accuracy = train_model(nb, xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("NB, CharLevel Vectors: ", accuracy)

accuracy = train_model(gb, xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("GB, CharLevel Vectors: ", accuracy)

# Linear Classifier on Character Level TF IDF Vectors
accuracy = train_model(lr, xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("LR, CharLevel Vectors: ", accuracy)

# RF on Character Level TF IDF Vectors
accuracy = train_model(rf, xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("RF, CharLevel Vectors: ", accuracy)

# SVM on Character Level TF IDF Vectors
accuracy = train_model(svc, xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("SVM, CharLevel Vectors: ", accuracy)

total_count_vect = count_vect.transform(datacolumn)
total_tfidf_vect = tfidf_vect.transform(datacolumn)
total_ngram = tfidf_vect_ngram.transform(datacolumn)
total_chars = tfidf_vect_ngram_chars.transform(datacolumn)

scores = cross_val_score(nb, total_count_vect, labels, cv=5)
print("NB CV Count Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(lr, total_count_vect, labels, cv=5)
print("LR CV Count Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(rf, total_count_vect, labels, cv=5)
print("RF CV Count Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
# scores = cross_val_score(svc, total_count_vect, labels, cv=5)
# print("SVC CV Count Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))

scores = cross_val_score(nb, total_tfidf_vect, labels, cv=5)
print("NB tfidf Word Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(lr, total_tfidf_vect, labels, cv=5)
print("LR tfidf Word Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(rf, total_tfidf_vect, labels, cv=5)
print("RF tfidf Word Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
# scores = cross_val_score(svc, total_tfidf_vect, labels, cv=5)
# print("SVC tfidf Word Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))

scores = cross_val_score(nb, total_ngram, labels, cv=5)
print("NB Ngram Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(lr, total_ngram, labels, cv=5)
print("LR Ngram Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(rf, total_ngram, labels, cv=5)
print("RF Ngram Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
# scores = cross_val_score(svc, total_ngram, labels, cv=5)
# print("SVC Ngram Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))

scores = cross_val_score(nb, total_chars, labels, cv=5)
print("NB Char Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(lr, total_chars, labels, cv=5)
print("LR Char Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
scores = cross_val_score(rf, total_chars, labels, cv=5)
print("RF Char Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))
# scores = cross_val_score(svc, total_chars, labels, cv=5)
# print("SVC Char Accuracy: %0.2f (+/- %0.2f)" % (scores.mean(), scores.std() * 2))