#include "TDatabase.h"

TDatabase* TDatabase::current = NULL ;

// TDatabase

bool TDatabase::init ( string s1 ) { return false ; } ;
void TDatabase::getArticle ( TTitle t , TArticle &art ) { } ;

void TDatabase::filterBackslashes ( TUCS &s )
    {
    TUCS x = "\\ " ;
    x[1] = DOUBLE_QUOTE ;
    s.replace ( "\\n" , "\n" ) ;
    s.replace ( "\\'" , "'" ) ;
    s.replace ( x , '"' ) ;
    s.replace ( "\\r" , "" ) ;
    }

void TDatabase::mysql2sqlite ( string fn_in , string fn_out )
    {
    long l ;
    ifstream in ( fn_in.c_str() , ios::in | ios::binary ) ;
    in.seekg (0, ios::end);
    l = in.tellg();
    in.seekg (0, ios::beg);    
    char *t = new char[l+5] ;
    in.read ( t , l ) ;
    in.close () ;
    t[l] = 0 ;
    
    string cur ;
    sqlite *db = sqlite_open ( fn_out.c_str() , 0 , NULL ) ;
    sqlite_exec ( db , "BEGIN;" , 0 , 0 , 0 ) ;
    
    
    vector <char*> vc ;
    char *c ;
    vc.push_back ( t ) ;
    for ( c = t ; *c ; c++ )
        {
        if ( *c == '\n' )
           {
           *c = 0 ;
           vc.push_back ( c+1 ) ;
           }
        }
    
    
    // All lines now indexed in vc
    cout << vc.size() << " lines\n" ;
    
    TUCS unique = " " ;
    unique[0] = 1 ;
    
    VTUCS index , keys ;
    TUCS table_name ;
    vector <string> values ;
    uint a , b ;
    bool creating = false ;
    for ( a = 0 ; a < vc.size() ; a++ )
        {
        cout << a << endl ;
        char *x = vc[a] ;
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
              sqlite_exec ( db , cur.c_str() , 0 , 0 , 0 ) ;
//              cout << cur << endl ;
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
        else if ( *x == 'I' )
           {
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
    
    delete t ;
    sqlite_exec ( db , "COMMIT;" , 0 , 0 , 0 ) ;
    sqlite_close ( db ) ;
    system("PAUSE");	
    }


// *****************************************************************************
// TDatabaseFile


bool TDatabaseFile::init ( string s1 )
    {
    filename = s1 ;
    return true ;
    }
    
void TDatabaseFile::getArticle ( TTitle t , TArticle &art )
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

void TDatabaseSqlite::getArticle ( TTitle t , TArticle &art )
    {
    st = this ;
    results.clean() ;
    db = sqlite_open ( filename.c_str() , 0 , NULL ) ;
    
    cout << t.getNiceTitle().getstring() << endl ;

    string sql ;
    sql = "SELECT * FROM cur WHERE cur_namespace=0 AND cur_title ='" + t.getDBkey().getstring() + "' LIMIT 1" ;
    cout << sql << endl ;
    sqlite_exec ( db , sql.c_str() , callback , 0 , 0 ) ;
    sqlite_close ( db ) ;
    
    
    if ( results.content.size() == 1 )
       {
//       cout << results[0][results["cur_text"]] << endl ;
       TUCS s = results[0][results["cur_text"]] ;
       filterBackslashes ( s ) ;
       art.setSource ( s ) ;
       }
    art.setTitle ( t ) ;
    }

