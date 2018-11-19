from sklearn import model_selection, preprocessing, linear_model, naive_bayes, metrics, svm
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer
from sklearn import decomposition, ensemble
import pandas, numpy, textblob, string #, xgboost
# from keras.preprocessing import text, sequence
# from keras import layers, models, optimizers
# import nltk
# nltk.download('averaged_perceptron_tagger')

dataset = pandas.read_csv('savefile.csv',encoding='ISO-8859-1')
oriset = pandas.read_csv('test.csv',encoding='ISO-8859-1')

train_x, valid_x, train_y, valid_y = model_selection.train_test_split(dataset['message'], dataset['likes'])

encoder = preprocessing.LabelEncoder()
train_y = encoder.fit_transform(train_y)
valid_y = encoder.fit_transform(valid_y)

count_vect = CountVectorizer(analyzer='word', token_pattern=r'\w{1,}')
count_vect.fit(oriset['message'])

# transform the training and validation data using count vectorizer object
xtrain_count =  count_vect.transform(train_x)
xvalid_count =  count_vect.transform(valid_x)

# word level tf-idf
tfidf_vect = TfidfVectorizer(analyzer='word', token_pattern=r'\w{1,}', max_features=5000)
tfidf_vect.fit(oriset['message'])
xtrain_tfidf =  tfidf_vect.transform(train_x)
xvalid_tfidf =  tfidf_vect.transform(valid_x)

# ngram level tf-idf 
tfidf_vect_ngram = TfidfVectorizer(analyzer='word', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=5000)
tfidf_vect_ngram.fit(oriset['message'])
xtrain_tfidf_ngram =  tfidf_vect_ngram.transform(train_x)
xvalid_tfidf_ngram =  tfidf_vect_ngram.transform(valid_x)

# characters level tf-idf
tfidf_vect_ngram_chars = TfidfVectorizer(analyzer='char', token_pattern=r'\w{1,}', ngram_range=(2,3), max_features=5000)
tfidf_vect_ngram_chars.fit(oriset['message'])
xtrain_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(train_x) 
xvalid_tfidf_ngram_chars =  tfidf_vect_ngram_chars.transform(valid_x) 

oriset['char_count'] = oriset['message'].apply(len)
oriset['word_count'] = oriset['message'].apply(lambda x: len(x.split()))
oriset['word_density'] = oriset['char_count'] / (oriset['word_count']+1)
oriset['punctuation_count'] = oriset['message'].apply(lambda x: len("".join(_ for _ in x if _ in string.punctuation))) 
oriset['title_word_count'] = oriset['message'].apply(lambda x: len([wrd for wrd in x.split() if wrd.istitle()]))
oriset['upper_case_word_count'] = oriset['message'].apply(lambda x: len([wrd for wrd in x.split() if wrd.isupper()]))

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

oriset['noun_count'] = oriset['message'].apply(lambda x: check_pos_tag(x, 'noun'))
oriset['verb_count'] = oriset['message'].apply(lambda x: check_pos_tag(x, 'verb'))
oriset['adj_count'] = oriset['message'].apply(lambda x: check_pos_tag(x, 'adj'))
oriset['adv_count'] = oriset['message'].apply(lambda x: check_pos_tag(x, 'adv'))
oriset['pron_count'] = oriset['message'].apply(lambda x: check_pos_tag(x, 'pron'))

# train a LDA Model
lda_model = decomposition.LatentDirichletAllocation(n_components=20, learning_method='online', max_iter=20)
X_topics = lda_model.fit_transform(xtrain_count)
topic_word = lda_model.components_ 
vocab = count_vect.get_feature_names()

# view the topic models
n_top_words = 10
topic_summaries = []
for i, topic_dist in enumerate(topic_word):
    topic_words = numpy.array(vocab)[numpy.argsort(topic_dist)][:-(n_top_words+1):-1]
    topic_summaries.append(' '.join(topic_words))

def train_model(classifier, feature_vector_train, label, feature_vector_valid, is_neural_net=False):
    # fit the training dataset on the classifier
    classifier.fit(feature_vector_train, label)
    
    # predict the labels on validation dataset
    predictions = classifier.predict(feature_vector_valid)
    
    if is_neural_net:
        predictions = predictions.argmax(axis=-1)
    
    return metrics.accuracy_score(predictions, valid_y)

# Naive Bayes on Count Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_count, train_y, xvalid_count)
print("NB, Count Vectors: ", accuracy)

# Naive Bayes on Word Level TF IDF Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_tfidf, train_y, xvalid_tfidf)
print("NB, WordLevel TF-IDF: ", accuracy)

# Naive Bayes on Ngram Level TF IDF Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("NB, N-Gram Vectors: ", accuracy)

# Naive Bayes on Character Level TF IDF Vectors
accuracy = train_model(naive_bayes.MultinomialNB(), xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("NB, CharLevel Vectors: ", accuracy)

# Linear Classifier on Count Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_count, train_y, xvalid_count)
print("LR, Count Vectors: ", accuracy)

# Linear Classifier on Word Level TF IDF Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_tfidf, train_y, xvalid_tfidf)
print("LR, WordLevel TF-IDF: ", accuracy)

# Linear Classifier on Ngram Level TF IDF Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("LR, N-Gram Vectors: ", accuracy)

# Linear Classifier on Character Level TF IDF Vectors
accuracy = train_model(linear_model.LogisticRegression(), xtrain_tfidf_ngram_chars, train_y, xvalid_tfidf_ngram_chars)
print("LR, CharLevel Vectors: ", accuracy)

# SVM on Ngram Level TF IDF Vectors
accuracy = train_model(svm.SVC(), xtrain_tfidf_ngram, train_y, xvalid_tfidf_ngram)
print("SVM, N-Gram Vectors: ", accuracy)

# RF on Count Vectors
accuracy = train_model(ensemble.RandomForestClassifier(), xtrain_count, train_y, xvalid_count)
print("RF, Count Vectors: ", accuracy)

# RF on Word Level TF IDF Vectors
accuracy = train_model(ensemble.RandomForestClassifier(), xtrain_tfidf, train_y, xvalid_tfidf)
print("RF, WordLevel TF-IDF: ", accuracy)

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

# import pandas as pd
# oriset = pd.read_csv('test.csv',encoding='ISO-8859-1')
# dataset = pd.read_csv('savefile.csv',encoding='ISO-8859-1')
# for i in range(oriset.shape[0]):
#     if(pd.notna(oriset.iloc[i,0])):
#         num = oriset.iloc[i,0]
#         print("start")
#         print(num)
#         label = num/100
#         label = int(label)
#         if(label < 10):
#             label = 1
#         elif(label < 50):
#             label = 5
#         elif(label < 100):
#             label = 10  
#         elif(label < 150):
#             label = 15  
#         elif(label < 200):
#             label = 20  
#         elif(label < 250):
#             label = 25
#         elif(label < 300):
#             label = 30  
#         elif(label < 350):
#             label = 35  
#         elif(label < 400):
#             label = 40        
#         print("here1: ")
#         print(label)
#         if(label < 1):
#             dataset.iloc[i,0] = 0
#         else:
#             dataset.iloc[i,0] = label

# dataset.to_csv('savefile.csv', encoding='utf-8', index=False)

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