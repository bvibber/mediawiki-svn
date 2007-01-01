/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#include <wx/wxprec.h>
#ifndef WX_PRECOMP
   #include <wx/wx.h>
#endif

#include "base.h"
#include <wx/filename.h>

// Outcomment the following to turn loging off
// Note : Logging may significantly increase runtime!!
//#define LOGIT

IMPLEMENT_APP(MainApp)

bool MainApp::OnInit()
{
   frame = new MainFrame(_("Yet Another Wikipedia Reader"), wxPoint (100, 100),
     wxSize(450, 340));
   frame->Show(TRUE);
   SetTopWindow(frame);
   
   return TRUE;
}


BEGIN_EVENT_TABLE(MainFrame, wxFrame)
   EVT_CLOSE(MainFrame::OnClose)
   
   EVT_BUTTON(ID_START_SERVER, MainFrame::OnStartServer)
   EVT_BUTTON(ID_STOP_SERVER, MainFrame::OnStopServer)
   EVT_BUTTON(ID_START_BROWSER, MainFrame::OnStartBrowser)
   EVT_BUTTON(ID_CHOOSE_DIR, MainFrame::OnChooseDir)
END_EVENT_TABLE()

MainFrame::MainFrame(const wxString &title, const wxPoint &pos, const wxSize &size)
    : wxFrame((wxFrame *) NULL, -1, title, pos, size, wxDEFAULT_FRAME_STYLE|wxMINIMIZE|wxMINIMIZE_BOX)
{
    sep = wxFileName::GetPathSeparator() ;
    project = _T("wikipedia") ;
    dirbase = wxGetCwd() + sep ;
#ifdef __WXMAC__
	if ( !wxFileExists ( dirbase + _T("qunicode.txt") ) )
	{
		dirbase += _T("YAWR.app/Contents/Resources/") ;
	}

#endif
    config = new wxConfig(_T("YAWR"));
    wxStaticBoxSizer *v0 = new wxStaticBoxSizer ( wxVERTICAL , this , _T("Einstellungen") ) ;
    wxBoxSizer *h_dir = new wxBoxSizer ( wxHORIZONTAL ) ;
    wxBoxSizer *h_port = new wxBoxSizer ( wxHORIZONTAL ) ;
    wxBoxSizer *h_buttons = new wxBoxSizer ( wxHORIZONTAL ) ;
    
    answer_local_only = new wxCheckBox ( this , -1 , _T("Nur auf Anforderungen von diesem Rechner antworten") ) ;
    start_server_automatically = new wxCheckBox ( this , -1 , _T("Server automatisch starten") ) ;
    minimize_automatically = new wxCheckBox ( this , -1 , _T("YAWR automatisch minimieren") ) ;
    start_browser_automatically = new wxCheckBox ( this , -1 , _T("Webbrowser automatisch starten") ) ;
    dir_line = new wxTextCtrl ( this , -1 , config->Read ( _T("DefaultDir") , dirbase ) ) ;
    port_line = new wxTextCtrl ( this , -1 , config->Read ( _T("DefaultPort") , _T("8080") ) ) ;
    
    b_start_server = new wxButton ( this , ID_START_SERVER , _T("Server st&arten") ) ;
    b_stop_server = new wxButton ( this , ID_STOP_SERVER , _T("Server b&eenden") ) ;
    b_start_browser = new wxButton ( this , ID_START_BROWSER , _T("&Webbrowser starten") ) ;
    b_choose_dir = new wxButton ( this , ID_CHOOSE_DIR , _T("...") ) ;
    
    h_dir->Add ( new wxStaticText ( this , -1 , _T("Datenverzeichnis") ) , 0 , wxALL , 5 ) ;
    h_dir->Add ( dir_line , 1 , wxEXPAND|wxALL , 5 ) ;
    h_dir->Add ( b_choose_dir , 0 , wxALL , 5 ) ;

    h_port->Add ( new wxStaticText ( this , -1 , _T("Portnummer") ) , 0 , wxALL , 5 ) ;
    h_port->Add ( port_line , 1 , wxALL , 5 ) ;

    h_buttons->Add ( b_start_server , 1 , wxALL , 5 ) ;
    h_buttons->Add ( b_stop_server , 1 , wxALL , 5 ) ;
    h_buttons->Add ( b_start_browser , 1 , wxALL , 5 ) ;

    v0->Add ( h_dir , 0 , wxEXPAND|wxALL , 5 ) ;
    v0->Add ( h_port , 0 , wxALL , 5 ) ;
    v0->Add ( answer_local_only , 0 , wxALL , 5 ) ;
    v0->Add ( start_server_automatically , 0 , wxALL , 5 ) ;
    v0->Add ( minimize_automatically , 0 , wxALL , 5 ) ;
    v0->Add ( start_browser_automatically , 0 , wxALL , 5 ) ;
    v0->Add ( h_buttons , 0 , wxALL , 5 ) ;

#ifdef LOGIT
	log_output = new wxTextCtrl ( this , -1 , _T("") , wxDefaultPosition , wxDefaultSize , wxTE_MULTILINE ) ;
    v0->Add ( log_output , 1 , wxEXPAND|wxALL , 5 ) ;
#endif
    
    CreateStatusBar(3);
    SetStatusText(_("Bereit"));

    SetBackgroundColour ( *wxWHITE ) ;
    SetSizer ( v0 ) ;
    v0->Fit ( this ) ;
    
    answer_local_only->SetValue ( config->Read ( _T("AnswerLocalOnly") , (long)1 ) ) ;
    start_server_automatically->SetValue ( config->Read ( _T("StartServerAutomatically") , (long)0 ) ) ;
    minimize_automatically->SetValue ( config->Read ( _T("MinimizeAutomatically") , (long)0 ) ) ;
    start_browser_automatically->SetValue ( config->Read ( _T("StartBrowserAutomatically") , (long)0 ) ) ;
    
    long l ;
    server.frame = this ;
    port_line->GetValue().ToLong ( &l ) ;
    if ( start_server_automatically->GetValue() ) server.Start ( l ) ;
    
    b_start_server->Enable ( !server.IsRunning() ) ;
    b_stop_server->Enable ( server.IsRunning() ) ;
    
    if ( zf_main.Open ( dir_line->GetValue() + sep + project + _T(".zeno") ) ) SetStatusText(_T("Texte OK"),0);
    else SetStatusText(_T("Texte failed"),0);
    
    if ( zf_images.Open ( dir_line->GetValue() + sep + project + _T(".images.zeno") ) ) SetStatusText(_T("Bilder OK"),1);
    else SetStatusText(_T("Bilder failed"),1);
	
    if ( zf_index.Open ( dir_line->GetValue() + sep + project + _T(".index.zeno") ) ) SetStatusText(_T("Index OK"),2);
    else SetStatusText(_T("Index failed"),2);
	
	UpdateEnDis() ;
	
	if ( minimize_automatically->GetValue() ) Iconize() ;
}

