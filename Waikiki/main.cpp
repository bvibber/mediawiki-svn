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

    // Parsing command line parameters
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

    if ( loadFromFile )
        {
        cout << "!" << forcetitle.getstring() << endl ;
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




//************************************* MAIN

int main(int argc, char *argv[])
{
    TWikiInterface w ;
    LANG->loadPHP ( "Language.php" ) ;
    w.run ( argc , argv ) ;
//    system("PAUSE");	


    // Convert a MySQL dump imto a sqlite file
//    DB->mysql2sqlite ( ".\\brief_cur_table.sql" , ".\\test.sqlite" ) ;
//    DB->mysql2sqlite ( ".\\20030906_cur_table.sql" , ".\\test.sqlite" ) ;

    return 0;
}

