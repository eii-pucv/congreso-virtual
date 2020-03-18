import mysql.connector
import pandas as pd
import numpy as np
import pandas as pd
from os import path
from wordcloud import WordCloud, STOPWORDS, ImageColorGenerator
import nltk
nltk.download('stopwords')
from nltk import tokenize
from nltk.corpus import stopwords
from multipledispatch import dispatch
import json
import dbconfig


@dispatch(int)
def get_data(id_project):
        #Conect MySQL
    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE,
        ssl_disabled=True
    )
    
    df_comments = pd.read_sql('select * from comments WHERE project_id =' +str(id_project)+ ';', con=mydb)
    return df_comments



# Start with one review:
def tagcloud_project(project,max_word):
    df_comments = get_data(project)
    text = " ".join(review for review in df_comments.body)
    wordcloud = WordCloud(max_words=max_word).generate(text)
    response = ["text"]
    response[0] = text
    return json.dumps(response)

