from datetime import datetime
from nltk.tokenize import RegexpTokenizer
from stop_words import get_stop_words
from nltk.stem.porter import PorterStemmer
from gensim import corpora, models
from multipledispatch import dispatch
import mysql.connector
import dbconfig
import pandas as pd
import numpy as np
import sys
import os
import gensim
import pyLDAvis
import pyLDAvis.gensim
import json
sys.path.append(os.path.join(os.getcwd(), '..'))

@dispatch(int)
def getData(projectId):
    # Connect MySQL
    mydb = mysql.connector.connect(
        host = dbconfig.CONGRESO_MYSQL_HOST,
        user = dbconfig.CONGRESO_MYSQL_USER,
        passwd = dbconfig.CONGRESO_MYSQL_PASSWD,
        database = dbconfig.CONGRESO_MYSQL_DATABASE,
        ssl_disabled = True
    )

    return pd.read_sql('SELECT * FROM comments WHERE project_id = ' + str(projectId) + ';', con = mydb)

def projectTopicModel(projectId):
    projectComments = getData(projectId)
    numTopics = 5

    projectComments = projectComments.body
    projectComments = projectComments.str.lower()
    pattern = r"@([A-Za-z0-9_]+)"
    projectComments = projectComments.str.replace(pattern, '')

    elements = np.array(projectComments.tolist())
    tokenizer = RegexpTokenizer(r'\w+')
    es_stop = get_stop_words('es')
    texts = []
    for i in elements:
        raw = i.lower()
        tokens = tokenizer.tokenize(raw)
        # Remove stop words from tokens
        stopped_tokens = [i for i in tokens if not i in es_stop]
        texts.append(stopped_tokens)

    dictionary = corpora.Dictionary(texts)
    corpus = [dictionary.doc2bow(text) for text in texts]

    try:
        ldamodel = gensim.models.ldamulticore.LdaMulticore(corpus, num_topics = numTopics, id2word = dictionary, passes = 20)
    except ValueError:
        return json.dumps({ "error": "ANALYTICS_ERR_EMPTY_COLLECTION", "message": "Error: empty collection found; unable to continue." })

    visData = pyLDAvis.gensim.prepare(ldamodel, corpus, dictionary)
    pyLDAvis.display(visData)

    return json.dumps({ "value": pyLDAvis.prepared_data_to_html(visData) })
