#include "TSkin.h"

// This contains the methods of the default skin

TSkin::TSkin ()
    {
    article = NULL ;
    }

void TSkin::doHeaderStuff ()
    {
    OUTPUT->addHeaderLink ( "stylesheet" , LANG->getData("stylepath") + "/wikistandard.css" ) ;
    
    OUTPUT->addHeader ( 
"    
<style type='text/css'><!--
a.stub, a.new, a.internal, a.external { text-decoration: none; }
a.new, #quickbar a.new { color: #CC2200; }
#article { text-align: justify; }
#quickbar { position: absolute; top: 4px; left: 4px; border-right: 1px solid gray; }
#article { margin-left: 152px; margin-right: 4px; }
//--></style>
    " ) ;
    }
    
TUCS TSkin::getArticleHTML ()
    {
    TUCS r ;
    r += "<div id='article'>\n" ;
    if ( article )
        {
        TParser p ;
        r += "<H1 class='pagetitle'>" ;
        r += article->getTitle().getNiceTitle() ;
        r += "</H1>\n" ;
        r += "<P class='subtitle'>" ;
        if ( article->redirectedFrom.empty() ) r += LNG("fromwikipedia") ;
        else
           {
           TUCS u = LNG ( "redirectedfrom" ) ;
           TTitle t2 ( article->redirectedFrom ) ;
           TUCS u2 = getInternalLink ( t2 , "" , "" , "redirect=no" ) ;
           u.replace ( "$1" , u2 ) ;
           r += u ;
           }
        r += "</P>\n" ;
        TUCS s = article->getSource() ;
        r += p.parse ( s ) ;
        }
    r += "</div>\n" ;
    return r ;
    }
    
TUCS TSkin::getTopBar()
    {
    TUCS ll ;
    ll.implode ( " | " , OUTPUT->languageLinks ) ;
    
    TUCS r ;
    r += "<div id='topbar'>\n" ;
    r += "<table width='98%' border='0' cellspacing='0'>" ;
    r += "<tbody>" ;
    
    // Row one
    r += "<tr>" ;
    r += "<td width='152' rowspan='2'> </td>\n" ; // Blank cell
    
    // The usual suspect links
    r += "<td align='left' valign='top'>" ;
    r += getInternalLink ( TTitle ( LNG("mainpage") ) ) + " | " ;
    r += getSpecialLink ( "recentchanges" ) + " | " ;
    r += getEditLink ( " | " ) ;
    r += getHistoryLink ( "<br>\n" ) ;
    r += "Printable etc." ;
    r += "</td>" ;
    
    // User stuff
    r += "<td align='right' valign='top'>" ;
    r += USER->getUserPageLink () ;
    r += " (" + USER->getUserTalkPageLink () + ")<br>\n" ;
    r += USER->getLogLink ( " | " ) ;
    r += USER->getPreferencesLink ( " | " ) ;
    r += getHelpLink ( "<br>\n" ) ;
    
    // Search box
    r += "<form name=\"search\" class=\"inline\" method=\"get\" action=\"./waikiki.exe\">" ;
    r += "<input type=\"text\" name=\"search\" size=\"19\" value=\"\">" ;
    r += "<input type=\"submit\" name=\"go\" value=\"Go\"> " ;
    r += "<input type=\"submit\" value=\"Search\"></form>" ;
    r += "</td>\n" ;
    r += "</tr>" ;
    
    // Row two
    // Language links
    r += "<tr>" ;
    r += "<td colspan=2>" ;
    if ( !ll.empty() ) r += LNG("otherlanguages") + ": " ;
    r += ll ;
    r += "</td>" ;
    r += "</tr>" ;
    r += "</tbody>" ;
    r += "</table>" ;
    r += "</div>\n" ;
    return r ;
    }
    
TUCS TSkin::getSideBar()
    {
    TUCS r ;
    r += "<div id='quickbar'>\n" ;
    r += getImageLink ( TTitle ( "Main Page" ) , LANG->getData("stylepath") + "/wiki.png" ) ;
    r += "<hr class='sep'>\n" ;
    r += getInternalLink ( TTitle ( LNG("mainpage") ) ) ;
    r += "<br>\n" + getSpecialLink ( "recentchanges" ) ;
    r += "<br>\n" + getSpecialLink ( "randompage" ) ;
    
    if ( USER->isLoggedIn() )
        {
        r += "<br>\n" + getSpecialLink ( "watchlist" ) ;
        r += "<br>\n" + getSpecialLink ( "contributions" , "mycontris" , "target="+USER->getURLname() ) ;
        }

    r += "<br>\n" + getInternalLink ( TTitle ( LNG("currentevents") ) ) ;
    r += "<hr class='sep'>\n" ;

    r += getEditLink ( "<br>\n" ) ;
    r += getWatchThisPageLink ( "<br>\n" ) ;
    r += getMoveLink ( "<br>\n" ) ;
    r += getDeleteLink ( "<br>\n" ) ;
    r += getProtectLink ( "<br>\n" ) ;

    // Talk page
/*    r += "
<a href='http://www.wikipedia.org/wiki/Wikipedia_talk:How_to_edit_a_page' class='internal'>Discuss this page</a><br>
    " ;*/

    r += getHistoryLink ( "<br>\n" ) ;
    r += getSpecialLink ( "whatlinkshere" , "" , "target="+article->getTitle().getURL() ) + "<br>\n" ;
    r += getSpecialLink ( "recentchangeslinked" , "" , "target="+article->getTitle().getURL() ) + "<br>\n" ;
    
    r += "<hr class='sep'>\n" ;
    r += getSpecialLink ( "upload" ) ;
    r += "<br>\n" + getSpecialLink ( "specialpages" ) ;
    r += "<br>\n" + getSpecialLink ( "bugreports" ) ;
    r += "</div>\n" ;
    return r ;
    }
    
// Often used links
    
TUCS TSkin::getEditLink ( TUCS sep )
    {
    if ( !USER->canEditArticle() ) return "" ;
    return "<b>" + getInternalLink ( article->getTitle() , LNG("editthispage") , "" , "action=edit" ) + "</b>" + sep ;
    }
    
TUCS TSkin::getWatchThisPageLink ( TUCS sep )
    {
    if ( !USER->isLoggedIn() ) return "" ;
    return getInternalLink ( article->getTitle() , LNG("watchthispage") , "" , "action=watch" ) + sep ;
    }
    
TUCS TSkin::getMoveLink ( TUCS sep )
    {
    if ( !USER->canMoveArticle() ) return "" ;
    return getInternalLink ( article->getTitle() , LNG("movethispage") , "" , "action=move" ) + sep ;
    }
    
TUCS TSkin::getDeleteLink ( TUCS sep )
    {
    if ( !USER->canDeleteArticle() ) return "" ;
    return getInternalLink ( article->getTitle() , LNG("deletethispage") , "" , "action=delete" ) + sep ;
    }
    
TUCS TSkin::getProtectLink ( TUCS sep )
    {
    if ( !USER->canProtectArticle() ) return "" ;
    return getInternalLink ( article->getTitle() , LNG("protectthispage") , "" , "action=protect" ) + sep ;
    }

TUCS TSkin::getHistoryLink ( TUCS sep )
    {
    return getInternalLink ( article->getTitle() , LNG("history") , "" , "action=history" ) + sep ;
    }
        
TUCS TSkin::getHelpLink ( TUCS sep )
    {
    return getInternalLink ( TTitle(LNG("helppage")) , LNG("help") ) + sep ;
    }
        
// Basic link styles
    
TUCS TSkin::getArticleLink ( TTitle t , TUCS text , TUCS params )
    {
    if ( DB->doesArticleExist ( t ) )
        {
        return getInternalLink ( t , text , "" , params ) ;
        }
    else
        {
        if ( params != "" ) params += "&" ;
        params += "action=edit" ;
        return getInternalLink ( t , text , "new" , params ) ;
        }
    }
    
TUCS TSkin::getInternalLink ( TTitle t , TUCS text , TUCS cl , TUCS params )
    {
    TUCS r ;
    if ( text == "" ) text = t.getNiceTitle() ;
    if ( cl == "" ) cl = "internal" ;
    r = t.getURL() ;
    if ( params != "" )
        {
        r = "./waikiki.exe?title=" + r + "&" + params ;
        }
    else
        {
        r = "./waikiki.exe?title=" + r ;
        }
    r = "<a class=" + cl + " href=\"" + r + "\">" + text + "</a>" ;
    return r ;
    }
    
TUCS TSkin::getImageLink ( TTitle t , TUCS url , TUCS alt )
    {
    TUCS r ;
    if ( alt != "" ) alt = " alt=\"" + alt + "\"" ;
    r = "<img src=\"" + url + "\" border=0" + alt + ">" ;
    return getInternalLink ( t , r , "image" ) ;
    }
    
TUCS TSkin::getSpecialLink ( TUCS page , TUCS text , TUCS params )
    {
    TUCS t = LNG("NamespaceNames:-1") + ":" + page ;
    if ( text == "" ) text = page ;
    text = LNG(text) ;
    return getInternalLink ( TTitle ( t ) , text , "" , params ) ;
    }
    
TArticle *TSkin::getArticle ()
    {
    return article ;
    }
    
void TSkin::setArticle ( TArticle *a )
    {
    article = a ;
    }
    
