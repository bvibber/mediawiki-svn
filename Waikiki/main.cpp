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

void loadMySQLdump ( string filename , vector <TArticle*> &va )
    {
    long l ;
    ifstream in ( filename.c_str() , ios::in | ios::binary ) ;
    in.seekg (0, ios::end);
    l = in.tellg();
    in.seekg (0, ios::beg);    
    char *t = new char[l+5] ;
    in.read ( t , l ) ;
    in.close () ;
    t[l] = 0 ;
    
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
    
    VTUCS index ;
    TUCS table_name ;
    uint a ;
    bool creating = false ;
    for ( a = 0 ; a < vc.size() ; a++ )
        {
        cout << a << endl ;
        TUCS s ( vc[a] ) ;
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
//           cout << "Creating table '" << s.getstring() << "'\n" ;
           }
        else if ( s.substr ( 0 , 1 ) == ")" && creating )
           {
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
              s.trim () ;
              VTUCS x ;
              s.explode ( " " , x ) ;
              index.push_back ( x[0] ) ;
//              cout << "Creating " << x[0].getstring() << endl ;
              }
           }
        else if ( s.substr ( 0 , 12 ) == "INSERT INTO " )
           {
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
                 TUCS t2 = s.substr ( l , b - l ) ;
                 if ( t2[t2.length()-1] == SINGLE_QUOTE ) t2.pop_back() ;

                 if ( idx == 0 ) va.push_back ( new TArticle() ) ;
                 TArticle *da = va[va.size()-1] ;
                 if ( index[idx] == "cur_title" ) da->setTitle ( TTitle ( t2 , FROM_DBKEY ) ) ;
                 if ( index[idx] == "cur_text" ) da->setSource ( t2 ) ;

                 idx++ ;
                 l = b+1 ;
                 if ( s[b] == ')' )
                    {
                    while ( b < s.length() && s[b] != '(' ) b++ ;
                    l = b+1 ;
                    idx = 0 ;
                    }
                 }
              }
           }
        }
    
    delete t ;
//    system("PAUSE");	
    }

void saveSQLITE ( string filename , vector <TArticle*> &va )
    {
    string cur = "CREATE TABLE cur (
    cur_title varchar(255) NOT NULL default '',
    cur_text mediumtext NOT NULL
    );" ;
    
    sqlite *db = sqlite_open ( filename.c_str() , 0 , NULL ) ;
//    sqlite_exec ( db , "BEGIN;" , 0 , 0 , 0 ) ;
    sqlite_exec ( db , "DROP TABLE cur;" , 0 , 0 , 0 ) ;
    sqlite_exec ( db , cur.c_str() , 0 , 0 , 0 ) ;

    uint a ;
    for ( a = 0 ; a < va.size() ; a++ )
        {
        string _ti , _tx ;
        _ti = va[a]->getTitle().getNiceTitle().getstring() ;
        _tx = va[a]->getSource().getstring() ;
        sqlite_exec_printf(db,
                "INSERT INTO cur VALUES('%q','%q');",
                0, 0, 0, _ti.c_str(), _tx.c_str());
        }
//    sqlite_exec ( db , "COMMIT;" , 0 , 0 , 0 ) ;

    sqlite_close ( db ) ;
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
//    loadMySQLdump ( ".\\brief_cur_table.sql" , va ) ;
    loadMySQLdump ( ".\\20030906_cur_table.sql" , va ) ;

    saveSQLITE ( ".\\test.sqlite" , va ) ;
*/

    return 0;
}

