#include "TParser.h"

// This parses the heading markup,
// assuming only lines starting with a '=' are passed
void TParser::parse_heading ( TUCS &s )
    {
    int a ;
    for ( a = 0 ; a < s.length() && s[a] == '=' && s[s.length()-a-1] == '=' ; a++ ) ;
    s = s.substr ( a , s.length() - a*2 ) ;
    s.trim() ;
    if ( USER->wantsTOC() && !notoc )
        {
        if ( first_header == 0 ) first_header = cur_line + 1 ;
        TUCS s2 = s ;
        s2.replace ( "[[" , "" ) ;
        s2.replace ( "]]" , "" ) ;
        s2.replace ( "_" , " " ) ;
        toc.push_back ( TUCS::fromint ( a ) + s2 ) ;
        s = "<a name='" + s2 + "'>" + s + "</a>" ;
        }
    TUCS t = "H" + TUCS::fromint ( a ) ;
    s = "<" + t + ">" + s + "</" + t + ">" ;
    }

bool TParser::parse_external_link ( TUCS &s )
    {
    uint a , b , c ;
    for ( a = 0 ; a + 5 < s.length() ; a++ )
        {
        if ( s[a] == '[' )
           {
           if ( s[a+4] == ':' || s[a+5] == ':' || s[a+7] == ':' )
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
                    text = "[" + TUCS::fromint ( external_link_counter++ ) + "]" ;
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
    return true ; // Why?
    }
    
bool TParser::parse_variables ( TUCS &s )
    {
    uint a , b , c ;
    for ( a = 0 ; a + 2 < s.length() ; a++ )
        {
        if ( s[a] == '{' && s[a+1] == '{' )
           {
           for ( b = a ; b+1 < s.length() && ( ( s[b] != '}' || s[b+1] != '}' ) && s[a] != '\n' ) ; b++ ) ;
           if ( b+1 < s.length() && s[b] == '}' && s[b+1] == '}' )
              {
              TUCS t = s.substr ( a+2 , b-a+1-3 ) ;
              TUCS ns ;
              VTUCS vs ;
              t.explode ( ":" , vs ) ;
              if ( vs.size() == 1 ) t = vs[0] ;
              else
                 {
                 ns = vs[0] ;
                 t = vs[1] ;
                 }

              ns.toupper () ;
              if ( ns == "MSG" )
                 {
                 TArticle ar ;
                 TUCS v = "MediaWiki:" ;
                 v += t ;
                 TTitle tt ( v ) ;
                 if ( DB->doesArticleExist ( tt ) )
                    {
                    DB->getArticle ( tt , ar ) ;
                    t = ar.getSource() ;
                    t.trim () ;
                    }
                 else t = "{{" + ns + ":" + t + "}}" ;
                 }

              s.modify ( a , b-a+2 , t ) ;
              }
           }
        }
    return true ; // Why?
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
        
    link.replace ( "_" , " " ) ;
    while ( link.replace ( "  " , " " ) ) ;
        
    // Trail
    b += 2 ;
    c = b ;
    while ( c < s.length() && TUCS::isChar ( s[c] ) ) c++ ;
    if ( c > b ) text += s.substr ( b , c - b ) ;

    TTitle t ( link ) ;
    if ( LANG->isLanguageNamespace ( t.getNamespace() ) ) // Interlanguage link
        {
        text = LANG->getLanguageName ( t.getNamespace() ) ;
        TUCS x = "<a class=external href=\"http://" + t.getNamespace() ;
        x += ".wikipedia.org/wiki/" ;
        x += t.getJustTitle() + "\">" + text + "</a>" ;
        OUTPUT->languageLinks.push_back ( x ) ;
        s = s.substr ( c ) ;
        }
    else if ( t.getNamespaceID() == 6 ) // Image link
        {
        MD5 md ;
        TUCS tt = t.getJustTitle() ;
        
        VTUCS vip ;
        text.explode ( "|" , vip ) ;
        text = vip[vip.size()-1] ;
        vip.pop_back () ;
  
        int pixel = -1 ;
        TUCS align ;
        bool thumbnail = false ;
        while ( vip.size() )
           {
           TUCS param = vip[vip.size()-1] ;
           vip.pop_back () ;
           param.trim () ;
           param.toupper () ;
           if ( param.right(2) == "PX" )
              {
              pixel = atoi ( param.getstring().c_str() ) ;
              }
           else if ( param == "THUMB" )
              {
              thumbnail = true ;
              if ( pixel == -1 ) pixel = 180 ;
              if ( align == "" ) align = "right" ;
              }
           else if ( param == "RIGHT" || param == "LEFT" || param == "CENTER" )
              {
              align = param ;
              }
           }
        
        md.update ( (unsigned char*) tt.getstring().c_str() , tt.length() ) ;
        md.finalize() ;
        string hex = md.hex_digest() ;
        
        TUCS img_param ;
        if ( pixel != -1 )
           {
           img_param += "width='" + TUCS::fromint ( pixel ) + "px' " ;
           }
        if ( !thumbnail && align != "" ) img_param += "align='" + align + "' " ;
        
        TUCS x ;
        if ( LANG->getData ( "USEONLINEIMAGES" ) == "YES" )
           {
           x = "<img border=0 " ;
           x += img_param ;
           x += "src=\"http://" + LANG->lid ;
           x += ".wikipedia.org/upload/" ;
           x += hex[0] ;
           x += "/" ;
           x += hex[0] ;
           x += hex[1] ;
           x += "/" + tt ;
           x += "\" title=\"" + text + "\">" ;
           }
        else if ( LANG->getData ( "IMAGESOURCE" ) != "" )
           {
           x = "<img border=0 " ;
           x += img_param ;
           x += "src=\"" ;
           x += LANG->getData ( "IMAGESOURCE" ) ;
           x += "/" ;
           x += hex[0] ;
           x += "/" ;
           x += hex[0] ;
           x += hex[1] ;
           x += "/" + tt ;
           x += "\" title=\"" + text + "\">" ;
           }
           
        s = SKIN->getArticleLink ( t , x ) + s.substr ( c ) ;

        if ( thumbnail )
           {
           x = "<div class='thumbnail-" + align + "' width='180px'>" ;
           x += s ;
           x += "<br>\n" + text ;
           x += "</div>" ;
           }
        s = x ;
        
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
    
//    parse_variables ( s ) ;
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
    uint a , b ;
    for ( a = 0 ; a+4 < s.length() ; a++ )
        {
        if ( s.substr ( a , 4 ) == "<!--" )
           {
           for ( b = a ; b+4 < s.length() && s.substr ( b , 3 ) != "-->" ; b++ ) ;
           if ( b+4 < s.length() )
              {
              s.modify ( a , b - a + 3 , "" ) ;
              a-- ;
              }
           }
        }
    }
    
void TParser::replace_variables ( TUCS &s )
    {
    time_t rawtime;
    struct tm * timeinfo;

    time ( &rawtime );
    timeinfo = localtime ( &rawtime );    
    
    TUCS currentday = TUCS::fromint ( timeinfo->tm_mday ) ;
    TUCS currentmonthname = TUCS::fromint ( timeinfo->tm_mon ) ;
    TUCS currentyear = TUCS::fromint ( timeinfo->tm_year + 1900 ) ;
    currentmonthname = LANG->getTranslation ( "MonthNames:" + currentmonthname ) ;
    TUCS numberofarticles = TUCS::fromint ( DB->getNumberOfArticles() ) ;

    s.replace ( "{{CURRENTDAY}}" , currentday ) ;
    s.replace ( "{{CURRENTMONTHNAME}}" , currentmonthname ) ;
    s.replace ( "{{CURRENTYEAR}}" , currentyear ) ;
    s.replace ( "{{NUMBEROFARTICLES}}" , numberofarticles ) ;
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
    
void TParser::insertTOC ( VTUCS &vs )
    {
    if ( toc.size() < 3 ) return ;
    if ( !USER->wantsTOC() ) return ;
    uint a , b , cnt[10] ;
    TUCS thetoc ;
    for ( a = 0 ; a < 10 ; a++ ) cnt[a] = 0 ;
    uint level , lastlevel = 1 ;
    FOREACH ( toc , a )
        {
        TUCS s = toc[a] , out ;
        level = s[0] - '1' ;
        s = s.substr ( 1 ) ;
        if ( level > 9 ) continue ;

        cnt[level]++ ;
        for ( b = 1 ; b < level ; b++ )
           if ( b >= 0 && b < 10 && cnt[b] == 0 ) cnt[b] = 1 ;
        for ( b = level + 1 ; b < 10 ; b++ ) cnt[b] = 0 ;
        for ( b = 1 ; b <= level ; b++ )
           {
           if ( !out.empty() ) out += "." ;
           out += TUCS::fromint ( cnt[b] ) ;
           }
        out += " " + s ;
        s.replace ( "'" , "\\'" ) ;
        out = "<a class='internal' href='#" + s + "'>" + out + "</a><br>" ;
        out = "<div style='margin-bottom:0px;'>" + out + "</div>\n" ;
        while ( lastlevel > level ) { out = "</div>" + out ; lastlevel-- ; }
        while ( lastlevel < level ) { out = "<div style='margin-left:2em;'>" + out ; lastlevel++ ; }
        thetoc += out ;
        }
    level = 1 ;
    while ( lastlevel > level ) { thetoc += "</div>" ; lastlevel-- ; }
    
    thetoc = "<table border=0 id=toc><tr><td align=center><b>" +
              LNG("toc") +
              "</b>
              <script type='text/javascript'>showTocToggle('show','hide')</script>
              </td></tr>
              <tr id=tocinside><td align=left>" +
             thetoc +
             "</td></tr></table>" ;

    
    vs.insert ( vs.begin() + first_header - 1 , thetoc ) ;
    }

void TParser::parse_table_markup ( VTUCS &vs )
    {
    uint a , tcnt = 0 ;
    TUCS t ;
    vector <bool> td , tr ; // Is TD / TR open?
    VTUCS last_tab ;
    for ( a = 0 ; a < vs.size() ; a++ )
        {
        if ( vs[a].substr ( 0 , 2 ) == "{|" )
           {
           tcnt++ ;
           t = "<table" ;
           t += vs[a].substr ( 2 ) ;
           t += ">" ;
           vs[a] = t ;
           while ( td.size() < tcnt+1 ) td.push_back ( false ) ;
           while ( tr.size() < tcnt+1 ) tr.push_back ( false ) ;
           while ( last_tab.size() < tcnt+1 ) last_tab.push_back ( "" ) ;
           tr[tcnt] = false ;
           td[tcnt] = false ;
           last_tab[tcnt] = "ERROR" ;
           }
        else if ( tcnt > 0 && vs[a].substr ( 0 , 2 ) == "|}" )
           {
           t = "" ;
           if ( td[tcnt] ) t += "</td>" ;
           if ( tr[tcnt] ) t += "</tr>" ;
           t += "</table>" ;
           t += vs[a].substr ( 2 ) ;
           tcnt-- ;
           vs[a] = t ;
           }
        else if ( tcnt > 0 && ( vs[a].substr ( 0 , 2 ) == "|-" || vs[a].substr ( 0 , 2 ) == "|+" ) )
           {
           uint ch = vs[a][1] ;
           t = "" ;
           if ( td[tcnt] ) t += "</td>" ;
           if ( tr[tcnt] ) t += "</tr>" ;
           if ( ch == '-' ) t += "<tr" ;
           else t += "<caption>" ;
           TUCS u = vs[a].substr ( 2 ) ;
           while ( u != "" && u[0] == '-' && ch == '-' ) u = u.substr ( 1 ) ;
           u.trim () ;
           if ( u != "" ) t += " " ;
           t += u ;
           if ( ch == '-' )
              {
              t += ">" ;
              tr[tcnt] = true ;
              }
           else t += "</caption>" ;
           td[tcnt] = false ;
           vs[a] = t ;
           }
        else if ( tcnt > 0 && ( vs[a][0] == '|' || vs[a][0] == '!' ) )
           {
           VTUCS vt , vu ;
           TUCS tab = "td" ;
           if ( vs[a][0] == '!' ) tab = "th" ;
           if ( vs[a].right ( 2 ) == "||" ) vs[a] += " " ; // Patch!
           vs[a].explode ( "||" , vt ) ;
           uint b ;
           t = "" ;
           if ( !tr[tcnt] ) t += "<tr>" ;
           FOREACH ( vt , b )
              {
              if ( td[tcnt] )
                 {
                 t += "</" ;
                 t += last_tab[tcnt] ;
                 t += ">" ;
                 }
              td[tcnt] = true ;
              last_tab[tcnt] = tab ;
              t += "<" ;
              t += tab ;
              
              uint c ;
              vt[b] = vt[b].substr ( 1 ) ;
              for ( c = 0 ; c < vt[b].length() && vt[b][c] != '|' ; c++ ) ;
              if ( c < vt[b].length() )
                 {
                 t += " " ;
                 TUCS u = vt[b].substr ( 0 , c ) ;
                 u.trim () ;
                 t += u ;
                 vt[b] = vt[b].substr ( c + 1 ) ;
                 }
              t += ">" ;
              t += vt[b] ;
              }
           tr[tcnt] = true ;
           vs[a] = t ;
           }
        }
    }

TUCS TParser::parse ( TUCS &source )
    {
    TUCS r ;
    VTUCS vs ;
    int a ;
    
    OUTPUT->languageLinks.clear() ;
    toc.clear() ;
    bullets = "" ;
    hasVariables = false ;
    lastWasPre = false ;
    lastWasBlank = false ;
    external_link_counter = 1 ;
    first_header = 0 ;

    store_nowiki ( source ) ;
    replace_variables ( source ) ;
    parse_variables ( source ) ;

    if ( source.replace ( "__NOTOC__" , "" ) > 0 ) notoc = true ;
    else notoc = false ;
    
    source.replace ( "__NOEDITSECTION__" , "" ) ; // Not supported anyway
    
    remove_evil_HTML ( source ) ;
    source.explode ( "\n" , vs ) ;
    
    FOREACH ( vs , cur_line )
        parse_line ( vs[cur_line] ) ;
    insertTOC ( vs ) ;
        
    FOREACH ( vs , a )
        {
        if ( vs[a] == "" )
           {
           vs.erase ( vs.begin()+a ) ;
           a-- ;
           }
        }
        
    parse_table_markup ( vs ) ;
        
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

