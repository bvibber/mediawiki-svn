#include "TLanguage.h"

#include <fstream>

using namespace std ;

TLanguage* TLanguage::current = NULL ;

void TLanguage::loadPHP ( string file )
    {
    uint a , b ;
    VTUCS v ;
    TUCS s ;
    ifstream in ( file.c_str() , ios::in ) ;
    char t[10000] ;
    while ( !in.eof() )
        {
        in.getline ( t , sizeof ( t ) ) ;
        s += t ;
        s += "\n" ;
        }
    in.close() ;

    s.explode ( "class Language" , v ) ;
    s = v[0] ;

    TUCS x = " " , y = " " ;
    x[0] = 1 ;
    y[0] = 2 ;
    s.replace ( "\\\"" , x ) ;
    s.replace ( "\"" , y ) ;
    s.replace ( x , "\\\"" ) ;
    
    VTUCS varr , vname ;
    s.explode ( "$wg" , v ) ;
    for ( a = 1 ; a < v.size() ; a++ )
        {
        b = v[a].find ( "=" ) ;
        varr.push_back ( v[a].substr ( b ) ) ;
        
        TUCS z = v[a].substr ( 0 , b ) ;
        while ( !z.empty() && ( z[z.length()-1] < 'A' || z[z.length()-1] > 'Z' ) )
           z.pop_back () ;
        z.pop_back () ;
        vname.push_back ( z ) ;
        }
    
//    ofstream out ( "lang.txt" , ios::out ) ;
    translations.clear() ;
    for ( b = 0 ; b < varr.size() ; b++ )
        {
        VTUCS w ;
        TUCS group = vname[b] ;
        if ( group == "AllMessages" ) group = "" ;
        s = varr[b] ;

        tg.push_back ( TLangGroup() ) ;
        tg[b].name = group ;

        v.clear() ;
        uint l = 0 ;
        bool quote = false ;
        for ( a = 0 ; a < s.length() ; a++ )
           {
           if ( s[a] == 2 ) quote = !quote ;
           else if ( s[a] == ',' && !quote )
              {
              v.push_back ( s.substr ( l , a - l + 1 ) ) ;
              l = a+1 ;
              }
           }
        v.push_back ( s.substr ( l ) ) ;
        
        for ( a = 0 ; a < v.size() ; a++ )
           {
           v[a].explode ( "=>" , w ) ;
           TUCS key , value ;
           if ( w.size() == 1 )
              {
              key = TUCS::fromint ( a ) ;
              value = w[0] ;
              }
           else
              {
              key = w[0] ;
              w.erase ( w.begin() ) ;
              value.implode ( "=>" , w ) ;
              }
           fromPHP ( key , y ) ;
           fromPHP ( value , y ) ;
//           key = group + key ;
           tg[b].setTrans ( key , value ) ;
//           out << key.getstring() << " = " << tg[b].getTrans(key).getstring() << endl ;
           }
//        cout << vname[b].getstring() << endl ;        
        }
        
    }
    
void TLanguage::fromPHP ( TUCS &s , TUCS y )
    {
    if ( s.find ( y ) < s.length() )
        {
        s = s.substr ( s.find ( y ) + 1 ) ;
        s = s.substr ( 0 , s.find ( y ) ) ;
        }
    else
        {
        s.trim() ;
        int a , b = -1 ;
        for ( a = s.length()-1 ; a >= 0 ; a-- )
           {
           if ( s[a] == '_' || s[a] == ' ' || s[a] == '-' ||
                ( s[a] >= '0' && s[a] <= '9' ) ||
                ( s[a] >= 'a' && s[a] <= 'z' ) ||
                ( s[a] >= 'A' && s[a] <= 'Z' ) )
                { }
           else if ( b == -1 ) b = a + 1 ;
           }
        if ( b != -1 ) s = s.substr ( b ) ;
        }
    s.trim() ;
    }

TLanguage::TLanguage ( string l )
    {
    }
    
TUCS TLanguage::getTranslation ( char *t )
    {
    return getTranslation ( TUCS ( t ) ) ;
//    cout << t << " : " << translations[t].getstring() << endl ;
//    return translations[t] ;
    }
    
TUCS TLanguage::getTranslation ( TUCS t )
    {
    uint i ;
    VTUCS x ;
    t.explode ( ":" , x ) ;
    if ( x.size() == 1 ) x.insert ( x.begin() , TUCS("") ) ;
    for ( i = 0 ; i < tg.size() && tg[i].name != x[0] ; i++ )
    if ( i == tg.size() ) return "" ;
//    cout << x[1].getstring() << " = " << tg[i].getTrans ( x[1] ).getstring() << endl ;
    return tg[i].getTrans ( x[1] ) ;
//    return getTranslation ( (char*) t.getstring().c_str() ) ;
    }

TUCS TLanguage::getUCfirst ( TUCS t )
    {
    if ( t.empty() ) return t ;
    if ( t[0] >= 'a' && t[0] <= 'z' ) t[0] = t[0] - 'a' + 'A' ;
    if ( t[0] == 'ä' ) t[0] = 'Ä' ;
    if ( t[0] == 'ö' ) t[0] = 'Ö' ;
    if ( t[0] == 'ü' ) t[0] = 'Ü' ;
    return t ;
    }

TUCS TLanguage::getLCfirst ( TUCS t )
    {
    if ( t.empty() ) return t ;
    if ( t[0] >= 'A' && t[0] <= 'Z' ) t[0] = t[0] - 'A' + 'a' ;
    if ( t[0] == 'Ä' ) t[0] = 'ä' ;
    if ( t[0] == 'Ö' ) t[0] = 'ö' ;
    if ( t[0] == 'Ü' ) t[0] = 'ü' ;
    return t ;
    }

bool TLanguage::isLanguageNamespace ( TUCS s )
    {
    s = "LanguageNames:" + getLCfirst ( s ) ;
//    cout << s.getstring() << " : " << getTranslation ( s ).getstring() << endl ;
    if ( getTranslation ( s ) == "" ) return false ;
//    if ( getLanguageNumber ( s ) == -1 ) return false ;
    return true ;
    }
    
TUCS TLanguage::getLanguageName ( TUCS s )
    {
    s = "LanguageNames:" + getLCfirst ( s )  ;
    return getTranslation ( s ) ;
    /*
    int i = getLanguageNumber ( s ) ;
    if ( i == -1 ) return "" ;
    return other_languages[i].lang_name ;
    */
    }

//*********************************

TOtherLanguages::TOtherLanguages ( TUCS a , TUCS b , TUCS c )
    {
    if ( b.empty() ) b = a ;
    if ( c.empty() ) c = "http://" + a + ".wikipedia.org" ;
    lang_id = a ;
    lang_name = b ;
    lang_url = c ;
    }
    
//************************************

void TLangGroup::setTrans ( TUCS k , TUCS v )
    {
    key.push_back ( k ) ;
    value.push_back ( v ) ;
//    trans[(char*)k.getstring().c_str()] = v ;
    }
    
TUCS TLangGroup::getTrans ( TUCS k )
    {
    uint a ;
    for ( a = 0 ; a < key.size() && k != key[a] ; a++ ) ;
    if ( a == key.size() ) return "" ;
    return value[a] ;
//    return trans[(char*)k.getstring().c_str()] ;
    }

