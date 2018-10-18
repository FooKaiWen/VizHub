import pandas as pd
import re
import nltk
from nltk.tokenize import word_tokenize as wt # nltk.download('punkt')
from nltk.corpus import stopwords # nltk.download('stopwords')
from nltk.stem.porter import PorterStemmer
from autocorrect import spell #spell correction

dataset = pd.read_csv('test.csv',encoding='ISO-8859-1')

from sklearn.feature_extraction.text import TfidfVectorizer

stemmer = PorterStemmer()

data = []

for i in range(dataset.shape[0]):
    if(pd.notna(dataset.iloc[i,1])):
        text = dataset.iloc[i,1]
        data.append(text)
    
vectorizer = TfidfVectorizer()
x = vectorizer.fit_transform(data)

from scipy import sparse
from scipy.stats import uniform
import numpy as np
from sklearn.decomposition import TruncatedSVD

data_csr = sparse.csr_matrix(x)
# print(data_csr)
tsvd = TruncatedSVD(n_components=4000)
X_sparse_tsvd = tsvd.fit(data_csr).transform(data_csr)
# print(X_sparse_tsvd)
print('Original number of features:', data_csr.shape[1])
print('Reduced number of features:', X_sparse_tsvd.shape[1])
print(tsvd.explained_variance_ratio_[0:2000].sum())
# data_csr_size = data_csr.nbytes/(1024**2)
# print('Size of full matrix: '+ '%3.2f' %data_csr_size + ' MB')

# print ('Shape of Sparse Matrix: ', x.shape)
# ZeroElem = (x.shape[0] * x.shape[1]) - x.nnz
# print ('Amount of Zero occurences: ', ZeroElem)
# print ('Sparsity: %.2f%%' % (100.0 * ZeroElem / (x.shape[0] * x.shape[1])))
# print(vectorizer.fit_transform(data).todense())
# print(vectorizer.vocabulary_)
# print(vectorizer.idf_)
