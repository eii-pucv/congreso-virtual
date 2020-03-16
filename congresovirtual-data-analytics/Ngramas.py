import mysql.connector
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import CountVectorizer
from plotly.offline import init_notebook_mode, plot, iplot

import string
import re
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
import json
import dbconfig

def ngrams(project,words):
    
    #Conect MySQL
    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE,
        ssl_disabled=True
    )
    
    textos = pd.read_sql('select * from comments where project_id='+ str(project), con=mydb)
    textos = textos.body
    print(textos)
    
    cv = CountVectorizer(stop_words='english', ngram_range=(words,words))
    try:
        text_processed = cv.fit_transform(textos)
    except ValueError:
        return json.dumps({"error": "ANALYTICS_ERR_EMPTY_VOCABULARY", "errordescription": "Empty vocabulary. Unable to continue"})

    s = np.array(np.sum(text_processed,axis=0))
    sort = np.array(s).argsort()[0][::-1]

    names = []
    for key,values in cv.vocabulary_.items():
        if values in sort[:40]:
            names.append(key)

    values = s[0][sort[:40]]

    results = {}
    results["names"] = names
    results["values"] = values.tolist()

    print(json.dumps(results))
    return json.dumps(results)
