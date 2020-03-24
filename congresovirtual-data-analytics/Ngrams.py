from sklearn.feature_extraction.text import CountVectorizer
from plotly.offline import init_notebook_mode, plot, iplot
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
import mysql.connector
import dbconfig
import pandas as pd
import numpy as np
import string
import re
import json

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

def projectNgram(projectId, minWords):
    projectComments = getData(projectId)
    projectComments = projectComments.body

    cv = CountVectorizer(stop_words = 'english', ngram_range = (minWords, minWords))
    try:
        text_processed = cv.fit_transform(projectComments)
    except ValueError:
        return json.dumps({ "error": "ANALYTICS_ERR_EMPTY_VOCABULARY", "message": "Error: empty vocabulary; unable to continue." })

    s = np.array(np.sum(text_processed, axis = 0))
    sort = np.array(s).argsort()[0][::-1]

    names = []
    for (key, values) in cv.vocabulary_.items():
        if values in sort[:40]:
            names.append(key)

    values = s[0][sort[:40]]

    return json.dumps({ "names": names, "values": values.tolist() })
