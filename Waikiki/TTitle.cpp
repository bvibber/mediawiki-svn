#include "TTitle.h"

TTitle::TTitle ( TUCS t , uint source )
    {
    if ( source == FROM_TEXT ) initFromText ( t ) ;
    }
    
void TTitle::initFromText ( TUCS s )
    {
    VTUCS v ;
    s.explode ( ":" , v ) ;
    if ( v.size() == 1 )
        {
        ns = "" ;
        title = s ;
        }
    else
        {
        ns = v[0] ;
        v.erase ( v.begin() , v.begin()+1 ) ;
        title.implode ( ":" , v ) ;
        }
    ns = UC1(ns) ;
    title = UC1(title) ;
    }
    
TUCS TTitle::getNiceTitle ()
    {
    TUCS r ;
    if ( ns != "" ) r = ns + ":" ;
    r += title ;
    r.replace ( "_" , " " ) ;
    return r ;
    }
    
TUCS TTitle::getURL ()
    {
    TUCS s = getNiceTitle() ;
    for ( int a = 0 ; a < s.length() ; a++ )
        if ( s[a] == ' ' ) s[a] = '_' ;
    return s ;
    }

TUCS TTitle::getDBkey ()
    {
    TUCS s = title ;
    s.replace ( " " , "_" ) ;
    return s ;
    }
        
TUCS TTitle::getNamespace ()
    {
    return LANG->getUCfirst ( ns ) ;
    }
    
int TTitle::getNamespaceID ()
    {
    if ( ns == "" ) return 0 ;
    return 0 ; // DUMMY
    }
