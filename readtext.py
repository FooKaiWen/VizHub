import pymongo

myclient = pymongo.MongoClient("mongodb://localhost:27017/")
mydb = myclient["test"]
mycol = mydb["post"]

x = mycol.find({'message': 'Try me'})

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

def process():
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
    
        from sklearn.feature_extraction.text import CountVectorizer    
        # from sklearn.feature_extraction.text import TfidfVectorizer #This method counts how many times a word is used.
        from sklearn.model_selection import train_test_split
        # from sklearn import linear_model # Linear regression is used
        from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
        import numpy as np

        matrix2 = CountVectorizer()
        matrix2.fit(data)
        x = matrix2.transform(data)

        # #This method balance out the number of times of used words with others
        from sklearn.feature_extraction.text import TfidfTransformer
        tfidf_transformer = TfidfTransformer().fit(x)

        from sklearn.externals import joblib
        joblib.dump(tfidf_transformer,'tfidf.joblib')
        loadedvec = joblib.load('tfidf.joblib')

        loadedvec.transform(x).toarray()
        tfidf2_text = tfidf_transformer.transform(x).toarray()
        print(tfidf2_text)
        likes = dataset['likes']

        tfidf2_text_train, tfidf2_text_test, likes2_train, likes2_test = \
        train_test_split(tfidf2_text, likes, test_size=0.3)

        tfidf_text = tfidf_transformer.transform(x).toarray()
        print(tfidf_text)

        tfidf_text_train, tfidf_text_test, likes_train, likes_test = \
        train_test_split(tfidf_text, likes, test_size=0.3)

        from sklearn.ensemble import RandomForestRegressor # Random Forest Regressor is used

        forest_reg = RandomForestRegressor(random_state=42)
        forest_reg.fit(tfidf_text_train, likes_train)
        likes_pred = forest_reg.predict(tfidf_text_test)
        forest_mse = mean_squared_error(likes_pred, likes_test)
        forest_rmse = np.sqrt(forest_mse)
        print('Random Forest RMSE: %.4f' % forest_rmse)

        from sklearn.externals import joblib
        joblib.dump(forest_reg,'test.joblib')
        loadedmodel = joblib.load('test.joblib')
        likes_pred = loadedmodel.predict(tfidf_text_test)
        forest_mse = mean_squared_error(likes_pred, likes_test)
        forest_rmse = np.sqrt(forest_mse)
        print('Random Forest RMSE: %.4f' % forest_rmse)