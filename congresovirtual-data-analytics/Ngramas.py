import mysql.connector
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import CountVectorizer
from plotly.offline import init_notebook_mode, plot, iplot
#import plotly
#import plotly.graph_objs as go

import string
import re
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
import json
import dbconfig

def ngrams(proyect,words):
    
    #Conect MySQL
    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE
    )
    
    textos = pd.read_sql('select * from comments where project_id='+ str(proyect), con=mydb)
    #stop_words = pd.read_sql('select * from comments where project_id='+ str(proyect), con=mydb)
    textos = textos.body
    print(textos)
    
  
    cv = CountVectorizer(stop_words='english', ngram_range=(words,words))
    try:
        text_processed = cv.fit_transform(textos)
    except ValueError:
        return json.dumps({"error": "Vocabulario vacio. Aparentemente parametros faltantes o erroneos."})

    s = np.array(np.sum(text_processed,axis=0))
    sort = np.array(s).argsort()[0][::-1]

    names = []
    for key,values in cv.vocabulary_.items():
        if values in sort[:40]:
            #print(key,"\t Ocurrencia: ",s[0][values])
            names.append(key)

    values = s[0][sort[:40]]
    #names = cv.inverse_transform(sort[:20])[0]
    #names

    results = {}
    results["names"] = names
    results["values"] = values.tolist()
    #trace0 = go.Bar(
    #    x=names,
    #    y=values,
    #    name='Frecuencia de N-gramas'
    #)

    #data = [trace0]
    #layout = {
    #    'title': 'Frecuencia de '+str(words)+'-gramas',
    #    'margin':go.layout.Margin(l=50,r=50,b=100,t=100,pad=4)
    #}
    print(json.dumps(results))

    #fig = go.Figure(data=data, layout=layout)
    #iplot(fig, show_link=False,image_height=800)
    #fig.write_image("img/"+str(words)+"-grama.jpeg")
    return json.dumps(results)
        
    #return iplot(fig, show_link=False,image_height=800)
