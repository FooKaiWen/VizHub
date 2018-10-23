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

from sklearn.feature_extraction.text import CountVectorizer #This method counts how many times a word is used.
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split
from sklearn import linear_model # Linear regression is used
from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
import numpy as np

matrix = TfidfVectorizer(
    min_df=1,  # min count for relevant vocabulary
    max_features=4000,  # maximum number of features
    strip_accents='unicode',  # replace all accented unicode char 
    # by their corresponding  ASCII char
    analyzer='word',  # features made of words
    ngram_range=(1, 1),  # features made of a single tokens
    use_idf=True,  # enable inverse-document-frequency reweighting
    smooth_idf=True,  # prevents zero division for unseen words
    sublinear_tf=False)
x = matrix.fit_transform(data)

from scipy import sparse
from scipy.stats import uniform
import numpy as np
from sklearn.decomposition import TruncatedSVD

data_csr = sparse.csr_matrix(x).toarray()
# # print(data_csr)
# tsvd = TruncatedSVD(n_components=1998)
# X_sparse_tsvd = tsvd.fit(data_csr).transform(data_csr)

# tfidf_vectorizer = TfidfVectorizer(
#     min_df=1,  # min count for relevant vocabulary
#     max_features=4000,  # maximum number of features
#     strip_accents='unicode',  # replace all accented unicode char 
#     # by their corresponding  ASCII char
#     analyzer='word',  # features made of words
#     ngram_range=(1, 1),  # features made of a single tokens
#     use_idf=True,  # enable inverse-document-frequency reweighting
#     smooth_idf=True,  # prevents zero division for unseen words
#     sublinear_tf=False)
# tfidf_df = tfidf_vectorizer.fit_transform(X_sparse_tsvd)

# tfidf_text = tfidf_transformer.transform(X_sparse_tsvd).toarray()

# print(matrix.get_feature_names())
import csv

myFile = open('savefile.csv', 'w')
with myFile:
    # writer = csv.writer(myFile)
    # writer.write(matrix.get_feature_names())
    # writer.writerows(x)
    writer = csv.DictWriter(myFile,fieldnames=matrix.get_feature_names())
    writer.writerows(x)
print("Writing complete")