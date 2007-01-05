/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
 * Based on sources in wxMusic
*/
#ifndef __WXWEBSERVER_H_
#define __WXWEBSERVER_H_

#include <wx/socket.h>

inline const wxCharBuffer ConvToUTF8( const wxString &s )
{
#if wxUSE_UNICODE
	return wxConvUTF8.cWC2MB(s);
#else
	return wxConvUTF8.cWC2WX(wxConvCurrent->cMB2WC(s));
#endif
}


class wxWebServer : public wxEvtHandler
{
      
    public:
    wxWebServer () ;
    virtual ~wxWebServer () ;
    virtual void Start(int port);
    virtual void Stop();
    virtual bool IsRunning();
    virtual int GetPort();
    virtual wxString GetValue ( wxString key , wxString def = _T("") ) ;
    virtual wxString Unescape ( wxString s ) ;
    virtual char *spc_decode_url(const char *url, size_t *nbytes) ;
    
    class HttpResponse // Response class
    {
        public: 
        HttpResponse();
        void Send(wxSocketBase*pSocket);
        void SetRC(const wxString & rc);
        void AddDataLine(const wxString & data);
        void AddHeader(const wxString & Header);
        void SetBinaryData(char *data, unsigned long len);
        bool Ok(){return !m_sRC.IsEmpty();}
        
        private:
        wxString m_sRC;
        wxString m_sHeaders;
        wxString m_sData;
        char *m_sBinaryData ;
        unsigned long m_sBinaryDataLength ;
    };

    DECLARE_EVENT_TABLE()
    
    private:
    bool m_running;
    int m_port;
	wxSocketServer *socket_server;
	wxArrayString keys , values ;

	virtual void OnServerEvent(wxSocketEvent& event);
	virtual void OnSocketEvent(wxSocketEvent& event);

	virtual void ProcessRequest(const wxString &reqstr,HttpResponse &hr);
	virtual int  ReadLine(wxSocketBase*pSocket,wxString& outstr);
	virtual void WriteLine(wxSocketBase*pSocket,const wxString &str);

    virtual void HandleSimpleGetRequest(const wxString &page,HttpResponse &hr);
    
} ;


#endif
