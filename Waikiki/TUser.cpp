#include "TUser.h"

TUser* TUser::current = NULL ;

TUser::TUser ()
    {
    skin = new TSkinStandard ;
    name = "Tony Test" ;
    }
    
TUser::~TUser()
    {
    delete skin ;
    }

TSkin *TUser::getSkin()
    {
    return skin ;
    }
    
void TUser::setSkin ( TUCS s )
    {
    s.toupper () ;
    TSkin *newskin = NULL ;
    if ( s == "STANDARD" ) newskin = new TSkinStandard ;
    else if ( s == "DEFAULT" ) newskin = new TSkinStandard ;
    else if ( s == "NONE" ) newskin = new TSkinBlank ;
    else if ( s == "BLANK" ) newskin = new TSkinBlank ;
    
    if ( newskin )
        {
        newskin->setArticle ( skin->getArticle() ) ;
        delete skin ;
        skin = newskin ;
        }
    }
    
    

bool TUser::isLoggedIn ()
    {
    return true ; // DUMMY
    }

bool TUser::canEditArticle ( TArticle *art  )
    {
    if ( art == NULL ) art = SKIN->getArticle() ;
    return true ; // DUMMY
    }

bool TUser::canMoveArticle ( TArticle *art )
    {
    if ( art == NULL ) art = SKIN->getArticle() ;
    return true ; // DUMMY
    }

bool TUser::canDeleteArticle ( TArticle *art )
    {
    if ( art == NULL ) art = SKIN->getArticle() ;
    return true ; // DUMMY
    }

bool TUser::canProtectArticle ( TArticle *art )
    {
    if ( art == NULL ) art = SKIN->getArticle() ;
    return true ; // DUMMY
    }



TUCS TUser::getURLname ()
    {
    return name ; // DUMMY
    }
    
    
// Links


TUCS TUser::getUserPageLink ( TUCS sep )
    {
    if ( !isLoggedIn() ) return "" ;
    return "USER" + sep ;
    }

TUCS TUser::getUserTalkPageLink ( TUCS sep )
    {
    if ( !isLoggedIn() ) return "" ;
    return "USERTALK" + sep ;
    }

TUCS TUser::getLogLink ( TUCS sep )
    {
    if ( isLoggedIn() )
        return "LOGOUT" + sep ;
    return "LOGIN" + sep ;
    }

TUCS TUser::getPreferencesLink ( TUCS sep )
    {
    if ( !isLoggedIn() ) return "" ;
    return "PREFS" + sep ;
    }
    
bool TUser::wantsTOC ()
    {
    return true ; // DUMMY
    }
    
