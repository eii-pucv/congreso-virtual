from flask import Flask
from flask import request
from flask_cors import CORS
from flask import Response
from Ngrams import projectNgram
from TopicsModels import projectTopicModel
from WordCloud import projectWordCloud
import json

app = Flask(__name__)
CORS(app)

@app.errorhandler(404)
def page_not_found(e):
    return Response(json.dumps({ "error": "ANALYTICS_ERR_NOT_FOUND", "message": "Error: nothing here!" }), status = 404, mimetype = 'application/json')

@app.route("/")
def home():
    return Response(json.dumps({ "info": "Congreso Virtual Analytics, ready to serve!" }), status = 200, mimetype = 'application/json')

@app.route("/ngram")
def ngram():
    projectId = request.args.get('project_id', None, type = int)
    minWords = request.args.get('min_words', -1, type = int)
    if not projectId or not minWords:
        return Response(json.dumps({ "error": "ANALYTICS_ERR_PARAM_MISSING", "message": "Error: missing parameters." }), status = 400, mimetype = 'application/json')
    else:
        try:
            # This can return the following errors: ANALYTICS_ERR_EMPTY_VOCABULARY
            output = projectNgram(projectId, minWords)
            outputDict = json.loads(output)
            if "error" in outputDict:
                return Response(output, status = 400, mimetype = 'application/json')
            else:
                return Response(output, status = 200, mimetype = 'application/json')
        except Exception as exception:
            return Response(json.dumps({ "error": "ANALYTICS_ERR_SERVER_ERROR", "message": exception }), status = 500, mimetype = 'application/json')

@app.route("/topicmodel")
def topicmodel():
    projectId = request.args.get('project_id', None, type = int)
    if not projectId:
        return Response(json.dumps({ "error": "ANALYTICS_ERR_PARAM_MISSING", "message": "Error: missing parameters." }), status = 400, mimetype = 'application/json')
    else:
        try:
            # This can return the following errors: ANALYTICS_ERR_EMPTY_COLLECTION
            output = projectTopicModel(projectId)
            outputDict = json.loads(output)
            if "error" in outputDict:
                return Response(output, status = 400, mimetype = 'application/json')
            else:
                return Response(output, status = 200, mimetype = 'application/json')
        except Exception as exception:
            return Response(json.dumps({ "error": "ANALYTICS_ERR_SERVER_ERROR", "message": exception }), status = 500, mimetype = 'application/json')

@app.route("/wordcloud")
def wordcloud():
    projectId = request.args.get('project_id', 0, type = int)
    maxWords = request.args.get('max_words', 1000, type = int)
    if not projectId or not maxWords:
        return Response(json.dumps({ "error": "ANALYTICS_ERR_PARAM_MISSING", "message": "Error: missing parameters." }), status = 400, mimetype = 'application/json')
    else:
        try:
            output = projectWordCloud(projectId, maxWords)
            outputDict = json.loads(output)
            if "error" in outputDict:
                return Response(output, status = 400, mimetype = 'application/json')
            else:
                return Response(output, status = 200, mimetype = 'application/json')
        except Exception as exception:
            print(exception)
            return Response(json.dumps({ "error": "ANALYTICS_ERR_SERVER_ERROR", "message": exception }), status = 500, mimetype = 'application/json')

@app.route("/coruscant")
def coruscant():
    return Response(json.dumps({ "coruscant": "SW4gdGhlIG5hbWUgb2YgdGhlIEdhbGFjdGljIFNlbmF0ZSBvZiB0aGUgUmVwdWJsaWMsIHlvdSBhcmUgdW5kZXIgYXJyZXN0LCBDaGFuY2VsbG9yLuKAnSDigJxBcmUgeW91IHRocmVhdGVuaW5nIG1lLCBNYXN0ZXIgSmVkaT/igJ0g4oCcVGhlIFNlbmF0ZSB3aWxsIGRlY2lkZSB5b3VyIGZhdGUu4oCdIOKAnEkgQU0gdGhlIFNlbmF0ZSE=" }), status = 418, mimetype = 'application/json')
