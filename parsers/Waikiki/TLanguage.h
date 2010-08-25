#ifndef _TLANGUAGE_H_
#define _TLANGUAGE_H_

#include "TUCS.h"

using namespace std ;

class TLangGroup
    {
    public :
    TUCS name ;
    virtual void addTrans ( TUCS k , TUCS v ) ;
    virtual void setTrans ( TUCS k , TUCS v ) ;
    virtual TUCS getTrans ( TUCS k ) ;
    virtual VTUCS getKeys () ;
    
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
    
    virtual void dumpCfile () ;
    
    string lid ;

    private :
    virtual void initEN () ;
    virtual void initDE () ;
    virtual uint getGroup ( TUCS s ) ;
    vector <TLangGroup> tg ;
    } ;

#endif
