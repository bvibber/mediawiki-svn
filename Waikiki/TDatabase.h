#ifndef _TDATABASE_H_
#define _TDATABASE_H_

#include "main.h"

#ifdef WINDOWS
#include "win_sqlite.h"
#endif

class TArticle ;

typedef vector <string> TVS ;

class TSQLresult
    {
    public:
    TVS field ;
    vector <TVS> content ;
    virtual void clean()
        {
        while ( field.size() ) field.pop_back() ;
        while ( content.size() ) content.pop_back() ;
        }
    virtual int cols () { return field.size() ; }
    virtual int rows () { return content.size() ; }
    virtual string item ( char *s , int i ) ;
    virtual TVS & operator [] ( int i )
        {
        return content[i] ;
        }
    virtual int operator [] ( char *s ) ;
    } ;
    

class TDatabase
    {
    public :
    // Dummy methods
    virtual bool init ( string s1 ) ;
    virtual void storeArticle ( TArticle &art , bool makeOldVersion = true ) ;
    virtual void getArticle ( TTitle t , TArticle &art , bool wasRedirected = false ) ;
    virtual void getRandomArticle ( TArticle &art ) ;
    virtual bool doesArticleExist ( TTitle &t ) ;
    virtual void findArticles ( TUCS s , VTUCS &bytitle , VTUCS &bytext ) ;
    virtual void query ( TUCS s ) ;
    virtual string identify () { return "BASETYPE" ; }
    
    // Useful methods
    virtual void mysql2sqlite ( string fn_in , string fn_out ) ;
    virtual int getNumberOfArticles() ;
    
    static TDatabase *current ;
    
    protected :
    virtual void addKeyValue ( TUCS &s1 , TUCS &s2 , TUCS t1 , TUCS t2 ) ;
    virtual void filterBackslashes ( TUCS &s ) ;
    } ;
    
class TDatabaseFile : public TDatabase
    {
    public :
    virtual bool init ( string s1 ) ;
    virtual void getArticle ( TTitle t , TArticle &art , bool wasRedirected = false ) ;
    virtual string identify () { return "FILE" ; }
    
    private :
    string filename ;
    } ;

class TDatabaseSqlite : public TDatabase
    {
    public :
    virtual bool init ( string s1 ) ;
    virtual void storeArticle ( TArticle &art , bool makeOldVersion = true ) ;
    virtual void getArticle ( TTitle t , TArticle &art , bool wasRedirected = false ) ;
    virtual void getRandomArticle ( TArticle &art ) ;
    virtual bool doesArticleExist ( TTitle &t ) ;
    virtual void findArticles ( TUCS s , VTUCS &bytitle , VTUCS &bytext ) ;
    virtual void query ( TUCS s ) ;
    virtual string identify () { return "SQLITE" ; }
    virtual int getNumberOfArticles() ;

    TSQLresult results ;
    
    protected :
    string filename ;
    sqlite *db ;
    } ;

class TDatabaseMySQL : public TDatabase
    {
    public :
    virtual string identify () { return "MYSQL" ; }
    } ;

#endif

