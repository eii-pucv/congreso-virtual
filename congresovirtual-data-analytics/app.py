from flask import Flask
from flask import request
from TopicsModels import topicmodel_forproject
from Ngramas import ngrams
from flask_cors import CORS
from flask import Response
from WordCloud import tagcloud_project
import json

app = Flask(__name__)
CORS(app)

@app.errorhandler(404)
def page_not_found(e):
    return Response(json.dumps({"error": "ANALYTICS_ERR_NOT_FOUND", "errordescription": "Nothing here!"}), status=404, mimetype='application/json')

@app.route("/")
def home():
    return Response(json.dumps({"info": "Congreso Virtual Analytics, ready to serve!"}), status=200, mimetype='application/json');


@app.route("/ngram")
def ngram():
    project_id = request.args.get('project_id',-1, type=int)
    words = request.args.get('words',-1, type=int)
    if not project_id or not words:
        return Response(json.dumps({"error": "ANALYTICS_ERR_PARAM_MISSING", "errordescription": "Missing parameters"}), status=400, mimetype='application/json')
    else:
        try:
            #This can return the following errors: ANALYTICS_ERR_EMPTY_VOCABULARY
            output = ngrams(project_id,words)
            output_dict = (json.loads(output))
            if "error" in output_dict:
                return Response(output, status=400, mimetype='application/json')
            else:
                return Response(output, status=200, mimetype='application/json')
        except Exception as ex:
            print(ex)
            return Response(json.dumps({"error": "ANALYTICS_ERR_SERVER_ERROR", "errordescription": ex}), status=500, mimetype='application/json')


@app.route("/topicmodel")
def topicmodel():
    project_id = request.args.get('project_id',-1, type=int)
    if not project_id:
        return Response(json.dumps({"error": "ANALYTICS_ERR_PARAM_MISSING", "errordescription": "Missing parameters"}), status=400, mimetype='application/json')
    else:
        try:
            #This can return the following errors: ANALYTICS_ERR_EMPTY_COLLECTION
            output = topicmodel_forproject(project_id)
            try:
                output_dict = (json.loads(output))
                #If we reached here without error it means something wrong already happened
                return Response(output, status=400, mimetype='application/json')
            except ValueError as e:
                #actually, this is a good case output. Non-JSON output means a correct output
                return Response(output, status=200, mimetype='application/json')
        except Exception as ex:
            print(ex)
            return Response(json.dumps({"error": "ANALYTICS_ERR_SERVER_ERROR", "errordescription": ex}), status=500, mimetype='application/json')

@app.route("/wordcloud")
def wordcloud():
    project_id = request.args.get('project_id',0, type=int)
    words = request.args.get('words',1000, type=int)
    if not project_id or not words:
        return Response(json.dumps({"error": "ANALYTICS_ERR_PARAM_MISSING", "errordescription": "Missing parameters"}), status=400, mimetype='application/json')
    else:
        try:
            output = tagcloud_project(project_id,words)
            output_dict = (json.loads(output))
            if "error" in output_dict:
                return Response(output, status=400, mimetype='application/json')
            else:
                return Response(output, status=200, mimetype='application/json')
        except Exception as ex:
            print(ex)
            return Response(json.dumps({"error": "ANALYTICS_ERR_SERVER_ERROR", "errordescription": ex}), status=500, mimetype='application/json')

@app.route("/coruscant")
def coruscant():
    return Response(json.dumps({"coruscant": "SW4gdGhlIG5hbWUgb2YgdGhlIEdhbGFjdGljIFNlbmF0ZSBvZiB0aGUgUmVwdWJsaWMsIHlvdSBhcmUgdW5kZXIgYXJyZXN0LCBDaGFuY2VsbG9yLuKAnSDigJxBcmUgeW91IHRocmVhdGVuaW5nIG1lLCBNYXN0ZXIgSmVkaT/igJ0g4oCcVGhlIFNlbmF0ZSB3aWxsIGRlY2lkZSB5b3VyIGZhdGUu4oCdIOKAnEkgQU0gdGhlIFNlbmF0ZSE="}), status=418, mimetype='application/json');
