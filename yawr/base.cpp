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
#include <wx/textfile.h>
#include <wx/image.h>

// Outcomment the following to turn loging off
// Note : Logging may significantly increase runtime!!
//#define LOGIT

IMPLEMENT_APP(MainApp)

BEGIN_EVENT_TABLE(MainFrame, wxFrame)
   EVT_CLOSE(MainFrame::OnClose)
   
   EVT_BUTTON(ID_START_SERVER, MainFrame::OnStartServer)
   EVT_BUTTON(ID_STOP_SERVER, MainFrame::OnStopServer)
   EVT_BUTTON(ID_START_BROWSER, MainFrame::OnStartBrowser)
   EVT_BUTTON(ID_CHOOSE_DIR, MainFrame::OnChooseDir)
   EVT_CHOICE(ID_CHOOSE_LANGUAGE, MainFrame::OnChooseLanguage)
   
   EVT_ICONIZE(MainFrame::OnIconize)
END_EVENT_TABLE()

BEGIN_EVENT_TABLE(MyTaskBarIcon, wxTaskBarIcon)
   EVT_TASKBAR_LEFT_DOWN(MyTaskBarIcon::OnLeftButtonDClick)
   EVT_TASKBAR_LEFT_DCLICK(MyTaskBarIcon::OnLeftButtonDClick)
END_EVENT_TABLE()


//________________________________________________________________________________________
// Global functions

WX_DECLARE_STRING_HASH_MAP( wxString, StringHashMap );

StringHashMap interface_translations ;

wxString txt ( wxString s )
{
    s = s.Lower() ;
    if ( interface_translations.find ( s ) == interface_translations.end() ) return _T("UNKNOWN TRANSLATION FOR ")+s ;
    return interface_translations[s] ;
}

wxString txt ( char *c )
{
    wxString s ( c , wxConvUTF8 ) ;
    return txt ( s ) ;
}

//________________________________________________________________________________________
// MainApp

bool MainApp::OnInit()
{
   frame = new MainFrame(_("Yet Another Wikipedia Reader"), wxPoint (100, 100),
     wxSize(450, 340));
   frame->Show(TRUE);
   SetTopWindow(frame);
   
   return TRUE;
}


//________________________________________________________________________________________
// MainFrame

