#include "main.h"
#include <time.h>

class TWikiInterface
    {
    public :
    TWikiInterface () ;
    ~TWikiInterface () ;
    void run (int argc, char *argv[]) ;
    void go ( TUCS s , TArticle &art ) ;
    } ;

TWikiInterface::TWikiInterface ()
    {
    TLanguage::current = new TLanguage ( "EN" ) ;
    TUser::current = new TUser ;
    TOutput::current = new TOutput ;
    DB = new TDatabase ;
    }
    
TWikiInterface::~TWikiInterface ()
    {
    delete TUser::current ;
    delete TOutput::current ;
    delete TLanguage::current ;
    delete DB ;
    }
    
void TWikiInterface::run (int argc, char *argv[])
    {
    TArticle art ;
    
    bool loadFromFile = false ;
    string sourcefile , destfile ;
    TUCS forcetitle ;
    
    TUCS s ;
    TUCS action = "view" ;

    // Parsing command line parameters
    int a ;
    for ( a = 1 ; a < argc ; a++ )
        {
        s = argv[a] ;
        VTUCS v ;
        s.explode ( "=" , v ) ;
        TUCS key = v[0] ;
        v.erase ( v.begin() , v.begin()+1 ) ;
        s.implode ( "=" , v ) ;
        s.trim() ;
        key.toupper () ;
        if ( key == "-SOURCEFILE" )
           {
           sourcefile = s.getstring() ;
           loadFromFile = true ;
           delete DB ;
           DB = new TDatabaseFile () ;
           DB->init ( sourcefile ) ;
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
        else if ( key == "-ACTION" )
           {
           action = s ;
           action.toupper() ;
           }
        else if ( key == "-MYSQL2SQLITE" )
           {
           VTUCS v ;
           s.explode ( "." , v ) ;
           v.pop_back () ;
           v.push_back ( "sqlite" ) ;
           TUCS t ;
           t.implode ( "." , v ) ;
           DB->mysql2sqlite ( s.getstring() , t.getstring() ) ;
           exit ( 0 ) ;
//           DB->mysql2sqlite ( ".\\brief_cur_table.sql" , ".\\test.sqlite" ) ;
           }
        else if ( key == "-REDIRECT" )
           {
           s.toupper() ;
           if ( s == "NO" ) art.allowRedirect = false ;
           }
        else if ( key == "-SQLITE" )
           {
           sourcefile = s.getstring() ;
           loadFromFile = true ;
           delete DB ;
           DB = new TDatabaseSqlite () ;
           DB->init ( sourcefile ) ;
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

    if ( action == "GO" )
        {
        go ( forcetitle , art ) ;
        }
    else if ( loadFromFile ) // View
        {
        DB->getArticle ( TTitle ( forcetitle, FROM_TEXT ) , art ) ;
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
        art.setTitle ( TTitle ( forcetitle, FROM_TEXT ) ) ;
        }
        

    SKIN->setArticle ( &art ) ;
    
    TUCS html = SKIN->getArticleHTML() ;
    
    SKIN->doHeaderStuff () ;
    OUTPUT->addHTML ( "<div id='content'>\n" ) ;
    OUTPUT->addHTML ( SKIN->getTopBar() ) ;
    OUTPUT->addHTML ( html ) ;
    OUTPUT->addHTML ( SKIN->getSideBar() ) ;
    OUTPUT->addHTML ( "</div>\n" ) ;

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

void TWikiInterface::go ( TUCS s , TArticle &art )
    {
    art.setTitle ( TTitle ( "Searching..." ) ) ;
    
    VTUCS bytitle , bytext ;
    DB->findArticles ( s , bytitle , bytext ) ;
    
    uint a ;
    for ( a = 0 ; a < bytitle.size() ; a++ )
        {
        TTitle t ( bytitle[a] ) ;
        bytitle[a] = SKIN->getInternalLink ( t ) ;
        }
    
    TUCS t ;
    t.implode ( "<br>\n" , bytitle ) ;
    
    art.setSource ( t ) ;
    }


//*****************************************************




//************************************* MAIN

int main(int argc, char *argv[])
{
    TWikiInterface w ;
    LANG->loadPHP ( "Language.php" ) ;
    w.run ( argc , argv ) ;

/*    TUCS s ( "A1B1C1" ) ;
    VTUCS v ;
    s.explode ( "1" , v ) ;
    for ( int a = 0 ; a < v.size() ; a++ ) cout << a << ":" << v[a].getstring() << endl ;*/
//    system("PAUSE");	


    // Convert a MySQL dump imto a sqlite file
//    DB->mysql2sqlite ( ".\\brief_cur_table.sql" , ".\\test.sqlite" ) ;
//    DB->mysql2sqlite ( ".\\20030906_cur_table.sql" , ".\\test.sqlite" ) ;

    return 0;
}

