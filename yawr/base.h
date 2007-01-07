/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#ifndef BASE_H
#define BASE_H

#include "wxWebServer.h"
#include "ZenoFile.h"
#include <wx/config.h>
#include <wx/taskbar.h>

class MainFrame;

#include "wxWikiServer.h"

class MainApp: public wxApp
{
  public:
      virtual bool OnInit();
	  MainFrame *frame ;
};

class MyTaskBarIcon : public wxTaskBarIcon
{
    public :
#if defined(__WXCOCOA__)
    MyTaskBarIcon(wxTaskBarIconType iconType = DEFAULT_TYPE)
    :   wxTaskBarIcon(iconType)
#else
    MyTaskBarIcon()
#endif
    {}

    void OnLeftButtonDClick(wxTaskBarIconEvent&);
    
    MainFrame *frame ;

      DECLARE_EVENT_TABLE()
} ;

class MainFrame: public wxFrame
{
  public:
      MainFrame(const wxString &title, const wxPoint &pos, const wxSize &size);
      void OnClose(wxCloseEvent &event);

      void OnStartServer(wxCommandEvent &event);
      void OnStopServer(wxCommandEvent &event);
      void OnStartBrowser(wxCommandEvent &event);
      void OnChooseDir(wxCommandEvent &event);
      void OnChooseLanguage(wxCommandEvent &event);
      void OnIconize(wxIconizeEvent& event);
      
      ZenoArticle GetPage ( wxString title , bool va = false ) ;
      ZenoArticle GetImage ( wxString title , bool va = false ) ;
      wxString GetIP() ;
      wxString GetPort() ;
      ZenoArticle RandomArticle ( wxString begin ) ;
      ZenoFile *GetMainPointer() ;
	  ZenoFile *GetIndexPointer() ;
	  void LoadTranslation ( wxString language ) ;
	  
	  void Log ( wxString message , wxString function = _T("") ) ;
      
      wxString sep , project , dirbase ;
	  bool iconized ;
      
  private:
      DECLARE_EVENT_TABLE()
      
      ZenoArticle GetArticle ( wxString title , ZenoFile &file , bool va = false ) ;
	  void UpdateEnDis () ;
	  wxArrayString ReadTranslationLine ( wxString line ) ;
	  void SwitchLanguage ( wxString nl ) ;
      
      wxWikiServer server;
      ZenoFile zf_main , zf_index , zf_images ;
      MyTaskBarIcon tbi ;
      wxCheckBox *answer_local_only ;
      wxCheckBox *start_server_automatically ;
      wxCheckBox *minimize_automatically ;
      wxCheckBox *start_browser_automatically ;
      wxCheckBox *minimize_to_tray ;
      wxTextCtrl *port_line , *dir_line ;
      wxButton *b_start_server , *b_stop_server, *b_start_browser , *b_choose_dir ;
      wxStaticText *data_dir_text , *port_number_text , *language_text ;
      wxChoice *langlist ;
      wxConfig *config ;
	  wxTextCtrl *log_output ;
	  wxArrayString languages ;
	  int current_language ;
};

enum
{
   ID_START_SERVER= wxID_HIGHEST+1,
   ID_STOP_SERVER,
   ID_START_BROWSER,
   ID_CHOOSE_DIR,
   ID_CHOOSE_LANGUAGE
};

// Global functions
wxString txt ( char *c ) ;
wxString txt ( wxString s ) ;

#endif
