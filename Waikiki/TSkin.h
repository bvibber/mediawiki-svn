#ifndef _TSKIN_H_
#define _TSKIN_H_

#include "main.h"

using namespace std ;

class TTitle ;
class TArticle ;

class TSkin
    {
    public :
    TSkin () ;
    virtual void doHeaderStuff () ;
    virtual TUCS getArticleHTML () ;
    virtual TUCS getEditHTML () ;
    virtual TUCS getTopBar() ;
    virtual TUCS getSideBar() ;
    virtual TUCS getImageLink ( TTitle t , TUCS url , TUCS alt = "" ) ;
    virtual TUCS getInternalLink ( TTitle t , TUCS text = "" , TUCS cl = "" , TUCS params = "" ) ;
    virtual TUCS getSpecialLink ( TUCS page , TUCS text = "" , TUCS params = "" ) ;
    virtual TUCS getArticleLink ( TTitle t , TUCS text = "" , TUCS params = "" ) ;
    
    virtual TUCS getEditLink ( TUCS sep = "" ) ;
    virtual TUCS getWatchThisPageLink ( TUCS sep = "" ) ;
    virtual TUCS getMoveLink ( TUCS sep = "" ) ;
    virtual TUCS getDeleteLink ( TUCS sep = "" ) ;
    virtual TUCS getProtectLink ( TUCS sep = "" ) ;
    virtual TUCS getHistoryLink ( TUCS sep = "" ) ;
    virtual TUCS getHelpLink ( TUCS sep = "" ) ;
    
    virtual TArticle *getArticle () ;
    virtual void setArticle ( TArticle *a ) ;
    
    private :
    TArticle *article ;
    } ;

class TSkinStandard : public TSkin
    {
    public :
    } ;

class TSkinBlank : public TSkin
    {
    public :
    void doHeaderStuff () ;
    TUCS getTopBar() ;
    TUCS getSideBar() ;
    } ;

#endif
