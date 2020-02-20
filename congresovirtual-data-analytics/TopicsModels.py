import numpy as np
import pandas as pd
from datetime import datetime
import sys
import os
sys.path.append(os.path.join(os.getcwd(),'..'))
from nltk.tokenize import RegexpTokenizer
from stop_words import get_stop_words
from nltk.stem.porter import PorterStemmer
from gensim import corpora, models
import gensim
import pyLDAvis
import pyLDAvis.gensim
from multipledispatch import dispatch
import json
import dbconfig

@dispatch()
def get_data():
    #Conect MySQL
    import mysql.connector
    import pandas as pd

    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE
    )

    #print(mydb)

    df_comments = pd.read_sql('select * from comments;', con=mydb)  #comments debe tener asociado el id del proyecto
    #print(df_comments.head())
    
    return df_comments

@dispatch(int)
def get_data(id_proyect):
    #Conect MySQL
    import mysql.connector
    import pandas as pd

    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE
    )

    #print(mydb)

    df_comments = pd.read_sql('select * from comments WHERE project_id =' +str(id_proyect)+ ' ;', con=mydb)  #comments debe tener asociado el id del proyecto
    #print(df_comments.head())
    
    return df_comments
    

#All Comments
def topicmodel_allcoments():
    
    df_comments = get_data()
    
    pattern = r"http\S+"
    #df['TEXTO'] = df['TEXTO'].str.replace(pattern,'')
    
    df_comments['body'] = df_comments['body'].str.replace(pattern,'')

    df2 = df_comments.body
    df2 = df2.str.lower()
    pattern = r"@([A-Za-z0-9_]+)"
    df2 = df2.str.replace(pattern, '')
    #pattern = r"\b(word1|word2|word3|word4|word5|word|etc)\b"
    #df2 = df2.str.replace(pattern,'')

    elements = np.array(df2.tolist())
    tokenizer = RegexpTokenizer(r'\w+')
    es_stop = get_stop_words('es')
    p_stemmer = PorterStemmer()
    texts = []
    for i in elements:
        raw = i.lower()
        tokens = tokenizer.tokenize(raw)
        # remove stop words from tokens
        stopped_tokens = [i for i in tokens if not i in es_stop]
        #stemmed_tokens = [p_stemmer.stem(i) for i in stopped_tokens]
        texts.append(stopped_tokens)
        #texts.append(stemmed_tokens)

    dictionary = corpora.Dictionary(texts)
    corpus = [dictionary.doc2bow(text) for text in texts]
    ldamodel = gensim.models.ldamodel.LdaModel(corpus, num_topics=5, id2word = dictionary, passes=20)
    import pyLDAvis.gensim
    import pyLDAvis

    vis_data = pyLDAvis.gensim.prepare(ldamodel, corpus, dictionary)
    pyLDAvis.display(vis_data)
    
    #return pyLDAvis.save_json(vis_data, 'TopicModel_allcomments.json')
    return pyLDAvis.json.dumps(vis_data)

def topicmodel_forproyect(id_proyect):
    
    df_comments = get_data(id_proyect)
    #list_mask=np.unique(df_comments.project_id)
    
    #mask = df_comments["project_id"] == id_proyect

    #df2 = pd.read_excel("datos_congresista_virtual.xlsx", sheet_name="clasificaciones")
    num_topics = 5

    df2 = df_comments.body
    df2 = df2.str.lower()
    pattern = r"@([A-Za-z0-9_]+)"
    df2 = df2.str.replace(pattern, '')

    elements = np.array(df2.tolist())
    tokenizer = RegexpTokenizer(r'\w+')
    es_stop = get_stop_words('es')
    p_stemmer = PorterStemmer()
    texts = []
    print(str(id_proyect))
    for i in elements:
        raw = i.lower()
        tokens = tokenizer.tokenize(raw)
        # remove stop words from tokens
        stopped_tokens = [i for i in tokens if not i in es_stop]
        #stemmed_tokens = [p_stemmer.stem(i) for i in stopped_tokens]
        texts.append(stopped_tokens)
        #texts.append(stemmed_tokens)
        print(i)

    dictionary = corpora.Dictionary(texts)
    corpus = [dictionary.doc2bow(text) for text in texts]
    #ldamodel = gensim.models.ldamodel.LdaModel(corpus, num_topics=num_topics, id2word = dictionary, passes=20)
    #ldamodel = gensim.models.ldamodel.LdaModel(corpus, num_topics=num_topics, id2word = dictionary, distributed=True, passes=20)
    try:
        ldamodel = gensim.models.ldamulticore.LdaMulticore(corpus, num_topics=num_topics, id2word = dictionary, passes=20)
    except ValueError:
        return "Coleccion Vacia. Aparentemente parametros faltantes o mal ingresados."

    import pyLDAvis.gensim
    import pyLDAvis

    vis_data = pyLDAvis.gensim.prepare(ldamodel, corpus, dictionary)
    pyLDAvis.display(vis_data)

    return pyLDAvis.prepared_data_to_html(vis_data)
    #return pyLDAvis.json.dumps(vis_data)