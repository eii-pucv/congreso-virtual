import mysql.connector
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import CountVectorizer
from plotly.offline import init_notebook_mode, plot, iplot
import plotly
from flask import jsonify
import plotly.graph_objs as go
import json

import string
import re
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords

def ngrams(proyect,min_words,max_words):
    
    #Conect MySQL
    mydb = mysql.connector.connect(
        host="165.22.190.109",
        user="root",
        passwd="3Ura4haF",
        database="congreso_prod"
    )
    
    df_comments = pd.read_sql('select * from comments;', con=mydb)
    mask = df_comments["project_id"] == proyect
    textos = df_comments.body[mask]
    
    print(textos)
    
    for i in range(min_words,max_words):
        cv = CountVectorizer(ngram_range=(i,i))
        text_processed = cv.fit_transform(textos)

        s = np.array(np.sum(text_processed,axis=0))
        sort = np.array(s).argsort()[0][::-1]

        names = []
        for key,values in cv.vocabulary_.items():
            if values in sort[:40]:
                print(key,"\tOcurrencia: ",s[0][values])
                names.append(key)

        values = s[0][sort[:40]]
        return json.dumps(values)