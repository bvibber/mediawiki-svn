/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#include <wx/wxprec.h>
#ifndef WX_PRECOMP
   #include <wx/wx.h>
#endif

#include "base.h"
//#include "wxWikiServer.h"
#include <wx/wfstream.h>
#include <wx/sstream.h>
#include <wx/datetime.h>
#include <wx/uri.h>

enum
{
    zenomimeTextHtml = 0,
    zenomimeTextPlain,
    zenomimeImageJpeg, 
    zenoMimeImagePng, 
    zenoMimeImageTiff, 
    zenoMimeTextCss, //5
    zenoMimeImageGif, 
    zenoMimeIndex, 
    zenoMimeApplicationJavaScript, 
    zenoMimeImageIcon
} ;

wxWikiServer::wxWikiServer ()
    :wxWebServer()
{
    busy = false ;
}

void wxWikiServer::Browse ( HttpResponse &hr )
{
    wxString pn = GetValue ( _T("n") , _T("A") ) ; // The namespace, I or A
    wxString ps = GetValue ( _T("s") , _T("0") ) ; // Start / offset
    wxString pc = GetValue ( _T("c") , _T("100") ) ; // Count (per page)
    wxString pa = GetValue ( _T("a") , _T("") ) ; // The start
    long ns , nc ;
    ps.ToLong ( &ns ) ;
    pc.ToLong ( &nc ) ;
    wxString start = pn + _T("/") + pa ;
    
    wxString html ;
//    html += start + _T(" / " ) ;
    ZenoFile *main = frame->GetMainPointer() ;
    unsigned long id ;
    if ( pa.IsEmpty() )
    {
        id = 0 ;
        ZenoArticle art ;
        do {
            id++ ;
            art = main->ReadSingleArticle ( id ) ;
        } while ( art.title.Mid(0,2) != _T("A/") ) ;
    } else id = main->GetFirstArticleStartingWith ( start ) ; // Find article starting with "start"
    id = main->SeekArticleRelative ( id , ns ) ; // Add the offset
    wxArrayString titles = main->GetArticleTitles ( id , nc ) ;

	html += FormatList ( titles , 1 , titles.GetCount() , false ) ;
	ReturnHTML ( _T("-/Bl&auml;ttern") , html , hr ) ;
}

void wxWikiServer::SpecialPage (const wxString &page,HttpResponse &hr)
{
    if ( page == _T("random") )
    {
        wxString begin = GetValue ( _T("n") , _T("A") ) ;
        ZenoArticle art = frame->RandomArticle ( begin + _T("/") ) ;
        HandleSimpleGetRequest ( _T("/Wikipedia/") + art.title , hr ) ;
        return ;
    } else if ( page == _T("browse") ) {
        Browse ( hr ) ;
    } else if ( page == _T("search") ) {
		wxURI uri;
		wxString query = Unescape ( GetValue ( _T("e") ) ) ;
//		query.Replace ( _T("+") , _T(" ") ) ;
//		query = uri.Unescape ( query ) ;

		wxString mode ;
		bool fulltext = GetValue ( _T("ft") ) != _T("") ;
		if ( !fulltext ) mode = _T("titles") ;
		else mode = _T("fulltext") ;
		
		wxArrayString titles = Search ( query , mode ) ;
//		wxMessageBox ( wxString::Format ( _T("%d titles") , titles.GetCount() ) ) ;

        // If only one article results, open it directly
        if ( titles.GetCount() == 1 )
        {
            HandleSimpleGetRequest ( _T("/Wikipedia/") + titles[0] , hr ) ;
            return ;
        }

		wxString html ;
        if ( titles.GetCount() > 0 ) html = FormatList ( titles , 1 , 100 , fulltext ) ;
        else if ( !fulltext ) {
            fulltext = true ;
            SpecialPage ( page , hr ) ;
            return ;
        } else {
            html = _T("<h2>Nix gefunden!</h2>") ; // Should rarely be the case...
        }
		ReturnHTML ( _T("-/Suche") , html , hr ) ;
	}
}

wxString wxWikiServer::EscapeURI ( wxString s )
{
    wxString ret ;
    wxURI uri ;
    uri.Create ( s ) ;
    ret = uri.BuildURI() ;
    return ret ;
}

