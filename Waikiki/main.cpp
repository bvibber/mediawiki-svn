#include "main.h"
#include <time.h>

class TWikiInterface
    {
    public :
    TWikiInterface () ;
    ~TWikiInterface () ;
    void run (int argc, char *argv[]) ;
    } ;

TWikiInterface::TWikiInterface ()
    {
    TLanguage::current = new TLanguage ( "EN" ) ;
    TUser::current = new TUser ;
    TOutput::current = new TOutput ;
    }
    
TWikiInterface::~TWikiInterface ()
    {
    delete TUser::current ;
    delete TOutput::current ;
    delete TLanguage::current ;
    }
    
void TWikiInterface::run (int argc, char *argv[])
    {
    TArticle art ;
    
    bool loadFromFile = false ;
    string sourcefile , destfile ;
    TUCS forcetitle ;

    // CLI
    int a ;
    for ( a = 1 ; a < argc ; a++ )
        {
        TUCS s ( argv[a] ) ;
        VTUCS v ;
        s.explode ( "=" , v ) ;
        TUCS key = v[0] ;
        v.erase ( v.begin() , v.begin()+1 ) ;
        s.implode ( "=" , v ) ;
        key.toupper () ;
        if ( key == "-SOURCEFILE" )
           {
           sourcefile = s.getstring() ;
           loadFromFile = true ;
           if ( forcetitle == "" )
              {
              s.replace ( "\\" , "/" ) ;
              s.explode ( "/" , v ) ;
              s = v[v.size()-1] ;
              v.pop_back () ;
              s.explode ( "." , v ) ;
              v.pop_back () ;
              forcetitle.implode ( "." , v ) ;
              }
           }
        else if ( key == "-TITLE" )
           {
           forcetitle = s ;
           }
        else if ( key == "-DESTFILE" )
           {
           destfile = s.getstring() ;
           }
        else if ( key == "-SKIN" )
           {
           USER->setSkin ( s ) ;
           }
        else
           {
           cout << "Illegal command line parameter :" << endl ;
           cout << key.getstring() << "=" << s.getstring() << endl ;
           }
        }

    if ( loadFromFile )
        {
        art.loadFromFile ( sourcefile.c_str() ) ;
        }
    else
        {
        char t[10000] ;
        TUCS t2 ;
        while ( !cin.eof() )
           {
           cin.getline ( t , sizeof ( t ) ) ;
           t2 += t ;
           t2 += "\n" ;
           }
        art.setSource ( t2 ) ;
        }
        
    art.setTitle ( TTitle ( forcetitle, FROM_TEXT ) ) ;

//    int c1 = clock () ;


//    for ( a = 0 ; a < 10 ; a++ )
        {
//        TOutput::current = new TOutput ;

    SKIN->setArticle ( &art ) ;
    
    TUCS html = SKIN->getArticleHTML() ;
    
    SKIN->doHeaderStuff () ;
    OUTPUT->addHTML ( "<div id='content'>\n" ) ;
    OUTPUT->addHTML ( SKIN->getTopBar() ) ;
    OUTPUT->addHTML ( html ) ;
    OUTPUT->addHTML ( SKIN->getSideBar() ) ;
    OUTPUT->addHTML ( "</div>\n" ) ;
        }

//    c1 = clock() - c1 ;
//    cout << c1 << endl ;
//    system("PAUSE");	

    // Writing    
    if ( destfile != "" )
        {
        ofstream out ( destfile.c_str() , ios::out ) ;
        out << OUTPUT->getPage().getstring() ;
        }
    else
        {
        cout << OUTPUT->getPage().getstring() ;
        }
    }

