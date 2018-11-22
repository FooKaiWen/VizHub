import pandas as pd
oriset = pd.read_csv('10k_5000.csv',encoding='ISO-8859-1')
dataset = pd.read_csv('savefile.csv',encoding='ISO-8859-1')
for i in range(oriset.shape[0]):
    if(pd.notna(oriset.iloc[i,0])):
        num = oriset.iloc[i,0]
        # print("start")
        # print(num)
        label = num
        # label = int(label)
        if(label < 2000):
            label = 2000
        elif(label < 4000):
            label = 4000
        elif(label < 6000):
            label = 6000  
        elif(label < 8000):
            label = 8000  
        # elif(label < 7500):
        #     label = 7500  
        elif(label < 10000):
            label = 10000
        # elif(label < 300):
        #     label = 30  
        # elif(label < 3500):
        #     label = 35  
        # elif(label < 400):
        #     label = 40
        # elif(label < 4500):
        #     label = 45  
        # elif(label < 500):
        #     label = 50  
        # elif(label < 5500):
        #     label = 55
        # elif(label < 6000):
        #     label = 60  
        # elif(label < 6500):
        #     label = 65  
        # elif(label < 7000):
        #     label = 70
        # elif(label < 7500):
        #     label = 75  
        # elif(label < 8000):
        #     label = 35  
        # elif(label < 8500):
        #     label = 80  
        # elif(label < 9000):
        #     label = 90  
        # elif(label < 9500):
        #     label = 95  
        # elif(label < 10000):
        #     label = 100  
        # elif(label < 10500):
        #     label = 105  
        # elif(label < 11000):
        #     label = 110  
        # elif(label < 11500):
        #     label = 115  
        # elif(label < 12000):
        #     label = 120
        else:
            label = 9999           
        # print("here1: ")
        # print(label)
        dataset.iloc[i,0] = label

dataset.to_csv('savefile.csv', encoding='utf-8', index=False)

from sklearn import model_selection, preprocessing, linear_model, naive_bayes, metrics, svm
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer
from sklearn import decomposition, ensemble
import pandas, numpy, textblob, string

import warnings
warnings.filterwarnings("ignore", category=FutureWarning)

def train_model(classifier, feature_vector_train, label, feature_vector_valid):
    # fit the training dataset on the classifier
    classifier.fit(feature_vector_train, label)
    # print(feature_vector_train.shape)
    # predict the labels on validation dataset
    predictions = classifier.predict(feature_vector_valid)
    return predictions
    # return metrics.accuracy_score(predictions, valid_y)

dataset = pandas.read_csv('savefile.csv',encoding='ISO-8859-1')
oriset = pandas.read_csv('5000.csv',encoding='ISO-8859-1')

train_x, valid_x, train_y, valid_y = model_selection.train_test_split(dataset['message'], dataset['two_thousand_likes'])

count_vect = CountVectorizer(analyzer='word', token_pattern=r'\w{1,}')
count_vect.fit(dataset['message'])

# transform the training and validation data using count vectorizer object
xtrain_count =  count_vect.transform(train_x)
xvalid_count =  count_vect.transform(valid_x)



import numpy as np
test = ['new chief staff']
test_count = count_vect.transform(test)
# print(test_count)
# print(test_count.shape)
# test = [1,2,3]
# print(len(test))
A = np.array([test_count])
N = A.size
# B = np.pad(A, ((0,N),(0,10)), mode='constant')
# print(B)
# arr = [0]*7290
# arr[0] = test
# arr = np.reshape(arr,(1,-1))

# Naive Bayes on Count Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_count, train_y, test_count)
print("NB, Count Vectors: ", accuracy)


# Linear Classifier on Count Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_count, train_y, test_count)
print("LR, Count Vectors: ", accuracy)

# RF on Count Vectors
accuracy = train_model(ensemble.RandomForestClassifier(), xtrain_count, train_y, test_count)
print("RF, Count Vectors: ", accuracy)

# SVM on Count Vectors
accuracy = train_model(svm.SVC(), xtrain_count, train_y, test_count)
print("SVM, Count Vectors: ", accuracy)

# word level tf-idf
tfidf_vect = TfidfVectorizer(analyzer='word', token_pattern=r'\w{1,}', max_features=5000)
tfidf_vect.fit(dataset['message'])
xtrain_tfidf =  tfidf_vect.transform(train_x)
xvalid_tfidf =  tfidf_vect.transform(test)

# Naive Bayes on Word Level TF IDF Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_tfidf, train_y, xvalid_tfidf)
print("NB, WordLevel TF-IDF: ", accuracy)

# Linear Classifier on Word Level TF IDF Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_tfidf, train_y, xvalid_tfidf)
print("LR, WordLevel TF-IDF: ", accuracy)