wxString wxWikiServer::GetSearchHeader()
{
    int a ;
    wxString ret ;
    wxString pa = GetValue ( _T("a") , _T("") ) ;
    
    ret += _T("Bl&auml;ttern : ") ;
    for ( a = 'A' - 1 ; a <= 'Z' ; a++ )
    {
        wxString label ;
        if ( a == 'A'-1 ) label = _T("A..Z") ;
        else label += (char) a ;
        wxString link = _T("~/browse?n=A&s=0&c=100") ;
        if ( a != 'A'-1 ) link += _T("&a=") + label ;
        if ( pa == label || ( a == 'A'-1 && pa.IsEmpty() ) )
        {
            ret += _T("<span class=\"z_azact\">") ;
            ret += label ;
            ret += _T("</span>") ;
        } else {
            ret += _T("<a href=\"") + link + _T("\">") + label + _T("</a>") ;
        }
        ret += _T("\n") ;
    }
    ret += _T("<br/>") ;
    
    return ret ;
}

wxString wxWikiServer::GetSearchResultsLink ( wxString title )
{
    wxString ret ;
    wxString nicetitle = GetHTMLtitle ( title.Mid(2) ) ;
    wxString esc = EscapeURI ( title ) ;
    if ( title.Mid(0,2) == _T("I/") )
    {
        wxString img_url = _T("/wikipedia.images/") + esc ;
        ret = _T("<center><a href=\"/Wikipedia/") + esc + _T("\"><img src=\"") + img_url + _T("\"/></a><br/>");
        ret += _T("<a href=\"/Wikipedia/") + esc + _T("\">") + nicetitle + _T("</a></center>");
    } else { // Default; should always be "A/"
        ret = _T("<a href=\"/Wikipedia/") + esc + _T("\">") + nicetitle + _T("</a>");
    }
    return ret ;
}

wxString wxWikiServer::FormatList ( const wxArrayString &titles , int from , int howmany , bool fulltext )
{
    wxString html = GetSearchHeader() ;
    if ( titles.GetCount() == 0 ) return html ;

    if ( from+howmany-1 > titles.GetCount() ) howmany = 1 + titles.GetCount() - from ;
    if ( howmany < 1 ) return html ; // Paranoia

//    if ( fulltext )
    {
//    } else {
        html += _T("<table class=\"z_lemtab\">") ;
        wxArrayString cols[4] ;
        int a , b = 1 ;

        for ( a = 0 ; a < howmany ; a++ )
        {
            int pos = from + a - 1 ;
            if ( pos > titles.GetCount() ) break ;
            wxString s = _T("<td>") + GetSearchResultsLink ( titles[pos] ) + _T("</td>") ;
            cols[a*4/howmany].Add ( s ) ;
        }
        
        // Fill blank cols
        for ( a = 1 ; a < 4 ; a++ )
        {
            while ( cols[a].GetCount() < cols[0].GetCount() )
                cols[a].Add ( _T("<td/>") ) ;
        }
        
        for ( a = 0 ; a < cols[0].GetCount() ; a++ )
        {
            html += _T("<tr>") ;
            for ( b = 0 ; b < 4 ; b++ )
            {
                html += cols[b][a] ;
            }
            html += _T("</tr>\n") ;
        }
        html += _T("</table>") ;
    }
    return html ;
}

wxString wxWikiServer::GetHTMLtitle ( wxString s )
{
    s.Replace ( _T("_") , _T(" ") ) ;
    return s ;
/*    int a ;
    wxString ret ;
    for ( a = 0 ; a < s.length() ; a++ )
    {
        wxChar c = s[a] ;
        if ( c == '_' ) ret += _T(" ") ;
        else if ( c > 127 ) ret += wxString::Format ( _T("&#%d;") , (int)c ) ;
        else ret += c ;
    }
    return ret ;*/
}


