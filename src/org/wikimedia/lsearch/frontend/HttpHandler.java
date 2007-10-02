/*
 * Created on Jan 23, 2007
 *
 */
package org.wikimedia.lsearch.frontend;

import java.io.BufferedInputStream;
import java.io.DataInputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.Socket;
import java.net.URI;
import java.net.URISyntaxException;
import java.util.HashMap;
import org.apache.log4j.Logger;

/**
 * Simple HTTP 1.1 handler, used for Index and Search daemons
 * for more info on protocole see handle() method
 * 
 * @author Brion Vibber
 *
 */
abstract public class HttpHandler extends Thread {
	protected static org.apache.log4j.Logger log = Logger.getLogger(HttpHandler.class);

	/** NOTE: we are using old JDK 1.1 classes which don't have
	 * a very good unicode support, so extra care needs to be taken
	 * so everything is converted out right. The old classes are used
	 * because they enable better byte-oriented operations.  
	 */
	/** Client input stream */
	DataInputStream istrm;
	/** Client output stream */
	PrintWriter ostrm;
	
	protected String method;
	protected String rawUri;
	protected URI uri;
	protected String version;
	protected String postData;

	protected final int BUF_SIZE = 8192;
	protected char[] outputBuffer = new char[BUF_SIZE];
	protected int bufLength = 0;

	protected int minorVersion; // the x in HTTP 1.x

	protected String contentType = "text/html";	
	boolean headersSent;

	protected HashMap headers;

	public HttpHandler(Socket s) {
		try {
			istrm = new DataInputStream(new BufferedInputStream(s.getInputStream()));
			ostrm = new PrintWriter(s.getOutputStream());			
		} catch (IOException e) {
			log.error("I/O in opening http socket.");
		}
	}

	private static int openCount = 0;
	private static Object countLock = new Object();

	public static int getOpenCount(){
		synchronized (countLock) {
			return openCount;
		}
	}

	private static void enter() {
		synchronized (countLock) {
			openCount++;
		}
	}

	private static void leave() {
		synchronized (countLock) {
			openCount--;
		}
	}

	public boolean isKeepAlive(){
		if(version.equals("HTTP/1.0")){
			if(headers.get("Connection")!=null &&
					((String)headers.get("Connection")).equalsIgnoreCase("Keep-Alive"))
				return true;
			else
				return false;
		}
		else if(version.equals("HTTP/1.1")){
			if(headers.get("Connection")!=null &&
					((String)headers.get("Connection")).equalsIgnoreCase("close"))
				return false;
			else
				return true;
		}
		return false;
	}

	public void run() {		
		try {
			enter();
			do{			
				headersSent = false;
				handle();
				log.debug("request handled.");
			} while(isKeepAlive());
			log.debug("No keep-alive, closing connection ... ");
		} catch (Exception e) {
			e.printStackTrace();
			log.error(e.getMessage());
		} finally {
			if (!headersSent) {
				sendError(500, "Internal server error", "An internal error occurred.");
			}
			flushOutput();
			// Make sure the client is closed out.
			try {  ostrm.close(); } catch(Exception e) { }
			try {  istrm.close(); } catch(Exception e) { }
			leave();
		}
	}

	/** 
	 * Simple HTTP protocol; Used for search (GET) and indexing (POST) 
	 * GET requests syntax:
	 *   URL path format: /operation/database/searchterm
	 *   The path should be URL-encoded UTF-8 (standard IRI).
	 * 
	 *   Additional paramters may be specified in a query string:
	 *     namespaces: comma-separated list of namespace numeric keys to subset results
	 *     limit: maximum number of results to return
	 *     offset: number of matches to skip before returning results
	 * 
	 *   Content-type is text/plain and results are listed.
	 *  
	 * POST request syntax:
	 *   URL path format: /method?param1=value1&param2=value2
	 *   typically, this means: /updatePage?db=entest&title=Main%20Page
	 *   and the POST content is article text
	 */
	protected void handle() {
		headers = new HashMap();

		// parse first line		
		String request = readInputLine();		
		String[] reqParts = request.split(" ");

		method = reqParts[0];
		rawUri = reqParts[1];
		version = reqParts[2];

		// Parse headers
		for (String headerline = readInputLine(); !headerline.equals(""); headerline = readInputLine()){
			if(headerline.startsWith("Content-Length:"))
				headers.put("Content-Length",new Integer(headerline.substring(15).trim()));
			else{
				// try to split key : value
				String[] pair = headerline.split(":");
				if(pair.length==2){
					headers.put(pair[0].trim(),pair[1].trim());
				}
			}
		}

		// make fully qualified uri
		try {
			uri = new URI("http://localhost:8123" + rawUri);
		} catch (URISyntaxException e) {
			sendError(400, "Bad Request",
			"Couldn't make sense of the given URI.");
			log.warn("Bad URI in request: " + rawUri);
			return;
		}	

		postData = null;
		// fetch post data if needed
		if(method.equals("POST") && headers.containsKey("Content-Length")){
			int len = ((Integer)headers.get("Content-Length")).intValue();
			if(len==0) postData = "";
			else postData = new String(readBytes(len));
		}

		processRequest();
		flushOutput();
	}

