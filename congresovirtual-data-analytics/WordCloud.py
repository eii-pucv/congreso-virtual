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

#import matplotlib.pyplot as plt

@dispatch()
def get_data():
    #Conect MySQL
    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE
    )

    #print(mydb)
    
    df_comments = pd.read_sql('select * from comments;', con=mydb) 
    
    return df_comments

@dispatch(int)
def get_data(id_proyect):
        #Conect MySQL
    mydb = mysql.connector.connect(
        host=dbconfig.CONGRESO_MYSQL_HOST,
        user=dbconfig.CONGRESO_MYSQL_USER,
        passwd=dbconfig.CONGRESO_MYSQL_PASSWD,
        database=dbconfig.CONGRESO_MYSQL_DATABASE
    )

    #print(mydb)
    
    df_comments = pd.read_sql('select * from comments WHERE project_id =' +str(id_proyect)+ ';', con=mydb) 
    
    return df_comments
    
# Create stopword list:

def list_stopwords(noinclude):
    #stopwords = set(STOPWORDS)
    stop_words_sp = set(stopwords.words('spanish'))
    stop_words_en = set(stopwords.words('english'))
    stop_words = stop_words_sp | stop_words_en
    if len(noinclude)>0:
        stopwords.update(noinclude)
    return stopwords
    
    
# Start with one review:
def tagcloud_proyect(proyect,max_word):
    df_comments = get_data(proyect)
    
    #print(df_comments)
    #print(df_comments.body)
    
    text = " ".join(review for review in df_comments.body)
    #print('texto',text)
    
    # Create and generate a word cloud image:
    # lower max_font_size, change the maximum number of word and lighten the background:

    #stop_words=list_stopwords(words_noinclude)
        
    wordcloud = WordCloud(max_words=max_word).generate(text)
    #print(wordcloud)
    #plt.figure()
    #plt.imshow(wordcloud, interpolation="bilinear")
    #plt.axis("off")
    #plt.show()
    #print(wordcloud)
    response = ["text"]
    response[0] = text
    
    # Save the image in the img folder:
    #wordcloud.to_file("img/wordcloud.png")
    #wordcloud.to_html("img/wordcloud.html")
    return json.dumps(response)

#result = tagcloud_proyect('1457',100,[])

def tagcloud_all(max_word,words_noinclude):
    
    df_comments = get_data()
    text = " ".join(review for review in df_comments.body)
    print ("There are {} words in the combination of all review.".format(len(text)))
    print(text)
    
    stop_words=list_stopwords(words_noinclude)

    # Generate a word cloud image
    wordcloud = WordCloud(stopwords=stop_words, background_color="white").generate(text)

    # Display the generated image:
    # the matplotlib way:
    plt.imshow(wordcloud, interpolation='bilinear')
    plt.axis("off")
    plt.show()

    wordcloud.to_file("img/Todos.png")
    
    return text

#tagcloud_proyect(1,100,[])