void wxWikiServer::HandleSimpleGetRequest(const wxString &page,HttpResponse &hr)
{
//	while ( busy ) wxMilliSleep ( 100 ) ; // De-threading, probably not useful
	busy = true ;
    ZenoArticle art ;
    wxString article = page ;
    if ( article.IsEmpty() || article == _T("/") ) article = _T("/Wikipedia/-/Hauptseite");
    if ( article == _T("/Wikipedia/") ) article += _T("-/Hauptseite") ;
    va = false ; // Versions/Authors
    
    if ( article.Left(11) == _T("/Wikipedia/") ) // Article or the like
    {
        article = article.Mid ( 11 ) ;
        if ( article.Left ( 2 ) == _T("~/") ) // Special page
        {
            article = article.Mid ( 2 ) ;
            article = article.Lower() ;
            SpecialPage ( article , hr ) ;
			busy = false ;
            return ;
        } else { // Normal page
            if ( article.Left ( 1 ) == _T("$") )
            {
                article = article.Mid ( 1 ) ;
                va = true ;
            }
            art = frame->GetPage ( article ) ;
        }
    } else if ( article.Left ( 18 ) == _T("/wikipedia.images/") ) { // Image
        article = article.Mid ( 18 ) ;
        art = frame->GetImage ( article ) ;
    }
    
    if ( !art.ok ) // Paranoia
    {
        hr.SetRC(wxT("404 Not Found"));
        hr.AddHeader(wxT("Content-Type: text/plain; charset=UTF8") );
        hr.AddDataLine( _T("Not found : ") + page );
		busy = false ;
        return ;
    }

/*
	// Cache headers don't seem to work...
	int expire = 15;  // Lebensdauer der Seite im Cache in Minuten
    wxDateTime now = wxDateTime::Now();
	wxString mod_gmt = now.Format ( _T("D, d M Y H:i:s") ) + _T(" GMT") ;
	now += wxTimeSpan ( 0 , expire , 0 , 0 ) ;
	wxString exp_gmt = now.Format ( _T("D, d M Y H:i:s") ) + _T(" GMT") ;

	// HTTP 1.0
	hr.AddHeader ( _T("Expires: ") + exp_gmt ) ;
	hr.AddHeader ( _T("Last-Modified: ") + mod_gmt ) ;

	// HTTP 1.1
	hr.AddHeader ( wxString::Format ( _T("Cache-Control: public, max-age=%d") , expire * 60 ) ) ;



	// Alternate; doesn't work either
    wxDateTime now = wxDateTime::Now();
	now += wxTimeSpan ( 48 , 0 , 0 , 0 ) ;
	wxString exp_gmt = now.Format ( _T("D, d M Y H:i:s") ) + _T(" GMT") ;
	hr.AddHeader ( _T("Expires: ") + exp_gmt ) ;
	hr.AddHeader ( wxString::Format ( _T("Cache-Control: public, max-age=%d") , 24*3600*10 ) ) ;

*/

    switch ( art.rMime )
    {
        case zenomimeTextHtml: ReturnHTML ( article , art , hr ) ; break ;
        case zenomimeTextPlain : ReturnPlainText ( article , art , hr ) ; break ;
        case zenoMimeTextCss : ReturnCSS ( article , art , hr ) ; break ;
        case zenoMimeApplicationJavaScript : ReturnPlainText ( article , art , hr ) ; break ;
        case zenomimeImageJpeg: ReturnBinary ( article , art , hr , _T("image/jpeg") ) ; break ;
        case zenoMimeImagePng:  ReturnBinary ( article , art , hr , _T("image/png") ) ; break ;
        case zenoMimeImageTiff: ReturnBinary ( article , art , hr , _T("image/tiff") ) ; break ;
        case zenoMimeImageGif:  ReturnBinary ( article , art , hr , _T("image/gif") ) ; break ;
        default : hr.SetRC(wxT("404 Not Found"));
    }
	busy = false ;
}

void wxWikiServer::ReturnPlainText ( wxString article , ZenoArticle &art , HttpResponse &hr )
{
    wxString text = art.GetText() ;
    hr.SetRC(wxT("200 OK"));
    hr.AddHeader(wxT("Content-Type: text/plain; charset=UTF8") );
    hr.AddDataLine( text );
}

void wxWikiServer::ReturnCSS ( wxString article , ZenoArticle &art , HttpResponse &hr )
{
    wxString text = art.GetText() ;
    hr.SetRC(wxT("200 OK"));
    hr.AddHeader(wxT("Content-Type: text/css") );
    hr.AddDataLine( text );
}

void wxWikiServer::ReturnHTML ( wxString article , ZenoArticle &art , HttpResponse &hr )
{
    wxString orig_article = article ;
    wxString ns = article.BeforeFirst('/').Upper() ;
    wxString title = article.AfterFirst ( '/' ) ;
    wxString text ;
    if ( va )
    {
        ZenoArticle va_page = frame->GetPage ( orig_article , true ) ;
        text = va_page.GetText() ;
    } else {
        text = art.GetText() ;
    }
    FixLinks ( text ) ;
    ReturnHTML ( orig_article , text , hr ) ;
}

/**
 * This function is needed to fix links from the iso-8859-1 encoded text of the original zeno files
 */
