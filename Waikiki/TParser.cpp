#include "TParser.h"

// This parses the heading markup,
// assuming only lines starting with a '=' are passed
void TParser::parse_heading ( TUCS &s )
    {
    int a ;
    for ( a = 0 ; a < s.length() && s[a] == '=' && s[s.length()-a-1] == '=' ; a++ ) ;
    s = s.substr ( a , s.length() - a*2 ) ;
    s.trim() ;
    TUCS t = "H" + TUCS::fromint ( a ) ;
    s = "<" + t + ">" + s + "</" + t + ">" ;
    }

// This attempts to parse a link
// c initially points to the first '['
uint *TParser::parse_internal_link ( uint *c , TUCS &s , TUCS &t )
    {
    uint *d ;
    bool intext = false ;
    TUCS link , text , trail ;
    for ( d = c+2 ; *d && ( ( *d != '[' && *d != ']' ) || *d != *(d+1) ) ; d++ )
        {
        if ( *d == '|' ) intext = true ;
        else if ( intext ) text += *d ;
        else link += *d ;
        }

    if ( *d != ']' || *(d+1) != ']' ) // Not a valid link
        {
        t += "[[" ;
        return c+2 ;
        }
        
    for ( d += 2 ; TUCS::isChar ( *d ) ; d++ ) trail += *d ;

    if ( !intext ) text = link ;
    text += trail ;
    
    t += SKIN->getInternalLink ( TTitle(link) , text ) ;
    return d ;
    }
    
bool TParser::parse_external_link ( TUCS &s )
    {
    uint a , b , c ;
    for ( a = 0 ; a + 5 < s.length() ; a++ )
        {
        if ( s[a] == '[' )
           {
           if ( s[a+4] == ':' || s[a+5] == ':' )
              {
              c = 0 ;
              for ( b = a ; b < s.length() && ( s[b] != ']' && s[a] != '\n' ) ; b++ )
                 if ( s[b] == ' ' && c == 0 )
                    c = b ;
              if ( b < s.length() && s[b] == ']' )
                 {
                 TUCS text , link ;
                 if ( c == 0 )
                    {
                    text = s.substr ( a+1 , b-a-1 ) ;
                    c = b ;
                    }
                 else text = s.substr ( c+1 , b - c - 1 ) ;
                 link = s.substr ( a+1 , c-a-1 ) ;
                 link = "<a class=external href=\"" + link + "\">" + text + "</a>" ;
                 s.modify ( a , b-a+1 , link ) ;
                 }
              }
           }
        }
    }
    
bool TParser::parse_internal_link ( TUCS &s )
    {
    uint a , b , c ;
    b = s.find ( "]]" ) ;
    if ( b == s.length() ) return false ;
    
    TUCS link , text ;
    c = s.find ( "|" ) ;
    if ( c < b )
        {
        link = s.substr ( 0 , c ) ;
        text = s.substr ( c+1 , b - c - 1 ) ;
        }
    else
        {
        link = s.substr ( 0 , b ) ;
        text = link ;
        }
        
    // Trail
    b += 2 ;
    c = b ;
    while ( c < s.length() && TUCS::isChar ( s[c] ) ) c++ ;
    if ( c > b ) text += s.substr ( b , c - b ) ;

    TTitle t ( link ) ;
    if ( LANG->isLanguageNamespace ( t.getNamespace() ) )
        {
        text = LANG->getLanguageName ( t.getNamespace() ) ;
//        TUCS x = SKIN->getInternalLink ( t , text , "external" ) ;
        TUCS x = "<a class=external href=\"http://" + t.getNamespace() ;
        x += ".wikipedia.org/wiki/" ;
        x += t.getJustTitle() + "\">" + text + "</a>" ;
        OUTPUT->languageLinks.push_back ( x ) ;
        s = s.substr ( c ) ;
        }
    else s = SKIN->getArticleLink ( t , text ) + s.substr ( c ) ;
    
    return true ;
    }

void TParser::parse_links ( TUCS &s )
    {
    int a , b ;
    VTUCS v ;
    s.explode ( "[[" , v ) ;
    s = v[0] ;
    for ( a = 1 ; a < v.size() ; a++ )
        {
        if ( !parse_internal_link ( v[a] ) ) s += "[[" ;
        s += v[a] ;
        }
    parse_external_link ( s ) ;
    }
    
// This will convert the ---- markup into <hr>
// It assumes correct syntax
void TParser::parse_hr ( TUCS &s )
    {
    int a ;
    for ( a = 0 ; a < s.length() && s[a] == '-' ; a++ ) ;
    s = "<hr>" + s.substr ( a ) ;
    }

TUCS TParser::get_bullet_tag ( uint c )
    {
    if ( c == '*' ) return "UL" ;
    if ( c == '#' ) return "OL" ;
    if ( c == ':' ) return "DL" ;
    return "" ;
    }

void TParser::parse_bullets ( TUCS &s )
    {
    TUCS b2 , r ;
    uint a ;
    for ( a = 0 ; a < s.length() && ( s[a] == '*' || s[a] == '#' || s[a] == ':' ) ; a++ )
        b2 += s[a] ;
        
    // Removing bullets chars
    s = s.substr ( a ) ;
    s.trim() ;
    
    // Closing old tags
    while ( bullets.length() > b2.length() )
        r += "</" + get_bullet_tag ( bullets.pop_back() ) + ">" ;
        
    // Closing unmatching tags
    while ( !bullets.empty() && bullets[bullets.length()-1] != b2[bullets.length()-1] )
        r += "</" + get_bullet_tag ( bullets.pop_back() ) + ">" ;
    
    // Opening new tags
    while ( bullets.length() < b2.length() )
        {
        a = b2[bullets.length()] ;
        r += "<" + get_bullet_tag ( a ) + ">" ;
        bullets += a ;
        }
        
    if ( bullets != "" )
        {
        if ( bullets[bullets.length()-1] == ':' ) s = "<dd>" + s + "</dd>" ;
        else s = "<li>" + s + "</li>" ;
        }
    
    s = r + s ;
    }
    
