#ifndef _TLANGUAGE_H_
#define _TLANGUAGE_H_

#include "TUCS.h"

using namespace std ;

class TOtherLanguages
    {
    public :
    TOtherLanguages ( TUCS a = "" , TUCS b = "" , TUCS c = "" ) ;
    TUCS lang_name , lang_id , lang_url ;
    } ;
    
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
    
    void loadPHP ( string file ) ;
    void fromPHP ( TUCS &s , TUCS y ) ;
    
    TUCS getTranslation ( char *t ) ;
    TUCS getTranslation ( TUCS t ) ;
    
    bool isLanguageNamespace ( TUCS s ) ;
    TUCS getLanguageName ( TUCS s ) ;
    
    TUCS getUCfirst ( TUCS t ) ;
    TUCS getLCfirst ( TUCS t ) ;
    
    private :
    vector <TLangGroup> tg ;
    
    MTUCS translations ;
    vector <TOtherLanguages> other_languages ;
    } ;

#endif
