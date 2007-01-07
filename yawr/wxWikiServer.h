/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#ifndef WXWIKISERVER_H
#define WXWIKISERVER_H

class wxWikiServer : public wxWebServer
{
    public :
    wxWikiServer () ;
    virtual void HandleSimpleGetRequest(const wxString &page,HttpResponse &hr);
    virtual void ReturnHTML ( wxString article , ZenoArticle &art , HttpResponse &hr ) ;
    virtual void ReturnHTML ( wxString article , wxString text , HttpResponse &hr ) ;
    virtual void ReturnPlainText ( wxString article , ZenoArticle &art , HttpResponse &hr ) ;
    virtual void ReturnCSS ( wxString article , ZenoArticle &art , HttpResponse &hr ) ;
    virtual void ReturnBinary ( wxString article , ZenoArticle &art , HttpResponse &hr , wxString content_type ) ;
    virtual void SpecialPage (const wxString &page,HttpResponse &hr);
    virtual void Browse (HttpResponse &hr);
	virtual wxArrayString Search ( wxString query , wxString mode ) ;
	virtual wxString FormatList ( const wxArrayString &titles , int from , int howmany , wxString url , bool fulltext = false ) ;
	virtual wxString GetHTMLtitle ( wxString s ) ;
	virtual wxString EscapeURI ( wxString s ) ;
	virtual void FixLinks ( wxString &text ) ;
	virtual wxString GetSearchHeader() ;
	virtual wxString GetSearchResultsLink ( wxString title ) ;
	virtual wxString GetSidebar() ;
	virtual void AddVisited ( wxString article ) ;
	
    MainFrame *frame ;
    bool va , busy , fulltext ;
    int search_offset ;
    wxArrayString visited_pages ;
} ;


#endif
