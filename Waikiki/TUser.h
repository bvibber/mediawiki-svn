#ifndef _TUSER_H_
#define _TUSER_H_

#include "main.h"

using namespace std ;

class TSkin ;
class TArticle ;

class TUser
    {
    public :
    TUser () ;
    virtual ~TUser() ;
    
    virtual TSkin *getSkin() ;
    virtual void setSkin ( TUCS s ) ;
    
    virtual bool isLoggedIn () ;
    virtual bool canEditArticle ( TArticle *art = NULL ) ;
    virtual bool canMoveArticle ( TArticle *art = NULL ) ;
    virtual bool canDeleteArticle ( TArticle *art = NULL ) ;
    virtual bool canProtectArticle ( TArticle *art = NULL ) ;
    virtual TUCS getURLname () ;

    virtual bool wantsTOC () ;

    virtual TUCS getUserPageLink ( TUCS sep = "" ) ;
    virtual TUCS getUserTalkPageLink ( TUCS sep = "" ) ;
    virtual TUCS getLogLink ( TUCS sep = "" ) ;
    virtual TUCS getPreferencesLink ( TUCS sep = "" ) ;
    
    static TUser *current ;
    
    private :
    TSkin *skin ;
    
    TUCS name ;
    } ;


#endif
