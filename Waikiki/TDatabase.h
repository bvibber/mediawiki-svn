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
    virtual void getArticle ( TTitle t , TArticle &art ) ;

    // Useful methods
    void mysql2sqlite ( string fn_in , string fn_out ) ;
    
    static TDatabase *current ;
    
    protected :
    void filterBackslashes ( TUCS &s ) ;
    } ;
    
class TDatabaseFile : public TDatabase
    {
    public :
    virtual bool init ( string s1 ) ;
    virtual void getArticle ( TTitle t , TArticle &art ) ;
    
    private :
    string filename ;
    } ;

class TDatabaseSqlite : public TDatabase
    {
    public :
    virtual bool init ( string s1 ) ;
    virtual void getArticle ( TTitle t , TArticle &art ) ;

    TSQLresult results ;
    
    private :
    string filename ;
    sqlite *db ;
    } ;

class TDatabaseMySQL : public TDatabase
    {
    public :
    } ;

#endif

