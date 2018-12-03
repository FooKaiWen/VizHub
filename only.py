import pandas as pd
from sklearn import model_selection, linear_model, metrics
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.externals import joblib

def trainModel(classifier, featureTrain, label, featureTest):
    classifier.fit(featureTrain, label) # fit the training dataset on the classifier
    joblib.dump(classifier,'model.joblib') #persist/save the model into joblib file for future use
    predictions = classifier.predict(featureTest) # predict the labels on validation dataset
    return metrics.classification_report(testLabel, predictions)

dataset = pd.read_csv('messageDataset.csv',encoding='ISO-8859-1')

labels = dataset['likes']
dataColumn = dataset['orimessage']

trainMessage,testMessage,trainLabel,testLabel = model_selection.train_test_split(dataColumn,labels,random_state=42)

model = linear_model.LogisticRegression(random_state=42, solver='lbfgs',max_iter=300)

# word level tf-idf
tfidfVect = TfidfVectorizer(analyzer='word', token_pattern=r'\w{1,}', max_features=1000)
tfidfVect.fit(dataColumn)

joblib.dump(tfidfVect.vocabulary_,'feature.joblib')

trainMessageTfidf =  tfidfVect.transform(trainMessage)
testMessageTfidf =  tfidfVect.transform(testMessage)

# Linear Classifier on Word Level TF IDF Vectors
report = trainModel(model, trainMessageTfidf, trainLabel, testMessageTfidf)
print(report)