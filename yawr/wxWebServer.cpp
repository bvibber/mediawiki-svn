/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
 * Based on sources in wxMusic
*/
#include <wx/wxprec.h>
#ifndef WX_PRECOMP
   #include <wx/wx.h>
#endif

#include "wxWebServer.h"
#include <wx/uri.h>

enum
{
  // id for sockets
  WX_HTTP_SERVER_ID = 100,
  WX_HTTP_SOCKET_ID
};

#define WX_HTTP_SERVER_MAX_BUF_LEN 1024

BEGIN_EVENT_TABLE(wxWebServer, wxEvtHandler)
  EVT_SOCKET(WX_HTTP_SERVER_ID,  wxWebServer::OnServerEvent)
  EVT_SOCKET(WX_HTTP_SOCKET_ID,  wxWebServer::OnSocketEvent)
END_EVENT_TABLE()

/**
 * Constructor
*/
wxWebServer::wxWebServer()
{
    m_port = -1;
    m_running = false;
}

wxWebServer::~wxWebServer()
{
    Stop();
}

void wxWebServer::Start(int port)
{
    if ( IsRunning() )
    {
        if ( m_port == port ) return ; // Already running on this port
        Stop() ; // Stop before restarting on new port
    }
     m_port = port;

    wxIPV4address addr;
    addr.Service( port );
    socket_server = new wxSocketServer( addr );

    socket_server->SetEventHandler( *this, WX_HTTP_SERVER_ID );
    socket_server->SetNotify( wxSOCKET_CONNECTION_FLAG );
    socket_server->Notify( TRUE );

     m_running = true;
}

void wxWebServer::Stop()
{
     if ( !m_running ) return ;
     socket_server->Destroy();
     m_running = false;
}

bool wxWebServer::IsRunning()
{
     return m_running;
}

int wxWebServer::GetPort()
{
    return m_port;
}


//____________________________________________ PRIVATE

void wxWebServer::OnServerEvent(wxSocketEvent& event)
{
     if ( !IsRunning() ) return ;

	switch( event.GetSocketEvent() )
	{
		case wxSOCKET_CONNECTION:
			break;
		default:
			return;
	}

	wxSocketBase *pSocket = socket_server->Accept( true );
	if ( !pSocket ) return ; // Paranoia

    pSocket->SetFlags(wxSOCKET_BLOCK|wxSOCKET_WAITALL);// block gui, fix reentrancy problem.
	pSocket->SetEventHandler(*this, WX_HTTP_SOCKET_ID);
	pSocket->SetNotify(wxSOCKET_INPUT_FLAG | wxSOCKET_LOST_FLAG);
	pSocket->Notify(true);
     
}

void wxWebServer::OnSocketEvent(wxSocketEvent& event)
{
    wxSocketBase *sock = event.GetSocket();
    
    // Now we process the event
    switch(event.GetSocketEvent())
    {
        case wxSOCKET_INPUT:
        {
        // We disable input events, so that the test doesn't trigger
        // wxSocketEvent again. this prohibits reentrance
        sock->SetNotify(wxSOCKET_LOST_FLAG);
        
        wxString sRequest;
        if(ReadLine(sock, sRequest ))
        {
            wxArrayString arrReqHeaders;
            wxString h;
            while(ReadLine(sock, h )> 2) // > 2 detects lines which do not consist only of \r\n
            {
                arrReqHeaders.Add(h);
            }
            
            HttpResponse hr;
            ProcessRequest( sRequest ,hr);
            if(hr.Ok()) hr.Send(sock);
        }
        
        // Enable input events again.
        sock->SetNotify(wxSOCKET_LOST_FLAG | wxSOCKET_INPUT_FLAG);
        break;
        }
        
        case wxSOCKET_LOST:
        {
            // Destroy() should be used instead of delete wherever possible,
            // due to the fact that wxSocket uses 'delayed events' (see the
            // documentation for wxPostEvent) and we don't want an event to
            // arrive to the event handler (the frame, here) after the socket
            // has been deleted. Also, we might be doing some other thing with
            // the socket at the same time; for example, we might be in the
            // middle of a test or something. Destroy() takes care of all
            // this for us.
            sock->Destroy();
            break;
        }
        default: ;
    }
    
}