# RF on Word Level TF IDF Vectors
accuracy = train_model(ensemble.RandomForestClassifier(), xtrain_tfidf, train_y, xvalid_tfidf)
print("RF, WordLevel TF-IDF: ", accuracy)

# SVM on Word Level TF IDF Vectors
accuracy = train_model(svm.SVC(), xtrain_tfidf, train_y, xvalid_tfidf)
print("SVM, WordLevel TF-IDF: ", accuracy)

# ngram level tf-idf 
tfidf_vect_ngram = TfidfVectorizer(analyzer='word', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=5000)
tfidf_vect_ngram.fit(dataset['message'])
xtrain_tfidf_ngram =  tfidf_vect_ngram.transform(train_x)
xvalid_tfidf_ngram =  tfidf_vect_ngram.transform(valid_x)

# Naive Bayes on Ngram Level TF IDF Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("NB, N-Gram Vectors: ", accuracy)

# Linear Classifier on Ngram Level TF IDF Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("LR, N-Gram Vectors: ", accuracy)

# RF on Ngram Level TF IDF Vectors
accuracy = train_model(ensemble.RandomForestClassifier(), xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("RF, N-Gram Vectors: ", accuracy)

# SVM on Ngram Level TF IDF Vectors
accuracy = train_model(svm.SVC(), xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("SVM, N-Gram Vectors: ", accuracy)

# characters level tf-idf
tfidf_vect_ngram_chars = TfidfVectorizer(analyzer='char', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=5000)
tfidf_vect_ngram_chars.fit(dataset['message'])
xtrain_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(train_x) 
xvalid_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(valid_x) 

# Naive Bayes on Character Level TF IDF Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("NB, CharLevel Vectors: ", accuracy)

# Linear Classifier on Character Level TF IDF Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("LR, CharLevel Vectors: ", accuracy)

# RF on Character Level TF IDF Vectors
accuracy = train_model(ensemble.RandomForestClassifier(), xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("RF, CharLevel Vectors: ", accuracy)

# SVM on Character Level TF IDF Vectors
accuracy = train_model(svm.SVC(), xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("SVM, CharLevel Vectors: ", accuracy)

# # Extereme Gradient Boosting on Count Vectors
# accuracy = train_model(xgboost.XGBClassifier(), xtrain_count.tocsc(), train_y, xvalid_count.tocsc())
# print("Xgb, Count Vectors: ", accuracy)

# # Extereme Gradient Boosting on Word Level TF IDF Vectors
# accuracy = train_model(xgboost.XGBClassifier(), xtrain_tfidf.tocsc(), train_y, xvalid_tfidf.tocsc())
# print("Xgb, WordLevel TF-IDF: ", accuracy)

# # Extereme Gradient Boosting on Character Level TF IDF Vectors
# accuracy = train_model(xgboost.XGBClassifier(), xtrain_tfidf_ngram_chars.tocsc(), train_y, xvalid_tfidf_ngram_chars.tocsc())
# print("Xgb, CharLevel Vectors: ", accuracy)

# def create_model_architecture(input_size):
#     # create input layer 
#     input_layer = layers.Input((input_size, ), sparse=True)
    
#     # create hidden layer
#     hidden_layer = layers.Dense(100, activation="relu")(input_layer)
    
#     # create output layer
#     output_layer = layers.Dense(1, activation="sigmoid")(hidden_layer)

#     classifier = models.Model(inputs = input_layer, outputs = output_layer)
#     classifier.compile(optimizer=optimizers.Adam(), loss='binary_crossentropy')
#     return classifier 

# classifier = create_model_architecture(xtrain_tfidf_ngram.shape[1])
# accuracy = train_model(classifier, xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram, is_neural_net=True)
# print("NN, Ngram Level TF IDF Vectors",  accuracy)



# label = oriset2['likes']
# text = oriset2['message']
# feature = oriset2['numWord']
# from sklearn_pandas import DataFrameMapper
# from sklearn.feature_extraction.text import TfidfVectorizer
# mapper = DataFrameMapper([
#     ('message',TfidfVectorizer()),
#     ('numWord',None),
# ])

# tfidfvocab = mapper.fit_transform(oriset2)

# # tfidfmatrix = TfidfVectorizer()
# # tfidfvocab = tfidfmatrix.fit_transform(text).toarray()

# from sklearn.model_selection import train_test_split

# train, test, train_labels, test_labels = train_test_split(tfidfvocab,label,test_size=0.3,random_state=42)

# from sklearn.naive_bayes import GaussianNB
# from sklearn.metrics import accuracy_score
# from sklearn.ensemble import RandomForestClassifier
# rfc = RandomForestClassifier()
# rfc.fit(train,train_labels)
# preds = rfc.predict(test)
# print(accuracy_score(test_labels,preds)) #0.03833
# # gnb = GaussianNB()
# # model = gnb.fit(train, train_labels)
# # preds = gnb.predict(test)
# # print(accuracy_score(test_labels, preds)) #0.02333