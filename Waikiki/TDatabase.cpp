#include "TDatabase.h"

TDatabase* TDatabase::current = NULL ;

// TDatabase

bool TDatabase::init ( string s1 ) { return false ; }
void TDatabase::getArticle ( TTitle t , TArticle &art , bool wasRedirected ) { }
void TDatabase::getRandomArticle ( TArticle &art ) {}
bool TDatabase::doesArticleExist ( TTitle &t ) { return false ; }
void TDatabase::query ( TUCS s ) {}
void TDatabase::findArticles ( TUCS s , VTUCS &bytitle , VTUCS &bytext ) { }
void TDatabase::storeArticle ( TArticle &art , bool makeOldVersion ) { }

void TDatabase::addKeyValue ( TUCS &s1 , TUCS &s2 , TUCS t1 , TUCS t2 )
    {
    if ( s1 != "" )
        {
        s1 += "," ;
        s2 += "," ;
        }
    t1.replace ( "'" , "''" ) ; // For sqlite
    t2.replace ( "'" , "''" ) ; // For sqlite
//    s1 += "'" + t1 + "'" ;
    s1 += t1 ;
    
    uint a ;
    for ( a = 0 ; a < t2.length() && t2.isDigit(t2[a]) ; a++ ) ;
    if ( t2 == "NULL" || a == t2.length() ) s2 += t2 ;
    else s2 += "'" + t2 + "'" ;
    }

void TDatabase::filterBackslashes ( TUCS &s )
    {
    TUCS x = "\\ " ;
    x[1] = DOUBLE_QUOTE ;
    s.replace ( "\\n" , "\n" ) ;
    s.replace ( "\\'" , "'" ) ;
    s.replace ( x , '"' ) ;
    s.replace ( "\\r" , "" ) ;
    s.replace ( "\\m" , "" ) ;
    s.replace ( "\\\\" , "\\" ) ;
    }

void TDatabase::mysql2sqlite ( string fn_in , string fn_out )
    {
    string cur ;
    sqlite *db = sqlite_open ( fn_out.c_str() , 0 , NULL ) ;
    sqlite_exec ( db , "BEGIN;" , 0 , 0 , 0 ) ;
    ifstream in ( fn_in.c_str() , ios::in | ios::binary ) ;
    
    VTUCS index , keys ;
    TUCS table_name ;
    vector <string> values ;
    uint a , b ;
    
    string create_indices ;
    
    time_t start = time ( NULL ) ;
    
    bool creating = false ;
    for ( a = 0 ; !in.eof() ; a++ )
        {
        cout << "Converting line " << a << endl ;

        string vc ;
        getline ( in , vc ) ;
        if ( vc == "" ) continue ;
        char *x = (char*) vc.c_str() ;


        if ( *x == '-' && *(x+1) == '-' ) continue ;
        else if ( *x == '/' && *(x+1) == '*' ) continue ;
        else if ( *x == 'C' )
           {
           TUCS s ( x ) ; s.trim() ;
           creating = true ;
           s += " " ;
           s.replace ( "CREATE" , "" ) ;
           s.replace ( "TABLE" , "" ) ;
           s.replace ( "(" , "" ) ;
           s.trim() ;
           table_name = s ;
           cur = "CREATE TABLE " + s.getstring() + " ( " ;
           }
        else if ( *x == ')' && creating )
           {
           TUCS s ( x ) ; s.trim() ;
           cur += ");" ;
           sqlite_exec ( db , "DROP TABLE cur;" , 0 , 0 , 0 ) ;
           sqlite_exec ( db , cur.c_str() , 0 , 0 , 0 ) ;
           cout << cur << endl ;
           cur = "" ;
           
           for ( b = 0 ; b < keys.size() ; b++ )
              {
              cur = "CREATE INDEX k_" + keys[b].getstring() + " ON " ;
              cur += table_name.getstring() ;
              cur += "(" + keys[b].getstring() + ");" ;
              create_indices += cur ;
              }
           
           creating = false ;
           }
        else if ( creating )
           {
           TUCS s ( x ) ;
           s.trim() ;
           TUCS q = " " + s.getstring() + " " ;
           if ( q.find ( " KEY " ) < q.length() )
              {
              if ( s.substr ( 0 , 3 ) == "KEY" )
                 {
                 VTUCS w ;
                 s.explode ( " " , w ) ;
                 s = w[1] ;
                 keys.push_back ( s ) ;
                 }
              }
           else
              {
              s.trim () ;
              VTUCS x ;
              s.replace ( "binary" , "" ) ;
              s.replace ( " NOT NULL" , "" ) ;
              s.replace ( "unsigned" , "" ) ;
              s.replace ( " integer" , " int" ) ;
              s.replace ( " int" , " integer" ) ;
              s.replace ( "auto_increment" , "PRIMARY KEY" ) ;
              while ( s.replace ( "  " , " " ) > 0 ) ;
              s.trim() ;
              s.pop_back() ;

              if ( index.size() > 0 ) cur += " , " ;
              cur += s.getstring() + "\n" ;

              s.explode ( " " , x ) ;
              index.push_back ( x[0] ) ;
//              cout << "Creating " << s.getstring() << endl ;
              }
           }
        else if ( *x == 'I' ) // INSERT INTO blah blah
           {
//           sqlite_exec ( db , "COMMIT;" , 0 , 0 , 0 ) ;
//           sqlite_exec ( db , "BEGIN;" , 0 , 0 , 0 ) ;
           uint l , b ;
           uint idx = 0 ;
           for ( b = 0 ; x[b] != '(' ; b++ ) ;
           l = b+1 ;
           bool quote = false ;
           for ( b = l ; x[b] && ( quote || x[b] != ';' ) ; b++ )
              {
              if ( x[b] == SINGLE_QUOTE && x[b-1] != '\\' )
                 {
                 quote = !quote ;
                 if ( quote ) l = b+1 ;
                 }
              else if ( ( x[b] == ',' || x[b] == ')' ) && !quote )
                 {
                 char y = x[b] ;
                 x[b] = 0 ;
                 
                 if ( x[b-1] == SINGLE_QUOTE ) x[b-1] = 0 ;               

                 for ( char *z = x+l ; *z ; z++ )
                    if ( *z == '\\' && *(z+1) == SINGLE_QUOTE )
                       *z = SINGLE_QUOTE ;

                 values.push_back ( x+l ) ;

                 idx++ ;
                 l = b+1 ;
                 if ( y == ')' )
                    {
                    cur = "INSERT INTO " + table_name.getstring() + " VALUES ( " ;
                    for ( idx = 0 ; idx < values.size() ; idx++ )
                       {
                       if ( idx > 0 ) cur += "," ;
                       cur += "'" + values[idx] + "'" ;
                       }
                    cur += ");" ;
                    sqlite_exec ( db , cur.c_str() , 0 , 0 , 0 ) ;
                    b++ ;
                    while ( x[b] && x[b+1] && x[b] != '(' ) b++ ;
                    l = b+1 ;
                    values.clear() ;
                    }
                 }
              }
           }
        }
        
    cout << time(NULL)-start << "seconds for conversion" << endl ;
    cout << "Creating indices..." << endl ;
    sqlite_exec ( db , create_indices.c_str() , 0 , 0 , 0 ) ;
    sqlite_exec ( db , "COMMIT;" , 0 , 0 , 0 ) ;
    sqlite_close ( db ) ;
    cout << time(NULL)-start << "seconds total" << endl ;
    system("pause");
    }