void wxWebServer::ProcessRequest(const wxString &reqstr,HttpResponse &hr)
{
    wxURI uri;
    wxString command = reqstr.BeforeFirst ( ' ' ) ;
    wxString param = reqstr.AfterFirst ( ' ' ) ;
    
    // Get parameters
    if ( 1 )
    {
        keys.Clear() ;
        values.Clear() ;
        wxString page = param.BeforeFirst ( ' ' ) ;
        page = page.AfterLast ( '?' ) ;
        while ( !page.IsEmpty() )
        {
            wxString p = page.BeforeFirst ( '&' ) ;
            page = page.AfterFirst ( '&' ) ;
            wxString k = p.BeforeFirst ( '=' ) ;
            wxString v = p.AfterFirst ( '=' ) ;
            keys.Add ( k ) ;
            values.Add ( v ) ;
        }
    }
    
    // Process command
    if ( command == _T("GET") )
    {
        wxString page = param.BeforeFirst ( ' ' ) ;
        if ( -1 != page.Find ( '?' ) )
        {
            page = page.BeforeFirst ( ' ' ) ;
            page = page.BeforeLast ( '?' ) ;
        }
        page = uri.Unescape ( page ) ;
        HandleSimpleGetRequest ( page , hr ) ;
        return ;
    }
    
    hr.SetRC(wxT("404 Not Found"));
}

void wxWebServer::HandleSimpleGetRequest(const wxString &page,HttpResponse &hr)
{
    hr.SetRC(wxT("404 Not Found")); // Dummy
}

int wxWebServer::ReadLine(wxSocketBase*pSocket,wxString& outstr)
{
    outstr.Empty();
	int n;
	for ( n = 1; n < WX_HTTP_SERVER_MAX_BUF_LEN; n++ )
	{
		char c;
		pSocket->Read( &c, 1 );
		int charsread = pSocket->LastCount();

		if ( charsread == 1 )
		{
			outstr += c;
			if ( c == '\n' )
				break;
		}
		else if ( charsread == 0 )
		{
			if ( n == 1 )
				return 0;
			else
				break;
		}
    }

	return n;
}

void wxWebServer::WriteLine(wxSocketBase*pSocket,const wxString &str)
{
}


//________________________________________________________________________________________________
// HttpResponse

wxWebServer::HttpResponse::HttpResponse()
{
    m_sBinaryData = NULL ;
    m_sBinaryDataLength = 0 ;
}

void wxWebServer::HttpResponse::Send(wxSocketBase*pSocket)
{
    if ( m_sBinaryData && m_sBinaryDataLength )
    {
        wxString server_version = wxT( "Server: wxWebServer" );
        AddHeader (server_version );
        AddHeader(wxString( wxT("Content-Length: ")) << m_sBinaryDataLength);
        AddHeader(wxT("Connection: close"));
        wxString h;
        h << m_sRC << m_sHeaders << wxT("\n");
        const wxCharBuffer hbuf = ConvToUTF8( h );
        pSocket->Write( hbuf, strlen(hbuf) );
        pSocket->Write( m_sBinaryData, m_sBinaryDataLength);
    } else {
        const wxCharBuffer databuf = ConvToUTF8( m_sData );
        int databuflen = strlen(databuf);
        wxString server_version = wxT( "Server: wxWebServer" );
        AddHeader (server_version );
        AddHeader(wxString( wxT("Content-Length: ")) << databuflen);
        AddHeader(wxT("Connection: close"));
        wxString h;
        h << m_sRC << m_sHeaders << wxT("\n");
        const wxCharBuffer hbuf = ConvToUTF8( h );
        pSocket->Write( hbuf, strlen(hbuf) );
        pSocket->Write( databuf, databuflen);
    }
    
/*
    const wxCharBuffer databuf = ConvToUTF8( m_sData );
    int databuflen = strlen(databuf);
    wxString server_version = wxT( "Server: wxWebServer" );
    AddHeader (server_version );
    AddHeader(wxString( wxT("Content-Length: ")) << databuflen);
    AddHeader(wxT("Connection: close"));
    wxString h;
    h << m_sRC << m_sHeaders << wxT("\n");
    const wxCharBuffer hbuf = ConvToUTF8( h );
    pSocket->Write( hbuf, strlen(hbuf) );
    if(databuflen)
    {
        pSocket->Write( databuf, databuflen);
    }
    else if ( m_sBinaryData && m_sBinaryDataLength )
    {
        pSocket->Write( m_sBinaryData, m_sBinaryDataLength);
    }
*/
}

void wxWebServer::HttpResponse::SetBinaryData(char *data, unsigned long len)
{
    m_sBinaryData = data ;
    m_sBinaryDataLength = len ;
}

void wxWebServer::HttpResponse::SetRC(const wxString & rc)
{
    
    m_sRC = wxT("HTTP/1.1 ");
    m_sRC << rc << wxT("\n");
}
void wxWebServer::HttpResponse::AddDataLine(const wxString & data)
{
    m_sData << data << wxT("\n");
}
void wxWebServer::HttpResponse::AddHeader(const wxString & Header)
{
   m_sHeaders << Header << wxT("\n");
}

wxString wxWebServer::GetValue ( wxString key , wxString def )
{
    for ( int a = 0 ; a < keys.GetCount() ; a++ )
    {
        if ( keys[a] == key ) return values[a] ;
    }
    return def ;
}