void TParser::parse_single_quotes ( TUCS &s , uint p , TUCS tag )
    {
    uint a , b ;
    for ( a = 0 ; a + p < s.length() ; a++ )
        {
        if ( s[a] == SINGLE_QUOTE &&
             s[a+1] == SINGLE_QUOTE &&
             s[a+p] == SINGLE_QUOTE )
           {
           for ( b = a+p+1 ; b+p < s.length() && (
                      s[b] != SINGLE_QUOTE ||
                      s[b+1] != SINGLE_QUOTE ||
                      s[b+p] != SINGLE_QUOTE ) ; b++ ) ;
           if ( b+p < s.length() )
              {
              while ( b+p < s.length() && s[b+p] == SINGLE_QUOTE ) b++ ;
              s.modify ( b-1 , p+1 , "</" + tag + ">" ) ;
              s.modify ( a , p+1 , "<" + tag + ">" ) ;
              }
           }
        }
    }

// This parses a line of the source
void TParser::parse_line ( TUCS &s )
    {
    TUCS isblank = s ;
    isblank.trim() ;
    if ( isblank.empty() )
        {
        s.clear() ;
        if ( bullets != "" ) parse_bullets ( s ) ;
        if ( s.empty() && !lastWasBlank && !lastWasPre )
           {
           s = "<p>\n" ;
           lastWasBlank = true ;
           }
        return ;
        }
    lastWasBlank = false ;
    
    if ( s[0] == '=' ) parse_heading ( s ) ;
    if ( s.substr ( 0 , 4 ) == "----" ) parse_hr ( s ) ;
    if ( s[0] == ' ' )
        {
        if ( !lastWasPre ) s = "<pre>" + s.substr ( 1 ) ;
        lastWasPre = true ;
        }
    else if ( lastWasPre )
        {
        s = "</pre>\n" + s ;
        lastWasPre = false ;
        }
    
    if ( s[0] == '*' || s[0] == '#' || s[0] == ':' ) parse_bullets ( s ) ;
    else if ( bullets != "" )
        {
        TUCS t ;
        parse_bullets ( t ) ;
        s = t + s ;
        }

    parse_links ( s ) ;
    
    parse_single_quotes ( s , 2 , "STRONG" ) ;
    parse_single_quotes ( s , 1 , "EM" ) ;
    }
    
void TParser::remove_evil_HTML ( TUCS &s )
    {
    }
    
void TParser::replace_variables ( TUCS &s )
    {
    time_t rawtime;
    struct tm * timeinfo;

    time ( &rawtime );
    timeinfo = localtime ( &rawtime );    
    
    TUCS currentday = TUCS::fromint ( timeinfo->tm_mday ) ;
    TUCS currentmonthname = TUCS::fromint ( timeinfo->tm_mon ) ;
    currentmonthname = LANG->getTranslation ( "MonthNames:" + currentmonthname ) ;

    s.replace ( "{{CURRENTDAY}}" , currentday ) ;
    s.replace ( "{{CURRENTMONTHNAME}}" , currentmonthname ) ;
    }
    
void TParser::store_nowiki ( TUCS &s )
    {
    int a ;
    VTUCS v1 , v2 ;
    nowikistring = "   " ;
    nowikistring[0] = 1 ;
    nowikistring[1] = 2 ;
    nowikistring[2] = 3 ;
    nowikiitems.clear() ;
    s.explode ( "<nowiki>" , v1 ) ;
    s = "" ;
    FOREACH ( v1 , a )
        {
        v1[a].explode ( "</nowiki>" , v2 ) ;
        if ( v2.size() > 1 )
           {
           remove_evil_HTML ( v2[0] ) ;
           nowikiitems.push_back ( v2[0] ) ;
           v2[0] = nowikistring ;
           }
        v1[a].implode ( "" , v2 ) ;
        s += v1[a] ;
        }
    }

void TParser::recall_nowiki ( TUCS &s )
    {
    int a ;
    VTUCS v1 ;
    s.explode ( nowikistring , v1 ) ;
    s.clear() ;
    FOREACH ( v1 , a )
        {
        if ( a > 0 ) s += nowikiitems[a-1] ;
        s += v1[a] ;
        }
    }

TUCS TParser::parse ( TUCS &source )
    {
    TUCS r ;
    VTUCS vs ;
    int a ;
    
    OUTPUT->languageLinks.clear() ;
    bullets = "" ;
    hasVariables = false ;
    lastWasPre = false ;
    lastWasBlank = false ;

    store_nowiki ( source ) ;
    if ( source.replace ( "__NOTOC__" , "" ) > 0 ) notoc = true ;
    else notoc = false ;
    remove_evil_HTML ( source ) ;
    replace_variables ( source ) ;
    source.explode ( "\n" , vs ) ;
    FOREACH ( vs , a )
        parse_line ( vs[a] ) ;
        
    FOREACH ( vs , a )
        {
        if ( vs[a] == "" )
           {
           vs.erase ( vs.begin()+a ) ;
           a-- ;
           }
        }
        
    r.implode ( "\n" , vs ) ;

    if ( lastWasPre )
        {
        r += "</pre>" ;
        lastWasPre = false ;
        }
    if ( bullets != "" )
        {
        TUCS s ;
        parse_bullets ( s ) ;
        r += s ;
        }

    recall_nowiki ( r ) ;
    
    return r ;
    }

