import flask
from flask import Flask, render_template, request
from sklearn.externals import joblib
import featurex

app = Flask(__name__)

@app.route("/")
@app.route("/index")
def index():
    return flask.render_template('index.html')

@app.route('/predict', methods=['GET','POST'])
def make_prediction():
    if request.method == 'POST':
        text = request.form['message']
        encodedtext = loadedvec.transform(text).toarray()
        prediction = model.predict(encodedtext)
        return render_template('index.html',message=prediction)


# @app.route('/form-example', methods=['GET', 'POST']) #allow both GET and POST requests
# def form_example():
#     if request.method == 'POST':  #this block is only entered when the form is submitted
#         language = request.form.get('language')
#         framework = request.form['framework']

#         return '''<h1>The language value is: {}</h1>
#                   <h1>The framework value is: {}</h1>'''.format(language, framework)

#     return '''<form method="POST">
#                   Language: <input type="text" name="language"><br>
#                   Framework: <input type="text" name="framework"><br>
#                   <input type="submit" value="Submit"><br>
#               </form>'''

if __name__ == '__main__':
    featurex.process()
    model = joblib.load('C:/xampp/htdocs/VizHub/test.joblib')
    loadedvec = joblib.load('C:/xampp/htdocs/VizHub/tfidf.joblib')
    app.run(host='0.0.0.0',port=8001,debug=True)