/*
// Experimental code to convert a MySQL dump to
// the sqlite format. This has little to do with the
// actual parser.
void mysql2sqlite ( string fn_in , string fn_out )
    {
    VTUCS v ;
    long l ;
    ifstream in ( fn_in.c_str() , ios::in | ios::binary ) ;
    in.seekg (0, ios::end);
    l = in.tellg();
    in.seekg (0, ios::beg);    
    char *t = new char[l+5] ;
    in.read ( t , l ) ;
    in.close () ;
    TUCS t2 = t ;
    t2.explode ( "\n" , v ) ;
    t2 = "" ;    
    delete t ;
    
    // All lines now in v
    ofstream out ( fn_out.c_str() , ios::out ) ;
    cout << v.size() << " lines\n" ;
    
    VTUCS index ;
    TUCS table_name ;
    uint a ;
    bool creating = false ;
    bool first = true ;
    for ( a = 0 ; a < v.size() ; a++ )
        {
        TUCS s = v[a] ;
        s.trim() ;
        if ( s.substr ( 0 , 2 ) == "--" || s.substr ( 0 , 2 ) == "/*" ) continue ;
        if ( s.substr ( 0 , 7 ) == "CREATE " )
           {
           creating = true ;
           s += " " ;
           s.replace ( "CREATE" , "" ) ;
           s.replace ( "TABLE" , "" ) ;
           s.replace ( "(" , "" ) ;
           s.trim() ;
           table_name = s ;
           out << "DROP TABLE " << s.getstring() << " ;" << endl ;
           out << "CREATE TABLE " << s.getstring() << " (" << endl ;
           cout << "Creating table '" << s.getstring() << "'\n" ;
           }
        else if ( s.substr ( 0 , 1 ) == ")" && creating )
           {
           out << endl << ") ; " << endl ;
           creating = false ;
           }
        else if ( creating )
           {
           TUCS q = " " + s + " " ;
           if ( q.find ( " KEY " ) < q.length() )
              {
              }
           else
              {
              s.replace ( " integer" , " int" ) ;
              s.replace ( " int" , " integer" ) ;
              s.replace ( "auto_increment" , "PRIMARY KEY" ) ;
              s.replace ( "binary" , "" ) ;
              s.replace ( "unsigned" , "" ) ;
              while ( s.replace ( "  " , " " ) > 0 ) ;
              s.trim () ;
              s.pop_back () ;
              if ( !first ) out << "," << endl ;
              out << s.getstring() ;
              first = false ;
              VTUCS x ;
              s.explode ( " " , x ) ;
              index.push_back ( x[0] ) ;
              cout << "Creating " << s.getstring() << endl ;
              }
           }
        else if ( s.substr ( 0 , 12 ) == "INSERT INTO " )
           {
/*           uint *i ;
           for ( i = s.c_str() ; *i ; i++ )
              {
              if ( *i == SINGLE_QUOTE && *(i-1) == '\\' )
                 {
                 *(i-1) = SINGLE_QUOTE ;
                 }
              }
           out << s.getstring() << endl ;
*/           
           uint l , b ;
           uint idx = 0 ;
           for ( b = 0 ; s[b] != '(' ; b++ ) ;
           l = b+1 ;
           bool quote = false ;
           for ( b = l ; b < s.length() && ( quote || s[b] != ';' ) ; b++ )
              {
              if ( s[b] == SINGLE_QUOTE && s[b-1] != '\\' )
                 {
                 quote = !quote ;
                 if ( quote ) l = b+1 ;
                 }
              else if ( ( s[b] == ',' || s[b] == ')' ) && !quote )
                 {
                 t2 = s.substr ( l , b - l ) ;
                 if ( t2[t2.length()-1] == SINGLE_QUOTE ) t2.pop_back() ;
                 if ( idx == 0 ) out << "INSERT INTO " << table_name.getstring() << " VALUES (" ;
                 else out << ", " ;
                 t2.replace ( "\\'" , "''" ) ;
                 out << "'" << t2.getstring() << "'" ;
                 idx++ ;
                 l = b+1 ;
                 if ( s[b] == ')' )
                    {
                    while ( idx++ < index.size() ) out << ",''" ;
                    out << ") ;" << endl ;
                    while ( b < s.length() && s[b] != '(' ) b++ ;
                    l = b+1 ;
                    idx = 0 ;
                    }
                 }
              }
           }
        }
    
    system("PAUSE");	
    }
*/

//************************************* MAIN

int main(int argc, char *argv[])
{
    TWikiInterface w ;
//    LANG->loadPHP ( "Language.php" ) ;
//    w.run ( argc , argv ) ;
//    system("PAUSE");	
    mysql2sqlite ( "Z:\\brief_cur_table.sql" , "Z:\\test.sqlite" ) ;

    return 0;
}

