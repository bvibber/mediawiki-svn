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


//*****************************************************

void makeMySQLdump ( string fn_in , string fn_out )
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
    
    VTUCS index ;
    TUCS table_name ;
    vector <string> values ;
    uint a ;
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
           cur = "" ;
           creating = false ;
           }
        else if ( creating )
           {
           TUCS s ( x ) ; s.trim() ;
           TUCS q = " " + s.getstring() + " " ;
           if ( q.find ( " KEY " ) < q.length() )
              {
              }
           else
              {
              s.trim () ;
              VTUCS x ;
              s.replace ( "binary" , "" ) ;
              s.replace ( "unsigned" , "" ) ;
              s.replace ( " integer" , " int" ) ;
              s.replace ( " int" , " integer" ) ;
              s.replace ( "auto_increment" , "" ) ;
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
    system("PAUSE");	
    }



//************************************* MAIN

int main(int argc, char *argv[])
{
    TWikiInterface w ;
    LANG->loadPHP ( "Language.php" ) ;
    w.run ( argc , argv ) ;
    system("PAUSE");	

/*
    // Convert a MySQL dump imto a sqlite file
    vector <TArticle*> va ;
    makeMySQLdump ( ".\\20030906_cur_table.sql" , ".\\test.sqlite" ) ;
*/
    return 0;
}

