# Author:   Esteban AVILA
#           COEXYA

## 2 Step script
#   1) Add the needed translations in the correct files
#   2) Add the new lines on projeqtor's MySql DB 

from asyncio.windows_events import NULL
import mariadb
import sys



"""

    This is a fuction that starts the connection to the DB and returns the connector

    @param user_name     : data base user name
    @param user_password : data base user password
    @param host_name     : data base host name
    @param db_port       : data base prefered port
    @param db_name       : data base name

    @return conn         : data base connector

    @author Esteban AVILA
            COEXYA

"""
def connectProjeQtOrDB(user_name,user_password,host_name,db_port,db_name):
    # Connect to MariaDB Platform
    try:
        conn = mariadb.connect(
            user=user_name,
            password=user_password,
            host=host_name,
            port=db_port,
            database=db_name
        )
        print("Connected")
    except mariadb.Error as e:
        print(f"Error connecting to MariaDB Platform: {e}")
        sys.exit(1)
    
    return conn



"""

    This function takes in a treated string to comply with the mariaDB functions and inserts the new info into the tsql tables. This only will work with insert commands.

    @param cursor        : connection cursor
    @param connector     : connector
    @param sql_command   : sql command
    @param values        : sql command values

    @author Esteban AVILA
            COEXYA
    
    info DB:  https://mariadb.com/fr/resources/blog/how-to-connect-python-programs-to-mariadb/

"""
def insertCommand(cursor,connector,sql_command,values):
    try:
        cursor.execute(sql_command,values)
        connector.commit()
        print("Query executed successfully")
    except mariadb.Error as e:
        print(f"The error '{e}' occurred")




"""

    This is a fuction that starts the connection to the DB and returns the connector

    @param sqlStr        : string containing many lines of sql INSERT commands

    @return resComm      : table of strings that have the correct syntax to be used in mariaDB commands
    @return resTup       : tuple that contains the values that need to be inserted

    @author Esteban AVILA
            COEXYA

"""
def tableOfCommands(sqlStr):
    
    entrySplit = sqlStr.rsplit(";")
    resComm = []
    resTup = []

    for str in entrySplit:
        if str!= "":
            split = str.rsplit("VALUES ")
            res = split[0] + "VALUES ("

            values = split[1].replace("(","")
            values = values.replace(")","")

            valuesTab = values.rsplit(",")
            count = 0
            for val in valuesTab:
                if (count+1!=len(valuesTab)):
                    res = res + "?,"
                else:
                    res = res + "?)"
                count = count +1
            resComm.append(res)
            # eval() lets us evaluate the type of the variable we are looking at
            resTup.append(eval(values))

    return resComm,resTup

def main():
    print("Initializing Deployment")
    
    ############   1 

    print("Translations being added")
    fileNameFR = "projeqtor/tool/i18n/nls/fr/lang.js"
    fileNameEN = "projeqtor/tool/i18n/nls/lang.js"

    fileNames = [fileNameFR,fileNameEN]

    addedStringFR = ("\",\nreportPlanResourceWeekly:\"Plan de charge projet/ressource par semaine\",\n"
                    "colIncludeThreeMonth:\"3 mois\",\n"
                    "colIncludeSixMonth:\"6 mois\",")
    addedStringEN = ("\",\nreportPlanResourceWeekly:\"Work plan project/respource per Week\",\n"
                    "colIncludeThreeMonth:\"3 months\",\n"
                    "colIncludeSixMonth:\"6 months\",")
    print(".")

    for file in fileNames:
        
        print(".")
        f = open(file,"r",encoding='utf-8')
        fileStr = f.read()
        f.close()
        finalStr = ""   
        count = 0
        
        for a in fileStr:

            if count==len(fileStr)-3:
                if(file == fileNameFR):
                    finalStr = finalStr + addedStringFR
                elif(file == fileNameEN):
                    finalStr = finalStr + addedStringEN
            else:
                finalStr = finalStr + a
            count = count + 1
        
        print(count)

        f = open(file,"w",encoding='utf-8')
        f.write(finalStr)
        f.close()

    print(".")
    print("Translations DONE")
    
    ############    2
    
    ## Create connection PARAMETERS
    host_name = "127.0.0.1"
    user_name = ""
    user_password = ""
    db_name = ""
    db_port = 1

    #Connect to ProjeQtOr's Maria DB
    conn = connectProjeQtOrDB(user_name,user_password,host_name,db_port,db_name)

    f = open("../parametersDeploiement.sql","r",encoding='utf-8')
    fileSql = f.read()
    f.close()

    tableSqlCommands, sqlValues = tableOfCommands(fileSql)
    print(tableSqlCommands)
    print(sqlValues)

    cur = conn.cursor()
    counter = 0
    for command in tableSqlCommands:
        insertCommand(cur,conn,command,sqlValues[counter])
        counter = counter +1

if __name__=="__main__":
    main()
