from flask import Flask
from flask import request
from TopicsModels import topicmodel_forproyect
from Ngramas import ngrams
from flask_cors import CORS
from flask import Response
from WordCloud import tagcloud_proyect
import json

app = Flask(__name__)
CORS(app)

@app.route("/")
def home():
    return "Hello, Flask!"

@app.route("/ngram")
def ngram():
    project_id = request.args.get('project_id',-1, type=int)
    words = request.args.get('words',-1, type=int)
    #max_words =  request.args.get('max_words')
    #return "PROJECT_ID " + project_id + " PROJECT_ID " + min_words + "PROJECT_ID" + max_words
    if not project_id or not words:
        return Response(json.dumps({"error": "Faltan parametros"}), mimetype='application/json')
    else:
        return Response(ngrams(project_id,words), mimetype='application/json')
    


@app.route("/topicmodel")
def topicmodel():
    project_id = request.args.get('project_id',-1, type=int)
    #min_words = request.args.get('min_words')
    #max_words =  request.args.get('max_words')
    #return "PROJECT_ID " + project_id + " PROJECT_ID " + min_words + "PROJECT_ID" + max_words
    if not project_id:
        return Response(json.dumps({"error": "Faltan parametros"}), mimetype='application/json')
    else:
        return topicmodel_forproyect(project_id)

@app.route("/wordcloud")
def wordcloud():
    project_id = request.args.get('project_id',0, type=int)
    words = request.args.get('words',1000, type=int)
	#max_words =  request.args.get('max_words')
    #return "PROJECT_ID " + project_id + " PROJECT_ID " + min_words + "PROJECT_ID" + max_words
    if not project_id or not words:
        return Response(json.dumps({"error": "Faltan parametros"}), mimetype='application/json')
    else:
        return Response(tagcloud_proyect(project_id,words), mimetype='application/json')