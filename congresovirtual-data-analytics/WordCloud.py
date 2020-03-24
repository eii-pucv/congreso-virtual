from os import path
from wordcloud import WordCloud, STOPWORDS, ImageColorGenerator
from nltk import tokenize
from nltk.corpus import stopwords
from multipledispatch import dispatch
import mysql.connector
import dbconfig
import pandas as pd
import numpy as np
import nltk
import json
nltk.download('stopwords')

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

def projectWordCloud(projectId, maxWords):
    projectComments = getData(projectId)
    projectComments = projectComments.body
    text = " ".join(review for review in projectComments)
    WordCloud(max_words = maxWords).generate(text)

    return json.dumps({ "value": text })