	abstract protected void processRequest();

	protected void sendHeaders(int code, String message){
		sendHeaders(code,message,-1); // since java doesn't have default params
	}

	protected void sendHeaders(int code, String message, int contentLen) {
		if (headersSent) {
			log.warn("Asked to send headers, but already sent! ("+code+" "+message+")");
			return;
		}
		sendOutputLine("HTTP/1.1 "+code+" "+message);
		sendOutputLine("Content-Type: " + contentType);
		if(contentLen!=-1)
			sendOutputLine("Content-Length: "+contentLen);
		if(version=="HTTP/1.0" && isKeepAlive())
			sendOutputLine("Connection: Keep-Alive");
		else if(version=="HTTP/1.1" && !isKeepAlive())
			sendOutputLine("Connection: close");

		sendOutputLine("");
		headersSent = true;
	}

	protected void sendError(int code, String message, String detail) {
		contentType = "text/html";
		sendHeaders(code, message);
		sendOutputLine("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n"+ 
				"<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n"+
				"<head>\n<title>Error: " + code + " " + message + "</title>\n"+
				"</head>\n<body>\n<h1>" + code + " " + message + "</h1>\n"+
				"<p>" + detail + "</p>\n<hr />\n<p><i>MWSearch on localhost" + 
		"</i></p>\n</body>\n</html>");
	}

	/** Send single line to client. The lines are buffered and sent out in chunks */
	protected void sendOutputLine(String sout) {
		log.debug(">>>"+sout);
		// write to buffer instead directly to stream!
		char[] s = (sout+"\r\n").toCharArray(); 
		if(bufLength + s.length >= outputBuffer.length)
			flushOutput();
		// extend buffer if needed
		if(s.length > bufLength){
			outputBuffer = new char[s.length*2];
		}
		System.arraycopy(s,0,outputBuffer,bufLength,s.length);
		bufLength+=s.length;
	}

	/** Sending raw data to client */
	protected void sendBytes(char[] data){
		log.debug(">>> Writing "+data.length+" bytes of data");
		flushOutput();
		ostrm.write(data);
	}

	/** Read some number of bytes. Used to read the raw article in POST */
	protected byte[] readBytes(int length){
		log.debug("Reading "+length+" bytes of data");
		try {
			int readsofar = 0;
			int read = 0;
			byte[] data = new byte[length];
			while(readsofar != length){
				read = istrm.read(data,readsofar,length-readsofar);
				readsofar += read;
			}    		
			//log.error("Internal error, read "+read+" bytes istead of "+contentLength+" from POST request");
			return data; 
		} catch (IOException e) {
			log.warn("Could not send raw data in bytes to output stream.");
		}
		return null;
	}

	/** This method is to be used for header reads only (which is utf-8 free!) */
	@SuppressWarnings("deprecation")
	protected String readInputLine() {
		String sin="";
		try {
			sin = istrm.readLine();
		} catch (IOException e) {
			log.warn("I/O problem in reading from stream");
		}
		log.debug("<<<"+ sin);
		return sin;
	}

	/** Flush output buffer, i.e. the one used by sendOutputLine() */
	protected void flushOutput(){
		if(bufLength != 0){
			ostrm.write(new String(outputBuffer,0,bufLength));
			bufLength = 0;
		}
		ostrm.flush();
	}

}