// *****************************************************************************
// TDatabaseFile


bool TDatabaseFile::init ( string s1 )
    {
    filename = s1 ;
    return true ;
    }
    
void TDatabaseFile::getArticle ( TTitle t , TArticle &art , bool wasRedirected )
    {
    TUCS source ;
    ifstream in ( filename.c_str() , ios::in ) ;
    while ( !in.eof() )
        {
        char t[10000] ;
        in.getline ( t , sizeof ( t ) ) ;
        if ( !source.empty() ) source += '\n' ;
        source += t ;
        }
    in.close() ;
    art.setTitle ( t ) ;
    art.setSource ( source ) ;
    }
    

// *****************************************************************************
// TDatabaseSqlite

string TSQLresult::item ( char *s , int i )
    {
    int a ;
    string s2 = s ;
    for ( a = 0 ; a < field.size() ; a++ )
        if ( s2 == field[a] )
//       if ( 0 == s2.CmpNoCase ( field[a].c_str() ) )
           return content[i][a] ;
    return "" ;
    }

int TSQLresult::operator [] ( char *s )
    {
    int a ;
    string s2 = s ;
    for ( a = 0 ; a < field.size() ; a++ )
        if ( field[a] == s2 )
//       if ( 0 == s2.CmpNoCase ( field[a].c_str() ) )
           return a ;
    return -1 ;
    }


TDatabaseSqlite *st ;
static int callback (void *NotUsed, int argc, char **argv, char **azColName)
    {
    int i , nf ;
    if ( st->results.content.size() == 0 )
        {
        for(i=0; i<argc; i++)
                st->results.field.push_back ( azColName[i] ) ;
        }
        
    nf = st->results.content.size() ;
    st->results.content.push_back ( TVS() ) ;

    for(i=0; i<argc; i++)
        {
        if ( argv[i] ) st->results.content[nf].push_back( argv[i] ) ;
        else st->results.content[nf].push_back ( "" ) ;
        }
    return 0;
    }

bool TDatabaseSqlite::init ( string s1 )
    {
    filename = s1 ;
    return true ;
    }

