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


@dispatch(int)
def get_data(id_project):
    #Conect MySQL
    import mysql.connector
    import pandas as pd

    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE,
        ssl_disabled=True
    )
    
    df_comments = pd.read_sql('select * from comments WHERE project_id =' +str(id_project)+ ' ;', con=mydb)
    return df_comments



def topicmodel_forproject(id_project):
    
    df_comments = get_data(id_project)
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
    print(str(id_project))
    for i in elements:
        raw = i.lower()
        tokens = tokenizer.tokenize(raw)
        # remove stop words from tokens
        stopped_tokens = [i for i in tokens if not i in es_stop]
        texts.append(stopped_tokens)
        print(i)
    
    dictionary = corpora.Dictionary(texts)
    corpus = [dictionary.doc2bow(text) for text in texts]
    
    try:
        ldamodel = gensim.models.ldamulticore.LdaMulticore(corpus, num_topics=num_topics, id2word = dictionary, passes=20)
    except ValueError:
        return json.dumps({"error": "ANALYTICS_ERR_EMPTY_COLLECTION", "errordescription": "Empty collection found. Unable to continue"})

    import pyLDAvis.gensim
    import pyLDAvis

    vis_data = pyLDAvis.gensim.prepare(ldamodel, corpus, dictionary)
    pyLDAvis.display(vis_data)

    return pyLDAvis.prepared_data_to_html(vis_data)
