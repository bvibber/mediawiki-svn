/*
 * WikiSuite is the driver class for the wiki test suite.
 * It represents the location of the wiki, and provides
 * some common static routines for access.  When idividual
 * tests are instantiated, they are passed this object,
 * and they use its utility functions and result reporting.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;
import org.w3c.dom.*;
import java.util.prefs.*;
import java.util.logging.*;
import java.io.*;

public class WikiSuite {

private static Preferences ms_uprefs =
  Preferences.userNodeForPackage( WikiSuite.class );

/* Settings loaded from preferences:
 */
private static String ms_server;
private static String ms_script;
private static String ms_articlepath;
private static String ms_uploadpath;
private static String ms_mainpage;
private static String ms_sysoppass;

/* Primary conversation for test suite; individual
 * tests may also create their own if needed.
 */
private WebConversation m_conv;

private static Logger ms_logger = Logger.getLogger( "com.piclab.wikitest" );

static {
	/* Set logging level and properties:
	*/
	ms_logger.setUseParentHandlers( false );
	Handler h = new StreamHandler( System.out, new WikiLogFormatter() );
	h.setLevel( Level.INFO );

	ms_logger.addHandler( h );
	ms_logger.setLevel( Level.INFO );
	ms_logger.setFilter( null );
}

static String preloadedPages[] = { "Agriculture", "Anthropology",
	"Archaeology", "Architecture", "Astronomy_and_astrophysics",
	"Biology", "Business_and_industry", "Card_game", "Chemistry",
	"Classics", "Communication", "Computer_Science", "Cooking",
	"Critical_theory", "Dance", "Earth_science", "Economics",
	"Education", "Engineering", "Entertainment",
	"Family_and_consumer_science", "Film", "Game", "Geography",
	"Handicraft", "Health_science", "History_of_science_and_technology",
	"History", "Hobby", "Language", "Law",
	"Library_and_information_science", "Linguistics", "Literature",
	"Main_Page", "Mathematics", "Music", "Opera", "Painting",
	"Philosophy", "Physics", "Poker", "Political_science", "Psychology",
	"Public_affairs", "Recreation", "Religion", "Sculpture",
	"Sociology", "Sport", "Statistics", "Technology", "Theater",
	"Tourism", "Transport", "Visual_arts_and_design",
	"World_Series_of_Poker" };

/* Suite constructor: load the prefs to determine which
 * wiki to test.
 */

public WikiSuite() {
	try {
		ms_uprefs.importPreferences(new java.io.FileInputStream(
		  "wikitest.prefs" ));
	} catch (java.io.IOException e) {
		/* File probably doesn't exist: no problem, use defaults */
	} catch (InvalidPreferencesFormatException e) {
		System.err.println( "Bad preferences file format: " + e );
	}

	ms_server = ms_uprefs.get( "server", "http://localhost" );
	ms_script = ms_uprefs.get( "script", "/wiki.phtml" );
	ms_articlepath = ms_uprefs.get( "articlepath", "" );
	ms_uploadpath = ms_uprefs.get( "uploadpath", "http://localhost/upload/" );
	ms_mainpage = ms_uprefs.get( "mainpage", "Main Page" );
	ms_sysoppass = ms_uprefs.get( "sysoppass", "adminpass" );

	m_conv = new WebConversation();
}

public void clearCookies() {
	m_conv.clearContents();
}

public static String getSysopPass() {
	return ms_sysoppass;
}

public WebResponse logout() {
	WebResponse wr = viewPage( "Special:Userlogout" );
	clearCookies();
	return wr;
}

public WebResponse loginAs( String name, String password )
throws WikiSuiteFailureException {
	WebResponse wr = null;
	WebRequest req = null;

	try {
		wr = viewPage( "Special:Userlogin" );

		WebForm loginform = WikiSuite.getFormByName( wr, "userlogin" );
		req = loginform.getRequest( "wpLoginattempt" );
		req.setParameter( "wpName", name );
		req.setParameter( "wpPassword", password );
    	wr = getResponse( req );
	} catch (org.xml.sax.SAXException e) {
		throw new WikiSuiteFailureException( "Exception (" + e +
		  ") parsing login form." );
	}
	fine( "Logged in as " + name );
	return wr;
}

/* Utility routine to munge page titles into URL form.
 * Should match the ruotines used by the wiki itself.
 */