wxLongLong lasttime ( 0 ) ;

void  MainFrame::Log ( wxString message , wxString function )
{
#ifdef LOGIT
	if ( !function.IsEmpty() ) message = function + _T(" : ") + message ;
	wxLongLong diff = wxGetLocalTimeMillis() - lasttime ;
	lasttime += diff ;
	message = diff.ToString() + _T(" : ") + message ;
	(*log_output) << message << _T("\n") ;
#endif
}

void MainFrame::UpdateEnDis ()
{
    b_start_server->Enable ( !server.IsRunning() ) ;
    b_stop_server->Enable ( server.IsRunning() ) ;
	answer_local_only->Enable ( !server.IsRunning() ) ;
	dir_line->Enable ( !server.IsRunning() ) ;
	port_line->Enable ( !server.IsRunning() ) ;
	b_choose_dir->Enable ( !server.IsRunning() ) ;
}

void MainFrame::OnClose(wxCloseEvent &event)
{
    config->Write ( _T("AnswerLocalOnly") , (long)answer_local_only->GetValue() ) ;
    config->Write ( _T("StartServerAutomatically") , (long)start_server_automatically->GetValue() ) ;
    config->Write ( _T("MinimizeAutomatically") , (long)minimize_automatically->GetValue() ) ;
    config->Write ( _T("StartBrowserAutomatically") , (long)start_browser_automatically->GetValue() ) ;
    config->Write ( _T("DefaultDir") , dir_line->GetValue() ) ;
    config->Write ( _T("DefaultPort") , port_line->GetValue() ) ;
    delete config;

    server.Stop() ;
    event.Skip();
}

void MainFrame::OnStartServer(wxCommandEvent &event)
{
    if ( server.IsRunning() ) return ;
    long l ;
    port_line->GetValue().ToLong ( &l ) ;
    server.Start ( l ) ;
	UpdateEnDis() ;
	Log ( _T("Server started" ) ) ;
}

void MainFrame::OnStopServer(wxCommandEvent &event)
{
    if ( !server.IsRunning() ) return ;
    server.Stop() ;
	UpdateEnDis() ;
	Log ( _T("Server stopped" ) ) ;
}

void MainFrame::OnStartBrowser(wxCommandEvent &event)
{
     wxString url = _T("http://127.0.0.1:") + port_line->GetValue() ;
     OnStartServer ( event ) ; // Just to make sure...
     wxLaunchDefaultBrowser ( url ) ;
}

void MainFrame::OnChooseDir(wxCommandEvent &event)
{
    wxString dir = dir_line->GetValue() ;
    wxString newdir = wxDirSelector ( _T("Verzeichnis mit Wikipedia-Daten") , dir ) ;
    if ( newdir.IsEmpty() ) return ; // Cancel
    dir_line->SetValue ( newdir ) ;
}

ZenoArticle MainFrame::GetPage ( wxString title , bool va )
{
    return GetArticle ( title , zf_main , va ) ;
}

ZenoArticle MainFrame::GetImage ( wxString title , bool va )
{
    return GetArticle ( title , zf_images , va ) ;
}

ZenoArticle MainFrame::GetArticle ( wxString title , ZenoFile &file , bool va )
{
    ZenoArticle art ;
    unsigned long l = file.FindPageID ( title ) ;
    if ( l == 0 ) { art.ok = false ; return art ; }
    if ( va ) l++ ;
    art = file.ReadSingleArticle ( l ) ;
    return art ;
}

wxString MainFrame::GetIP() { return _T("127.0.0.1") ; }
wxString MainFrame::GetPort() { return port_line->GetValue() ; }
ZenoFile *MainFrame::GetIndexPointer() { return &zf_index ; }

ZenoArticle MainFrame::RandomArticle ( wxString begin )
{
    srand ( (unsigned)time(0) );
    while ( 1 )
    {
        int random_integer = rand();
        random_integer %= zf_main.rCount ;
        ZenoArticle art = zf_main.ReadSingleArticle ( random_integer ) ;
        if ( art.rSubtype == 0 ) return art ;
    }
}
