import pandas as pd
dataset = pd.read_csv('test.csv',encoding='ISO-8859-1')
dataset2 = pd.read_csv('savefile.csv',encoding='ISO-8859-1')
# for i in range(dataset.shape[0]):
#     if(pd.notna(dataset.iloc[i,0])):
#         num = dataset.iloc[i,0]
#         print("start")
#         print(num)
#         label = num/100
#         label = int(label)
#         print("here1: ")
#         print(label)
#         if(label < 1):
#             dataset2.iloc[i,0] = 0
#         else:
#             dataset2.iloc[i,0] = label

# dataset2.to_csv('savefile.csv', encoding='utf-8', index=False)
label = dataset2['likes']
text = dataset2['message']
feature = dataset2['numWord']
from sklearn_pandas import DataFrameMapper
from sklearn.feature_extraction.text import TfidfVectorizer
mapper = DataFrameMapper([
    ('message',TfidfVectorizer()),
    ('numWord',None),
])

tfidfvocab = mapper.fit_transform(dataset2)

# tfidfmatrix = TfidfVectorizer()
# tfidfvocab = tfidfmatrix.fit_transform(text).toarray()

from sklearn.model_selection import train_test_split

train, test, train_labels, test_labels = train_test_split(tfidfvocab,label,test_size=0.3,random_state=42)

from sklearn.naive_bayes import GaussianNB

gnb = GaussianNB()
model = gnb.fit(train, train_labels)
preds = gnb.predict(test)

from sklearn.metrics import accuracy_score

print(accuracy_score(test_labels, preds))