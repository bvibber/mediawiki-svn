#ifndef _TLANGUAGE_H_
#define _TLANGUAGE_H_

#include "TUCS.h"

using namespace std ;

class TLangGroup
    {
    public :
    TUCS name ;
    void setTrans ( TUCS k , TUCS v ) ;
    TUCS getTrans ( TUCS k ) ;
    
    private :
    VTUCS key , value ;
    MTUCS trans ;
    } ;

class TLanguage
    {
    public :
    TLanguage ( string l = "" ) ;
    static TLanguage *current ;
    
    virtual void loadPHP ( string file ) ;
    virtual void fromPHP ( TUCS &s , TUCS y ) ;
    
    virtual TUCS getTranslation ( char *t ) ;
    virtual TUCS getTranslation ( TUCS t ) ;
    
    virtual bool isLanguageNamespace ( TUCS s ) ;
    virtual TUCS getLanguageName ( TUCS s ) ;
    
    virtual TUCS getUCfirst ( TUCS t ) ;
    virtual TUCS getLCfirst ( TUCS t ) ;
    
    virtual void setData ( TUCS s , TUCS t ) ;
    virtual TUCS getData ( TUCS t ) ;
    
    private :
    virtual uint getGroup ( TUCS s ) ;
    vector <TLangGroup> tg ;
    } ;

#endif
