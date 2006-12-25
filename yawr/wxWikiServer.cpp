/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#include <wx/wxprec.h>
#ifndef WX_PRECOMP
   #include <wx/wx.h>
#endif

#include "base.h"
#include <wx/wfstream.h>
#include <wx/sstream.h>

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

void wxWikiServer::SpecialPage (const wxString &page,HttpResponse &hr)
{
    if ( page == _T("random") )
    {
        wxString begin = GetValue ( _T("n") , _T("A") ) ;
        ZenoArticle art = frame->RandomArticle ( begin + _T("/") ) ;
        HandleSimpleGetRequest ( _T("/Wikipedia/") + art.title , hr ) ;
        return ;
    }
}

void wxWikiServer::HandleSimpleGetRequest(const wxString &page,HttpResponse &hr)
{
    ZenoArticle art ;
    wxString article = page ;
    if ( article.IsEmpty() || article == _T("/") ) article = _T("/Wikipedia/-/Hauptseite");
    if ( article == _T("/Wikipedia/") ) article += _T("-/Hauptseite") ;

    
    if ( article.Left(11) == _T("/Wikipedia/") ) // Article or the like
    {
        article = article.Mid ( 11 ) ;
        if ( article.Left ( 2 ) == _T("~/") ) // Special page
        {
            article = article.Mid ( 2 ) ;
            article = article.Lower() ;
            SpecialPage ( article , hr ) ;
            return ;
        } else { // Normal page
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
        return ;
    }

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
    wxString ns = article.BeforeFirst ( '/' ) ;
    wxString title = article.AfterFirst ( '/' ) ;
    wxString text = art.GetText() ;
    wxString nicetitle = title ;
    title.Replace ( _T(" ") , _T("_") ) ;
    nicetitle.Replace ( _T("_") , _T(" ") ) ;

    hr.SetRC(wxT("200 OK"));
    hr.AddHeader(wxT("Content-Type: text/html; charset=UTF8") );

    wxFFileInputStream in ( frame->dirbase + _T("base.html") ) ;
    wxStringOutputStream sop ;
    in.Read ( sop ) ;
    
    wxString html = sop.GetString() ;
    html.Replace ( _T("%%BODY%%") , text ) ;
    html.Replace ( _T("%%IP%%") , frame->GetIP() ) ;
    html.Replace ( _T("%%PORT%%") , frame->GetPort() ) ;
    html.Replace ( _T("%%TITLE%%") , nicetitle ) ;
    hr.AddDataLine( html );
}

void wxWikiServer::ReturnBinary ( wxString article , ZenoArticle &art , HttpResponse &hr , wxString content_type )
{
    char *data = art.GetBlob () ;
    hr.SetRC(wxT("200 OK"));
    hr.AddHeader(wxT("Content-Type: ") + content_type );
    hr.SetBinaryData ( data , art.rFileLen ) ;
}
