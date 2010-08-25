#ifndef _TPARSER_H_
#define _TPARSER_H_

#include "main.h"
#include <time.h>

using namespace std ;

class TParser
    {
    public :
    virtual TUCS parse ( TUCS &source ) ;
    
    private :
    virtual void insertTOC ( VTUCS &vs ) ;
    virtual void parse_heading ( TUCS &s ) ;
    virtual bool parse_internal_link ( TUCS &s ) ;
    virtual bool parse_external_link ( TUCS &s ) ;
    virtual bool parse_variables ( TUCS &s ) ;
    virtual void parse_links ( TUCS &s ) ;
    virtual void parse_hr ( TUCS &s ) ;
    virtual TUCS get_bullet_tag ( uint c ) ;
    virtual void parse_bullets ( TUCS &s ) ;
    virtual void parse_single_quotes ( TUCS &s , uint p , TUCS tag ) ;
    virtual void parse_line ( TUCS &s ) ;
    virtual void remove_evil_HTML ( TUCS &s ) ;
    virtual void store_nowiki ( TUCS &s ) ;
    virtual void recall_nowiki ( TUCS &s ) ;
    virtual void replace_variables ( TUCS &s ) ;
    virtual void parse_table_markup ( VTUCS &vs ) ;
    
    // Variables
    TUCS bullets ;
    TUCS nowikistring ;
    VTUCS nowikiitems ;
    bool notoc , hasVariables ;
    bool lastWasPre , lastWasBlank ;
    int external_link_counter ;
    VTUCS toc ;
    uint cur_line , first_header ;
    } ;

#endif

