
/*
 * WikiSuite is the driver class for the wiki test suite.
 * It represents the location of the wiki, and provides
 * some common routines for access.
 */

package com.piclab.wikitest;
import com.meterware.httpunit.*;
import org.w3c.dom.*;
import java.util.prefs.*;


public class WikiSuite {

private static Preferences m_uprefs =
  Preferences.userNodeForPackage( WikiSuite.class );

private String m_server;
private String m_script;
private String m_articlepath;
private String m_uploadpath;
private String m_mainpage;

public WikiSuite() {
	try {
		m_uprefs.importPreferences(new java.io.FileInputStream(
		  "wikitest.prefs" ));
	} catch (java.io.IOException e) {
		/* File probably doesn't exist: no problem, use defaults */
	} catch (InvalidPreferencesFormatException e) {
		System.err.println( "Bad preferences file format: " + e );
	}

	m_server = m_uprefs.get( "server", "http://www.wikipedia.org" );
	m_script = m_uprefs.get( "script", "/w/wiki.phtml" );
	m_articlepath = m_uprefs.get( "articlepath",
	  "http://www.wikipedia.org/wiki" );
	m_uploadpath = m_uprefs.get( "uploadpath",
	  "http://www.wikipedia.org/upload/" );
	m_mainpage = m_uprefs.get( "mainpage", "Main Page" );
}

public String titleToUrl( String title ) {
	StringBuffer sb = new StringBuffer( title.length() + 20 );

	if ( "".equals( title ) ) { title = m_mainpage; }

	for (int i=0; i<title.length(); ++i) {
		char c = title.charAt(i);
		if ((c >= 'A' && c <= 'Z') || (c >= 'a' && c <= 'z')) {
			sb.append(c);
		} else if (c >= '0' && c <= '9') {
			sb.append(c);
		} else if (c == '.' || c == '-' || c == '*' || c == ':' || c == '/'
		  || c == '(' || c == ')' || c == '_') {
			sb.append(c);
		} else if (c == ' ') {
			sb.append('_');
		} else {
			sb.append('%');
			String hex = "00" + Integer.toHexString((int)c);
			sb.append(hex.substring(hex.length() - 2));
		}
	}
	return sb.toString();
}

public WebResponse fetchPage( WebConversation wc, String title ) {
	WebResponse r = null;
	StringBuffer url = new StringBuffer(200);

	if ( "".equals( title ) ) {
		url.append( m_server );
	} else {
		url.append( m_articlepath ).append( "/" ).append(
		  titleToUrl( title ) );
	}

	try {
		r = wc.getResponse( url.toString() );
	} catch (Exception e) {
		System.err.println( "Exception: " + e );
	}
	return r;
}

public WebResponse editPage( WebConversation wc, String title ) {
	WebResponse r = null;
	StringBuffer url = new StringBuffer(200);

	url.append( m_server ).append( m_script ).append( "?title=" ).
	  append( titleToUrl( title ) ).append( "&action=edit" );

	try {
		r = wc.getResponse( url.toString() );
	} catch (Exception e) {
		System.err.println( "Exception: " + e );
	}
	return r;
}

public WebForm getFormByName( WebResponse resp, String name )
throws org.xml.sax.SAXException {

	WebForm[] forms = resp.getForms();
	for (int i=0; i < forms.length; i++) {
		Node formNode = forms[i].getDOMSubtree();
		NamedNodeMap nnm = formNode.getAttributes();
		Node nameNode = nnm.getNamedItem( "name" );

		if (nameNode == null) continue;
		if (nameNode.getNodeValue().equalsIgnoreCase( name )) {
			return forms[i];
		}
	}
	return null;
}

public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();

	if (! (new WikiTest(ws)).runTestAndReport()) return;
	if (! (new LinkTest(ws)).runTestAndReport()) return;
	if (! (new EditTest(ws)).runTestAndReport()) return;
}

}
