/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#ifndef __BASE_H
#define __BASE_H

#include "wxWebServer.h"
#include "ZenoFile.h"
#include <wx/config.h>

class MainFrame;

class wxWikiServer : public wxWebServer
{
    public :
    virtual void HandleSimpleGetRequest(const wxString &page,HttpResponse &hr);
    virtual void ReturnHTML ( wxString article , ZenoArticle &art , HttpResponse &hr ) ;
    virtual void ReturnHTML ( wxString article , wxString text , HttpResponse &hr ) ;
    virtual void ReturnPlainText ( wxString article , ZenoArticle &art , HttpResponse &hr ) ;
    virtual void ReturnCSS ( wxString article , ZenoArticle &art , HttpResponse &hr ) ;
    virtual void ReturnBinary ( wxString article , ZenoArticle &art , HttpResponse &hr , wxString content_type ) ;
    virtual void SpecialPage (const wxString &page,HttpResponse &hr);
	virtual wxArrayString Search ( wxString query , wxString mode ) ;
	virtual wxString FormatList ( const wxArrayString &titles , int from = 1 , int howmany = 100 , bool fulltext = false ) ;
    MainFrame *frame ;
    bool va ;
} ;

class MainApp: public wxApp
{
  public:
      virtual bool OnInit();
	  MainFrame *frame ;
};

class MainFrame: public wxFrame
{
  public:
      MainFrame(const wxString &title, const wxPoint &pos, const wxSize &size);
      void OnClose(wxCloseEvent &event);

      void OnStartServer(wxCommandEvent &event);
      void OnStopServer(wxCommandEvent &event);
      void OnStartBrowser(wxCommandEvent &event);
      void OnChooseDir(wxCommandEvent &event);
      
      ZenoArticle GetPage ( wxString title , bool va = false ) ;
      ZenoArticle GetImage ( wxString title , bool va = false ) ;
      wxString GetIP() ;
      wxString GetPort() ;
      ZenoArticle RandomArticle ( wxString begin ) ;
      ZenoFile *GetMainPointer() ;
	  ZenoFile *GetIndexPointer() ;
	  
	  void Log ( wxString message , wxString function = _T("") ) ;
      
      wxString sep , project , dirbase ;
      
  private:
      DECLARE_EVENT_TABLE()
      
      ZenoArticle GetArticle ( wxString title , ZenoFile &file , bool va = false ) ;
	  void UpdateEnDis () ;
      
      wxWikiServer server;
      ZenoFile zf_main , zf_index , zf_images ;
      wxCheckBox *answer_local_only ;
      wxCheckBox *start_server_automatically ;
      wxCheckBox *minimize_automatically ;
      wxCheckBox *start_browser_automatically ;
      wxTextCtrl *port_line , *dir_line ;
      wxButton *b_start_server , *b_stop_server, *b_start_browser , *b_choose_dir ;
      wxConfig *config ;
	  wxTextCtrl *log_output ;
};

enum
{
   ID_START_SERVER= wxID_HIGHEST+1,
   ID_STOP_SERVER,
   ID_START_BROWSER,
   ID_CHOOSE_DIR
};


#endif