void wxWikiServer::FixLinks ( wxString &text )
{
    wxString nt = text ;
    text.Empty() ;
    wxString key = _T("href=\"A/") ;
    int p = nt.Find ( key ) ;
    while ( p != wxNOT_FOUND )
    {
        text += nt.Mid ( 0 , p ) + key ;
        nt = nt.Mid ( p + key.length() ) ;
        p = nt.Find ( _T("\"") ) ;
        wxString url = nt.Mid ( 0 , p ) ;
        url = EscapeURI ( url ) ;
        text += url ;
        nt = nt.Mid ( p ) ;
        p = nt.Find ( key ) ;
    }
    
    text += nt ;
}

void wxWikiServer::ReturnHTML ( wxString article , wxString text , HttpResponse &hr )
{
    wxString orig_article = article ;
    wxString ns = article.BeforeFirst('/').Upper() ;
    wxString title = article.AfterFirst ( '/' ) ;

    wxString nicetitle = title ;
    title.Replace ( _T(" ") , _T("_") ) ;
    nicetitle.Replace ( _T("_") , _T(" ") ) ;

    hr.SetRC(wxT("200 OK"));
    hr.AddHeader(wxT("Content-Type: text/html; charset=UTF-8") );

    // Load HTML template
    wxFFileInputStream in ( frame->dirbase + _T("base.html") ) ;
    wxStringOutputStream sop ;
    in.Read ( sop ) ;
    
    // Prepare tabs
    wxString tablinks ;
    wxString page_full , page_discussion ;
    wxString name1 , name2 , target ;
    
    if ( ns == _T("-") || ns == _T("~") )
    {
        name1 = _T("Spezialseite") ;
        target = ns + _T("/") + title ;
        tablinks += _T("<li id=\"ca-nstab-special\" class=\"selected\"><a href=\"") + target + _T("\">") + name1 + _T("</a></li>\n") ;
    } else {
        wxString class1 , class2 ;
        wxString ns1 , ns2 ;
        ns1 = ns ;
        ns2.Replace ( _T("$") , _T("") ) ;
        ns2 = _T("$") + ns1 ;
        if ( ns1 == _T("A") ) { name1 = _T("Artikel") ; page_full = _T("") ; page_discussion = _T("Diskussion:") ; }
        if ( ns1 == _T("P") ) { name1 = _T("Portal") ; page_full = _T("Portal:") ; page_discussion = _T("Portal_Diskussion:") ; }
        if ( ns1 == _T("I") ) { name1 = _T("Bild") ; page_full = _T("Bild:") ; page_discussion = _T("Bild_Diskussion:") ; }
        page_full += title ;
        page_discussion += title ;
        name2 = _T("Versionen/Autoren") ;
        wxURI uri ( title ) ;
        target = uri.BuildURI() ;
        if ( va ) class2 = _T(" class=\"selected\"") ;
        else class1 = _T(" class=\"selected\"") ;
        tablinks += _T("<li id=\"ca-nstab-special\"") + class1 + _T("><a href=\"") + ns1 + _T("/") + target + _T("\">") + name1 + _T("</a></li>\n") ;
        tablinks += _T("<li id=\"ca-nstab-special\"") + class2 + _T("><a href=\"") + ns2 + _T("/") + target + _T("\">") + name2 + _T("</a></li>\n") ;
    }

    // Create HTML
    wxString html = sop.GetString() ;
    wxString lastsearch = Unescape ( GetValue ( _T("e") ) ) ;
    text = _T("\n<!--BEGIN INSERTION-->\n") + text + _T("\n<!--END INSERTION-->\n") ;
    
    // Replace some stuff
    html.Replace ( _T("%%IP%%") , frame->GetIP() ) ;
    html.Replace ( _T("%%PORT%%") , frame->GetPort() ) ;
    html.Replace ( _T("%%TITLE%%") , nicetitle ) ;
    html.Replace ( _T("%%NAME1%%") , name1 ) ;
    html.Replace ( _T("%%PAGE_FULL%%") , page_full ) ;
    html.Replace ( _T("%%PAGE_DISCUSSION%%") , page_discussion ) ;
    html.Replace ( _T("%%TABLINKS%%") , tablinks ) ;
    html.Replace ( _T("%%LASTSEARCH%%") , lastsearch ) ;
    
    // Insert the text
    html.Replace ( _T("%%BODY%%") , text ) ;
    hr.AddDataLine( html );
}

void wxWikiServer::ReturnBinary ( wxString article , ZenoArticle &art , HttpResponse &hr , wxString content_type )
{
    char *data = art.GetBlob () ;
    hr.SetRC(wxT("200 OK"));
    hr.AddHeader(wxT("Content-Type: ") + content_type );
    hr.SetBinaryData ( data , art.rFileLen ) ;
}