public static String titleToUrl( String title ) {
	StringBuffer sb = new StringBuffer( title.length() + 20 );

	if ( "".equals( title ) ) { title = ms_mainpage; }

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

public static String viewUrl( String title ) {
	StringBuffer url = new StringBuffer(200);
	String t = titleToUrl( title );

	int p = ms_articlepath.indexOf( "$1" );
	if ( p >= 0 ) {
		url.append( ms_articlepath );
		url.replace( p, p+2, t );
	} else {
		url.append( ms_server ).append( ms_script ).
		  append( "?title=" ).append( t );
	}
	return url.toString();
}

public static String editUrl( String title ) {
	StringBuffer url = new StringBuffer( 200 );
	String t = titleToUrl( title );

	url.append( ms_server ).append( ms_script ).append( "?title=" )
	  .append( t ).append( "&action=edit" );
	return url.toString();
}

/*
 * Logging/reporting routines:
 */

public static void fatal( String msg ) {
	ms_logger.severe( msg );
	ms_logger.getHandlers()[0].flush();
}

public static void error( String msg ) {
	ms_logger.severe( msg );
	ms_logger.getHandlers()[0].flush();
}

public static void warning( String msg ) {
	ms_logger.warning( msg );
	ms_logger.getHandlers()[0].flush();
}

public static void info( String msg ) {
	ms_logger.info( msg );
	ms_logger.getHandlers()[0].flush();
}

public static void fine( String msg ) {
	ms_logger.fine( msg );
	ms_logger.getHandlers()[0].flush();
}

public static void finer( String msg ) {
	ms_logger.finer( msg );
	ms_logger.getHandlers()[0].flush();
}

public static Level setLoggingLevel( Level newl ) {
	Level oldl = ms_logger.getLevel();

	ms_logger.getHandlers()[0].setLevel( newl );
	ms_logger.setLevel( newl );
	return oldl;
}

/*
 * Utility functions to interact with the wiki:
 */

public WebResponse getResponse( String url ) {
	WebResponse r = null;

	try {
		r = m_conv.getResponse( url );
	} catch (org.xml.sax.SAXException e) {
		warning( "Error parsing received page \"" + url + "\"" );
	} catch (java.net.MalformedURLException e) {
		fatal( "Badly formed URL \"" + url + "\"" );
	} catch (java.io.IOException e) {
		warning( "I/O Error receiving page \"" + url + "\"" );
	}
	return r;
}

public WebResponse getResponse( WebRequest req ) {
	WebResponse r = null;

	try {
		r = m_conv.getResponse( req );
	} catch (org.xml.sax.SAXException e) {
		warning( "Error parsing received page." );
	} catch (java.io.IOException e) {
		warning( "I/O Error receiving page." );
	}
	return r;
}

public static void showResponseTitle( WebResponse wr ) {
	try {
		fine( "Viewing \"" + wr.getTitle() + "\"" );
	} catch (org.xml.sax.SAXException e) {
		error( "Exception (" + e + ")" );
	}
}

public WebResponse viewPage( String title ) {
	WebResponse wr = getResponse( viewUrl( title ) );
	showResponseTitle( wr );
	return wr;
}

public WebResponse editPage( String title ) {
	WebResponse wr = getResponse( editUrl( title ) );
	showResponseTitle( wr );
	return wr;
}

public WebResponse deletePage( String title )
throws WikiSuiteFailureException {
	WebResponse wr = null;

	try {
		wr = loginAs( "WikiSysop", getSysopPass() );
	} catch ( WikiSuiteFailureException e ) {
		error( "Could not log in as Sysop to delete \"" + title + "\"" );
		return null;
	}
	StringBuffer url = new StringBuffer( 200 );
	String t = titleToUrl( title );

	url.append( ms_server ).append( ms_script ).append( "?title=" )
	  .append( t ).append( "&action=delete" );
	wr = getResponse( url.toString() );

	String rt = null;
	try {
		rt = wr.getTitle();
	} catch ( org.xml.sax.SAXException e ) {
		error( "Could not parse response to delete request." );
		wr = logout();
		return null;
	}

	if ( rt.equals( "Internal error" ) ) {
		wr = logout();
		return null;
		/* Can't delete because it doesn't exist: no problem */
	}

	WebForm delform = null;
	try {
		delform = getFormByName( wr, "deleteconfirm" );
	} catch (org.xml.sax.SAXException e) {
		error( "Error parsing delete form." );
		throw new WikiSuiteFailureException( e.toString() );
	}
	WebRequest req = delform.getRequest( "wpConfirmB" );
	req.setParameter( "wpReason", "Deletion for testing" );
	req.setParameter( "wpConfirm", "1" );

	WebResponse ret = null;
	try {
		ret = m_conv.getResponse( req );
	} catch (org.xml.sax.SAXException e) {
		fatal( "Error parsing received page from delete confirmation." );
		throw new WikiSuiteFailureException( e.toString() );
	} catch (java.net.MalformedURLException e) {
		fatal( "Badly formed URL from delete confirmation." );
		throw new WikiSuiteFailureException( e.toString() );
	} catch (java.io.IOException e) {
		fatal( "I/O Error receiving page from delete confirmation." );
		throw new WikiSuiteFailureException( e.toString() );
	}
	wr = logout();
	fine( "Deleted \"" + title + "\"" );
	return wr;
}

public WebResponse loadPageFromFile( String title )
throws WikiSuiteFailureException {
	StringBuffer url = new StringBuffer(200);
	String t = titleToUrl( title );

	url.append( "texts/" ).append( t ).append( ".txt" );
	String text = loadFile( url.toString() );

	WebResponse wr = editPage( title );
	WebForm editform = null;

	try {
		editform = getFormByName( wr, "editform" );
	} catch (org.xml.sax.SAXException e) {
		error( "Error parsing edit form for page \"" + title + "\"." );
		throw new WikiSuiteFailureException( e.toString() );
	}
	WebRequest req = editform.getRequest( "wpSave" );
	req.setParameter( "wpTextbox1", text );

	WebResponse ret = null;
	try {
		ret = m_conv.getResponse( req );
	} catch (org.xml.sax.SAXException e) {
		fatal( "Error parsing received page from form submission." );
		throw new WikiSuiteFailureException( e.toString() );
	} catch (java.net.MalformedURLException e) {
		fatal( "Badly formed URL from form submission." );
		throw new WikiSuiteFailureException( e.toString() );
	} catch (java.io.IOException e) {
		fatal( "I/O Error receiving page from form submission." );
		throw new WikiSuiteFailureException( e.toString() );
	}
	return ret;
}

public static WebForm getFormByName( WebResponse resp, String name )
throws org.xml.sax.SAXException {

	WebForm[] forms = resp.getForms();
	for (int i=0; i < forms.length; ++i) {
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

/*
 * Some utility functions useful for testing and comparing things.
 */

public static void saveText( String text, String filename ) {
	try {
		PrintWriter pw = new PrintWriter( new FileOutputStream( filename ) );
		pw.write( text );
		pw.close();
	} catch( IOException e ) {
		error( "Couldn't write to \"" + filename + "\"" );
	}
}

public static String loadFile( String fname )
{
	FileInputStream fis = null;
	BufferedInputStream bis;

	try {
		fis = new FileInputStream( fname );
	} catch (FileNotFoundException e) {
		error( "File \"" + fname + "\" not found." );
	}
	bis = new BufferedInputStream( fis );

	int r;
	StringBuffer result = new StringBuffer( 2048 );
	byte[] buf = new byte[1024];

	while (true) {
		r = -1;
		try {
			r = bis.read( buf );		
		} catch (IOException e) {
			error( "I/O Error reading \"" + fname + "\"." );
			break;
		}
		if ( r <= 0 ) break;

		try {
			result.append( new String( buf, 0, r, "ISO8859_1" ) );
		} catch ( java.io.UnsupportedEncodingException e ) {
			result.append( new String( buf, 0, r ) );
		}
	}
	try {
		bis.close();
		fis.close();
	} catch (IOException e) {
		warning( "I/O Error closing file \"" + fname + "\"." );
	}
	return result.toString();
}


/*
 * Load database with initial set of pages for testing.
 */
private void initializeDatabase() {

	WebResponse wr = viewPage( "" );
	String text = null;

	try {
		text = wr.getText();
		if ( text.indexOf( "no text in this page" ) < 0 ) {
			error( "Target wiki is not empty." );
			return;
		}
	} catch( IOException e ) {
		error( "Can't access target wiki." );
		return;
	}
	info( "Preloading database with test pages." );
	for (int i = 0; i < preloadedPages.length; ++i) {
		try {
			wr = loadPageFromFile( preloadedPages[i] );
		} catch (WikiSuiteFailureException e) {
			warning( "Failed to load \"" + preloadedPages[i] + "\"" );
		}
		if (wr != null) {
			fine( "Loaded \"" + preloadedPages[i] + "\"" );
		}
	}
	info( "Creating test users." );
	try {
		wr = viewPage( "Special:Userlogin" );
		WebForm loginform = WikiSuite.getFormByName( wr, "userlogin" );
		WebRequest req = loginform.getRequest( "wpCreateaccount" );
		req.setParameter( "wpName", "Fred" );
		req.setParameter( "wpPassword", "Fred" );
		req.setParameter( "wpRetype", "Fred" );
		wr = getResponse( req );

		wr = viewPage( "Special:Userlogin" );
		loginform = WikiSuite.getFormByName( wr, "userlogin" );
		req = loginform.getRequest( "wpCreateaccount" );
		req.setParameter( "wpName", "Barney" );
		req.setParameter( "wpPassword", "Barney" );
		req.setParameter( "wpRetype", "Barney" );
		wr = getResponse( req );
	} catch (org.xml.sax.SAXException e) {
		error( "Exception (" + e + ") parsing login form." );
	}
	clearCookies();
}

/*
 * Start a background thread which does regular fetches of
 * the preloaded page list while all the other tests are
 * going on.
 */

private boolean m_stillrunning = false;
private WikiFetchThread m_wft;
private int m_fetchcount = 0;

private void startBackgroundFetchThread() {
	m_stillrunning = true;
	m_wft = new WikiFetchThread( this );
	m_wft.start();
}

private synchronized void stopBackgroundFetchThread() {
	m_stillrunning = false;
	m_wft.waitfor();
}

public boolean stillRunning() {
	return m_stillrunning;
}

public void incrementFetchcount() {
	++m_fetchcount;
}

/*
 * Main suite starts here.  Interpret command line, load the
 * database, then run the individual tests.
 */

private static boolean f_skipload = false;
private static boolean f_nobackground = false;

public static void main( String[] params ) {
	for ( int i = 0; i < params.length; ++i ) {
		if ( "-s".equals( params[i].substring( 0, 2 ) ) ) {
			f_skipload = true;
		} else if ( "-v".equals( params[i].substring( 0, 2 ) ) ) {
			setLoggingLevel( Level.ALL );
		} else if ( "-n".equals( params[i].substring( 0, 2 ) ) ) {
			f_nobackground = true;
		} else if ( "-h".equals( params[i].substring( 0, 2 ) )
				|| "-?".equals( params[i].substring( 0, 2 ) ) ) {
			System.out.println( "Usage: java WikiSuite [-svn]\n" +
			  "  -s : Skip initial load of database\n" +
			  "  -v : Verbose logging\n" +
			  "  -n : No background thread\n" );
			return;
		}
	}
	WikiSuite ws = new WikiSuite();
	if ( ! f_skipload ) { ws.initializeDatabase(); }

	info( "Started Wikipedia Test Suite" );
	long start_time = System.currentTimeMillis();
	if ( ! f_nobackground ) { ws.startBackgroundFetchThread(); }

	/*
	 * All the actual tests go here.
	 */
	(new LinkTest()).run(ws);
	(new HTMLTest()).run(ws);
	(new EditTest()).run(ws);
	(new ParserTest()).run(ws);
	(new SpecialTest()).run(ws);
	(new UploadTest()).run(ws);
	(new SearchTest()).run(ws);
	/* (new MathTest()).run(ws); */

	/*
	 * Tests are all done. Clean up and report.
	 */
	if ( ! f_nobackground ) { ws.stopBackgroundFetchThread(); }
	info( "Finished Wikipedia Test Suite" );

	long elapsed_time = System.currentTimeMillis() - start_time;

	long t_hr = elapsed_time / 3600000;
	long t_min = (elapsed_time % 3600000) / 60000;
	double t_sec = (double)(elapsed_time % 60000) / 1000.0;

	StringBuffer sb = new StringBuffer(100);
	sb.append( "Total elapsed time: " ).append( t_hr ).append( " hr, " )
	  .append( t_min ).append( " min, " ).append( t_sec ).append( " sec." );
	info( sb.toString() );
	info( "Total background page fetches: " + ws.m_fetchcount );
}

}