MainFrame::MainFrame(const wxString &title, const wxPoint &pos, const wxSize &size)
    : wxFrame((wxFrame *) NULL, -1, title, pos, size, wxDEFAULT_FRAME_STYLE|wxMINIMIZE|wxMINIMIZE_BOX)
{
    iconized = false ;
    tbi.frame = this ;
    wxInitAllImageHandlers() ;
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
    
    LoadTranslation ( config->Read ( _T("Language") , _T("de") ) ) ;
    
    answer_local_only = new wxCheckBox ( this , -1 , _T("") ) ;
    start_server_automatically = new wxCheckBox ( this , -1 , _T("") ) ;
    minimize_automatically = new wxCheckBox ( this , -1 , _T("") ) ;
    start_browser_automatically = new wxCheckBox ( this , -1 , _T("") ) ;
    minimize_to_tray = new wxCheckBox ( this , -1 , _T("") ) ;
    dir_line = new wxTextCtrl ( this , -1 , config->Read ( _T("DefaultDir") , dirbase ) ) ;
    port_line = new wxTextCtrl ( this , -1 , config->Read ( _T("DefaultPort") , _T("8080") ) ) ;
    langlist = new wxChoice ( this , ID_CHOOSE_LANGUAGE ) ;
    
    b_start_server = new wxButton ( this , ID_START_SERVER , _T("") ) ;
    b_stop_server = new wxButton ( this , ID_STOP_SERVER , _T("") ) ;
    b_start_browser = new wxButton ( this , ID_START_BROWSER , _T("") ) ;
    b_choose_dir = new wxButton ( this , ID_CHOOSE_DIR , _T("...") ) ;
    
    data_dir_text = new wxStaticText ( this , -1 , _T("") ) ;
    port_number_text = new wxStaticText ( this , -1 , _T("") ) ;
    language_text = new wxStaticText ( this , -1 , _T("") ) ;
    
    h_dir->Add ( data_dir_text , 0 , wxALL , 5 ) ;
    h_dir->Add ( dir_line , 1 , wxEXPAND|wxALL , 5 ) ;
    h_dir->Add ( b_choose_dir , 0 , wxALL , 5 ) ;

    h_port->Add ( port_number_text , 0 , wxALL , 5 ) ;
    h_port->Add ( port_line , 1 , wxALL , 5 ) ;
    h_port->Add ( language_text , 0 , wxALL , 5 ) ;
    h_port->Add ( langlist , 0 , wxALL , 5 ) ;

    h_buttons->Add ( b_start_server , 1 , wxALL , 5 ) ;
    h_buttons->Add ( b_stop_server , 1 , wxALL , 5 ) ;
    h_buttons->Add ( b_start_browser , 1 , wxALL , 5 ) ;

    v0->Add ( h_dir , 0 , wxEXPAND|wxALL , 5 ) ;
    v0->Add ( h_port , 0 , wxALL , 5 ) ;
    v0->Add ( answer_local_only , 0 , wxALL , 5 ) ;
    v0->Add ( start_server_automatically , 0 , wxALL , 5 ) ;
    v0->Add ( minimize_automatically , 0 , wxALL , 5 ) ;
    v0->Add ( minimize_to_tray , 0 , wxALL , 5 ) ;
    v0->Add ( start_browser_automatically , 0 , wxALL , 5 ) ;
    v0->Add ( h_buttons , 0 , wxALL , 5 ) ;

#ifndef __WXMSW__
    minimize_to_tray->Hide() ; // Offer minimize to tray on Window$ only
#endif

#ifdef LOGIT
	log_output = new wxTextCtrl ( this , -1 , _T("") , wxDefaultPosition , wxDefaultSize , wxTE_MULTILINE ) ;
    v0->Add ( log_output , 1 , wxEXPAND|wxALL , 5 ) ;
#endif
    
    CreateStatusBar(3);

    SetBackgroundColour ( *wxWHITE ) ;
    SetSizer ( v0 ) ;
    v0->Fit ( this ) ;
    
    answer_local_only->SetValue ( config->Read ( _T("AnswerLocalOnly") , (long)1 ) ) ;
    start_server_automatically->SetValue ( config->Read ( _T("StartServerAutomatically") , (long)0 ) ) ;
    minimize_automatically->SetValue ( config->Read ( _T("MinimizeAutomatically") , (long)0 ) ) ;
    start_browser_automatically->SetValue ( config->Read ( _T("StartBrowserAutomatically") , (long)0 ) ) ;
    minimize_to_tray->SetValue ( config->Read ( _T("MinimizeToTray") , (long)0 ) ) ;
    
    SwitchLanguage ( languages[current_language] ) ;
    
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
	
	wxIcon icon ( dirbase + _T("Icon.png") , wxBITMAP_TYPE_PNG ) ;
	tbi.SetIcon ( icon , _T("YAWR") ) ;

	if ( minimize_automatically->GetValue() ) { wxIconizeEvent event ; OnIconize ( event ) ; }
	if ( start_browser_automatically->GetValue() ) { wxCommandEvent event; OnStartBrowser(event); }
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
    config->Write ( _T("MinimizeToTray") , minimize_to_tray->GetValue() ) ;
    config->Write ( _T("Language") , languages[current_language] ) ;
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

void MainFrame::OnChooseLanguage(wxCommandEvent &event)
{
    wxString l = langlist->GetStringSelection() ;
    int a , nl = -1 ;
    for ( a = 1 ; a < languages.GetCount() ; a++ )
    {
        if ( txt(_T("language_")+languages[a]) != l ) continue ;
        nl = a ;
    }
    if ( nl == -1 ) return ; // Paranoia
    SwitchLanguage ( languages[nl] ) ;
}

void MainFrame::OnChooseDir(wxCommandEvent &event)
{
    wxString dir = dir_line->GetValue() ;
    wxString newdir = wxDirSelector ( txt("chose_data_dir") , dir ) ;
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
ZenoFile *MainFrame::GetMainPointer() { return &zf_main ; }

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

void MainFrame::LoadTranslation ( wxString language )
{
    int a , index ;
    wxArrayString as ;

    // Open file
    wxString filename = dirbase + _T("interface.txt") ;
    wxTextFile f ( filename ) ;
    if ( !f.Open() ) // Paranoia
    {
        wxMessageBox ( _T("Can't open file interface.txt!") ) ;
        return ;
    }
    
    // Read language index
    as = ReadTranslationLine ( f.GetLine(0) ) ;
    for ( index = 1 ; index < as.GetCount() && as[index] != language ; index++ ) ;
    if ( index == as.GetCount() ) // Paranoia
    {
        wxMessageBox ( _T("Can't find that language!") ) ;
        return ;
    }
    
    // Read translation table
    languages = as ;
    current_language = index ;
    interface_translations.clear() ;
    for ( a = 1 ; a < f.GetLineCount() ; a++ )
    {
        wxString s = f.GetLine(a) ;
        if ( s.Left(1) != _T("\"") ) continue ; // Ignore headings, blank lines, etc.
        as = ReadTranslationLine ( s ) ;
        if ( as.GetCount() <= index ) continue ; // Compensating for missing translations
        wxString key = as[0] ;
        wxString value = as[index] ;
        key = key.Lower() ; // Keys must be case-insensitive
        interface_translations[key] = value ;
    }
}

wxArrayString MainFrame::ReadTranslationLine ( wxString line )
{
    wxArrayString ret ;
    bool quote = false ;
    wxString s ;
    int a ;

//    line += _T(",") ; // Little helper
    for ( a = 0 ; a <line.length() ; a++ )
    {
        wxChar c = line[a] ;
        if ( c == '"' )
        {
            if ( quote ) ret.Add ( s ) ;
            else s.Empty() ;
            quote = !quote ;
        }
        else if ( c == '\\' && s[a+1] == '"' )
        {
            s += _T("\\\"") ;
            a++ ;
        }
        else if ( quote ) s += c ;
    }
    
    return ret ;
}

void MainFrame::SwitchLanguage ( wxString nl )
{
    int a ;
    LoadTranslation ( nl ) ;
    
    langlist->Clear() ;
    for ( a = 1 ; a < languages.GetCount() ; a++ )
        langlist->Append ( txt(_T("language_")+languages[a]) ) ;
    langlist->SetSelection ( current_language-1 ) ;
    
    answer_local_only->SetLabel ( txt("answer_local_only") ) ;
    start_server_automatically->SetLabel ( txt("start_server_automatically") ) ;
    minimize_automatically->SetLabel ( txt("minimize_automatically") ) ;
    start_browser_automatically->SetLabel ( txt("start_browser_automatically") ) ;
    b_start_server->SetLabel ( txt("b_start_server") ) ;
    b_stop_server->SetLabel ( txt("b_stop_server") ) ;
    b_start_browser->SetLabel ( txt("b_start_browser") ) ;
    data_dir_text->SetLabel ( txt("data_dir_text") ) ;
    port_number_text->SetLabel ( txt("port_number_text") ) ;
    language_text->SetLabel ( txt("language_text") ) ;
    minimize_to_tray->SetLabel ( txt("minimize_to_tray") ) ;
    
    GetSizer()->Layout() ;
}

void MainFrame::OnIconize(wxIconizeEvent& event)
{
    if ( !minimize_to_tray->GetValue() ) return ;

    if (event.Iconized())
    {
        this->Show(FALSE);
    } else {
        Raise() ;
        Show(TRUE);
        Raise() ;
        SetFocus() ;
        Raise() ;
    }
    iconized = !event.Iconized() ;
}


void MyTaskBarIcon::OnLeftButtonDClick(wxTaskBarIconEvent&event)
{
    if ( !frame ) return ; // Not available
    wxIconizeEvent ev ( 0 , frame->iconized ) ;
    frame->OnIconize ( ev ) ;
}
