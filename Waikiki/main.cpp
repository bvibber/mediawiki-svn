#include "main.h"
#include <time.h>

class TWikiInterface
    {
    public :
    TWikiInterface () ;
    ~TWikiInterface () ;
    virtual void run (int argc, char *argv[]) ;
    
    private :
    void readPostParams () ;
    virtual void edit ( TTitle s , TArticle &art ) ;
    virtual void go ( TUCS s , TArticle &art ) ;
    virtual void load_ini ( VTUCS &v ) ;
    TSpecialPages *sp ;
    TUCS html ;
    } ;

TWikiInterface::TWikiInterface ()
    {
    LANG = new TLanguage ( "EN" ) ;
    USER = new TUser ;
    OUTPUT = new TOutput ;
    DB = new TDatabase ;
    sp = new TSpecialPages ;
    }
    
TWikiInterface::~TWikiInterface ()
    {
    delete USER ;
    delete OUTPUT ;
    delete DB ;
    delete sp ;
    delete LANG ;
    }
    
void TWikiInterface::load_ini ( VTUCS &v )
    {
    ifstream in ( "waikiki.ini" , ios::in ) ;
    while ( in.good() )
        {
        string s ;
        getline ( in , s ) ;
        v.insert ( v.begin() , TUCS ( s ) ) ;
        }
    }
    
void TWikiInterface::readPostParams ()
    {
    char *cl ;
    cl = getenv ( "CONTENT_LENGTH" ) ;
    int cli = cl ? atoi ( cl ) : 0 ;

    if ( cli > 0 ) // POST
       {
       char *ip = new char[cli+5] ;
       int tmp , i = 0;
       while (i < cli) {
             tmp = fgetc(stdin);
             if (tmp==EOF) break;
             ip[i++] = tmp;
       }
       ip[i] = '\0';

       TUCS x = ip ;
       delete ip ;
       
       VTUCS vv ;
       x.explode ( "&" , vv ) ;
       for ( uint b = 0 ; b < vv.size() ; b++ )
          {
          VTUCS zz ;
          vv[b].explode ( "=" , zz ) ;
          TUCS k2 = "_" + zz[0] , v2 ;
          zz.erase ( zz.begin() ) ;
          v2.implode ( "=" , zz ) ;
          LANG->setData ( k2 , v2 ) ;
//          cout << k2.getstring() << " = " << v2.getstring() << "<br>" << endl ;
          }
       }
    }
    
void TWikiInterface::run (int argc, char *argv[])
    {
    int a ;
    VTUCS params ;
    if ( argc == 1 )
        {
        readPostParams () ;

        char *qs = getenv ( "QUERY_STRING" ) ;
        if ( qs )
           {
           TUCS x = qs ;
           x.explode ( "&" , params ) ;
           }
        }
    else
        {
        for ( a = 1 ; a < argc ; a++ ) params.push_back ( argv[a] ) ;
        }
        
    if ( params.size() == 0 )
        {
        cout << "No parameters. Goodbye." << endl ;
        return ;
        }

    load_ini ( params ) ; // Imports default settings from ini
    
    TArticle art ;
    
    bool loadFromFile = false ;
    string sourcefile , destfile ;
    TUCS forcetitle ;
    
    TUCS s ;
    TUCS action = "VIEW" ;
    
    // Default settings
    LANG->setData ( "skinpath" , "wiki" ) ;

    // Parsing command line parameters
    for ( a = 0 ; a < params.size() ; a++ )
        {
        s = params[a] ;
        VTUCS v ;
        s.explode ( "=" , v ) ;
        TUCS key = v[0] ;
        v.erase ( v.begin() , v.begin()+1 ) ;
        s.implode ( "=" , v ) ;
        s.trim() ;
        key.toupper () ;
        key.replace ( "-" , "" ) ;
        if ( key == "SOURCEFILE" )
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
        else if ( key == "USEONLINEIMAGES" )
           {
           s.toupper () ;
           s.trim() ;
           LANG->setData ( "USEONLINEIMAGES" , s ) ;
           }
        else if ( key == "SEARCH" )
           {
           forcetitle = s ;
           forcetitle.fromURL () ;
           }
        else if ( key == "GO" && s != "" )
           {
           action = "GO" ;
           }
        else if ( key == "ACTION" )
           {
           action = s ;
           action.toupper() ;
           }
        else if ( key == "PHP2C" )
           {
           LANG->loadPHP ( "Language.php" ) ;
           LANG->dumpCfile () ;
           exit ( 0 ) ;
           }
        else if ( key == "MYSQL2SQLITE" )
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
        else if ( key == "REDIRECT" )
           {
           s.toupper() ;
           if ( s == "NO" ) art.allowRedirect = false ;
           }
        else if ( key == "SQLITE" )
           {
           sourcefile = s.getstring() ;
           loadFromFile = true ;
           delete DB ;
           DB = new TDatabaseSqlite () ;
           DB->init ( sourcefile ) ;
           }
        else if ( key == "TITLE" )
           {
           forcetitle = s ;
           forcetitle.fromURL () ;
           }
        else if ( key == "DESTFILE" )
           {
           destfile = s.getstring() ;
           }
        else if ( key == "SKIN" )
           {
           USER->setSkin ( s ) ;
           }
        else if ( !key.empty() )
           {
           LANG->setData ( key , s ) ;
           }
        else
           {
//           cout << "Illegal command line parameter :" << endl ;
//           cout << key.getstring() << "=" << s.getstring() << endl ;
           }
        }

    LANG->setData ( "ACTION" , action ) ;

    TTitle ft ( forcetitle , FROM_TEXT ) ;

    if ( action == "GO" )
        {
        go ( forcetitle , art ) ;
        }
    else if ( ft.getNamespaceID() == -1 ) // Special page
        {
        sp->render ( ft.getJustTitle() , art ) ;
        }
    else if ( loadFromFile )
        {
        SKIN->setArticle ( &art ) ;
        if ( action == "VIEW" )
           {
           DB->getArticle ( ft , art ) ;
           html = SKIN->getArticleHTML() ;
           }
        else if ( action == "EDIT" || action == "SUBMIT" )
           {
           DB->getArticle ( ft , art ) ;
           html = SKIN->getEditHTML() ;
           }
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
        art.setTitle ( ft ) ;
        SKIN->setArticle ( &art ) ;
        html = SKIN->getArticleHTML() ;
        }
        

    clock_t start = clock () ;
    html += "\n<!-- Rendering took " ;
    html += TUCS::fromint ( (clock()-start)*100/CLK_TCK ) ;
    html += " ms -->" ;
    
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
    
void TWikiInterface::edit ( TTitle s , TArticle &art )
    {
    DB->getArticle ( s , art ) ;
    html = SKIN->getArticleHTML() ;
    }


//*****************************************************




//************************************* MAIN

int main(int argc, char *argv[])
{
    cout << "Content-type: text/html\n\n" ;
    TWikiInterface w ;
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