void TDatabaseSqlite::getArticle ( TTitle t , TArticle &art , bool wasRedirected )
    {
    string sql ;
    sql = "SELECT * FROM cur WHERE cur_namespace=" ;
    sql += TUCS::fromint ( t.getNamespaceID() ) . getstring() ;
    sql += " AND cur_title ='" ;
    sql += t.getDBkey().getstring() ;
    sql += "' LIMIT 1" ;
    
    query ( sql ) ;
    
    if ( results.content.size() == 1 )
       {
       TUCS s = results[0][results["cur_text"]] ;
       TUCS u = s.substr ( 0 , 9 ) ;
       u.toupper() ;
       filterBackslashes ( s ) ;
       if ( u == "#REDIRECT" && !wasRedirected && art.allowRedirect )
          {
          VTUCS v ;
          s.explode ( "\n" , v ) ;
          s = v[0].substr ( 9 ) ;
          s.replace ( "[[" , "" ) ;
          s.replace ( "]]" , "" ) ;
          s.trim() ;
          art.redirectedFrom = t.getNiceTitle() ;
          return getArticle ( TTitle ( s ) , art , true ) ;
          }
       art.setSource ( s ) ;
       art.id = atoi ( results[0][results["cur_id"]].c_str() ) ;
       }
    art.setTitle ( t ) ;
    }

void TDatabaseSqlite::getRandomArticle ( TArticle &art )
    {
    string sql ;
    sql = "SELECT * FROM cur WHERE cur_namespace=0 AND cur_is_redirect=0 ORDER BY random() LIMIT 1" ;
    
    query ( sql ) ;
    
    if ( results.content.size() == 1 )
       {
       TUCS s = results[0][results["cur_text"]] ;
       filterBackslashes ( s ) ;
       art.setSource ( s ) ;
       }
    art.setTitle ( TTitle ( results[0][results["cur_title"]] ) ) ;
    }

bool TDatabaseSqlite::doesArticleExist ( TTitle &t )
    {
    string sql ;
    sql = "SELECT cur_title FROM cur WHERE cur_namespace=" ;
    sql += TUCS::fromint ( t.getNamespaceID() ) . getstring() ;
    sql += " AND cur_title ='" ;
    sql += t.getDBkey().getstring() ;
    sql += "' LIMIT 1" ;

    query ( sql ) ;

    if ( results.content.size() == 1 ) return true ;
    return false ;
    }
    
void TDatabaseSqlite::findArticles ( TUCS s , VTUCS &bytitle , VTUCS &bytext )
    {
    TUCS sql , t ;
    VTUCS v1 , v2 ;
    uint a ;
    sql = "SELECT cur_namespace,cur_title FROM cur WHERE " ;
    
    s.explode ( " " , v1 ) ;
    for ( a = 0 ; a < v1.size() ; a++ )
        {
        v1[a].trim() ;
        if ( !v1[a].empty() ) v2.push_back ( "cur_title LIKE \"%" + v1[a] + "%\"" ) ;
        }
    t.implode ( " OR " , v2 ) ;
    sql += t ;

    query ( sql ) ;    

    for ( a = 0 ; a < results.content.size() ; a++ )
        {
        t = "NamespaceNames:" ;
        t += results[a][results["cur_namespace"]] ;
        t = LNG(t);
        if ( !t.empty() ) t += ":" ;
        t += results[a][results["cur_title"]] ;
        bytitle.push_back ( t ) ;
        }
        
    }
    
void TDatabaseSqlite::query ( TUCS s )
    {
    st = this ;
    results.clean() ;
    db = sqlite_open ( filename.c_str() , 0 , NULL ) ;
    int error = sqlite_exec ( db , s.getstring().c_str() , callback , 0 , 0 ) ;
    if ( SQLITE_OK != error )
        {
        cout << "SQLITE error " << error << "! Query was :<br>" << endl ;
        cout << s.getstring() << endl ;
        exit ( 0 ) ;
        }
    sqlite_close ( db ) ;    
    }
    
void TDatabaseSqlite::storeArticle ( TArticle &art , bool makeOldVersion )
    {
    TTitle tt = art.getTitle() ;
    TUCS sql , s1 , s2 ;
    TUCS source = art.getSource() ;
    source.replace ( "\\" , "\\\\" ) ;
    source.replace ( "\n" , "\\n" ) ;
    addKeyValue ( s1 , s2 , "cur_text" , source ) ;
    addKeyValue ( s1 , s2 , "cur_title" , tt.getDBkey() ) ;
    addKeyValue ( s1 , s2 , "cur_namespace" , TUCS::fromint ( art.id ) ) ;
    if ( doesArticleExist ( tt ) )
        {
        if ( makeOldVersion )
           {
           }
        sql = "DELETE FROM cur WHERE cur_title='" ;
        sql += tt.getDBkey() ;
        sql += "' AND cur_namespace=" ;
        sql += TUCS::fromint ( tt.getNamespaceID() ) ;
        query ( sql ) ;
//        cout << sql.getstring() << "<br>\n" << endl ;
        addKeyValue ( s1 , s2 , "cur_id" , TUCS::fromint ( art.id ) ) ;
        }
    else
        {
        addKeyValue ( s1 , s2 , "cur_id" , "NULL" ) ;
        }
    sql = "INSERT INTO cur (" + s1 + ") VALUES (" + s2 + ")" ;
//    cout << sql.getstring() << endl ;
    query ( sql ) ;
    }
