
/*
 * WikiSuite is the driver class for the wiki test suite.
 * It represents the location of the wiki, and provides
 * some common routines for access.
 */

package com.piclab.wikitest;
import com.meterware.httpunit.*;
import org.w3c.dom.*;


public class WikiSuite {

private String m_server = "http://www.wikipedia.org";
private String m_script = "/w/wiki.phtml";
private String m_articlepath = "http://www.wikipedia.org/wiki";
private String m_uploadpath = "http://www.wikipedia.org/upload/";
private String m_mainpage = "Main Page";


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
