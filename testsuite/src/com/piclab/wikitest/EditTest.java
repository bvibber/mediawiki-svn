
/*
 * Test that basic page editing is working.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;
import java.io.*;

public class EditTest extends WikiTest {

public EditTest( WikiSuite ws ) { super(ws); }

public String testName() { return "Editing"; }

protected boolean runTest() throws Exception {
	m_suite.clearCookies();

	/* java.util.logging.Level l = WikiSuite.setLoggingLevel(
	  java.util.logging.Level.ALL ); */

	if ( ! part1() ) { throw new WikiSuiteFailureException( "Part 1" ); }
	if ( ! part2() ) { throw new WikiSuiteFailureException( "Part 2" ); }
	if ( ! part3() ) { throw new WikiSuiteFailureException( "Part 3" ); }
	if ( ! part4() ) { throw new WikiSuiteFailureException( "Part 4" ); }
	if ( ! part5() ) { throw new WikiSuiteFailureException( "Part 5" ); }

	/* WikiSuite.setLoggingLevel( l ); */
	return true;
}

private boolean part1() throws Exception {
	/*
	 * Add a line to some pages.  See that the change
	 * shows up in Recent Changes, and the new text appears.
	 */
	boolean result = true;

	WebResponse wr = addText( "Agriculture",
	  "Edited for testing: 85769476243364759655." );

	wr = m_suite.viewPage( "Special:Recentchanges" );
	String text = wr.getText();
	if ( text.indexOf( "Agriculture" ) < 0 ) { result = false; }

	wr = m_suite.viewPage( "Agriculture" );
	text = wr.getText();
	if ( text.indexOf( "85769476243364759655" ) < 0 ) { result = false; }

	wr = addText( "Talk:Physics", "Edited for testing: 98762415237651243634" );
	wr = addText( "User:Fred", "Edited for testing: 54637465888374655394" );

	wr = m_suite.viewPage( "Special:Recentchanges" );
	text = wr.getText();
	if ( text.indexOf( "Physics" ) < 0 ) { result = false; }
	if ( text.indexOf( "Mathematics" ) < 0 ) { result = false; }

	wr = m_suite.viewPage( "Talk:Physics" );
	text = wr.getText();
	if ( text.indexOf( "98762415237651243634" ) < 0 ) { result = false; }
	if ( text.indexOf( "54637465888374655394" ) >= 0 ) { result = false; }

	wr = m_suite.viewPage( "User:Fred" );
	text = wr.getText();
	if ( text.indexOf( "54637465888374655394" ) < 0 ) { result = false; }
	if ( text.indexOf( "98762415237651243634" ) >= 0 ) { result = false; }

	return result;
}

private boolean part2() throws Exception {
	/*
	 * Create a new page, verify it, add to it, replace it.
	 */
	boolean result = true;

	WebResponse wr = m_suite.viewPage( "Omaha" ); /* Not preloaded */
	String text = wr.getText();
	if ( text.indexOf( "no text in this page" ) < 0 ) { result = false; }

	wr = addText( "Omaha", "'''Omaha''' is a city in [[Florida]]" );
	wr = m_suite.viewPage( "Omaha" );
	text = wr.getText();

	if ( text.indexOf( "no text in this page" ) >= 0 ) { result = false; }
	if ( text.indexOf( "Florida" ) < 0 ) { result = false; }

	wr = addText( "Omaha", "And a [[poker]] game for masochists." );
	wr = m_suite.viewPage( "Special:Recentchanges" );
	text = wr.getText();
	if ( text.indexOf( "Omaha" ) < 0 ) { result = false; }

	wr = m_suite.viewPage( "Omaha" );
	text = wr.getText();
	if ( text.indexOf( "Florida" ) < 0 ) { result = false; }
	if ( text.indexOf( "poker" ) < 0 ) { result = false; }

	wr = m_suite.editPage( "Omaha" );
	WebForm editform = WikiSuite.getFormByName( wr, "editform" );
	WebRequest req = editform.getRequest( "wpSave" );
	req.setParameter( "wpTextbox1", "See: \n" +
	  "* [[Omaha, Nebraska]]\n* [[Omaha holdem|Omaha hold'em]]" );
	wr = m_suite.getResponse( req );

	text = wr.getText();
	if ( text.indexOf( "Florida" ) >= 0 ) { result = false; }
	if ( text.indexOf( "poker" ) >= 0 ) { result = false; }
	if ( text.indexOf( "Nebraska" ) < 0 ) { result = false; }
	if ( text.indexOf( "holdem" ) < 0 ) { result = false; }

	return result;
}

private boolean part3() throws Exception {
	/*
	 * Log in and make some edits as a user.
	 */
	boolean result = true;

	WebResponse wr = m_suite.loginAs( "Fred", "Fred" );
	wr = addText( "Talk:Language", "This page sucks!" );

	wr = m_suite.viewPage( "Special:Recentchanges" );
	String text = wr.getText();
	if ( text.indexOf( "Fred" ) < 0 ) { result = false; }

	wr = m_suite.loginAs( "Barney", "Barney" );
	wr = addText( "Talk:Language", "No it doesn't" );

	wr = m_suite.viewPage( "Special:Recentchanges" );
	text = wr.getText();
	if ( text.indexOf( "Barney" ) < 0 ) { result = false; }

	wr = m_suite.viewPage( "Talk:Language" );
	text = wr.getText();
	if ( text.indexOf( "sucks" ) < 0 ) { result = false; }
	if ( text.indexOf( "doesn't" ) < 0 ) { result = false; }

	WebLink l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Older versions" );
	wr = l.click();
	text = wr.getText();
	if ( text.indexOf( "Fred" ) < 0 ) { result = false; }
	if ( text.indexOf( "Barney" ) < 0 ) { result = false; }

	return result;
}

private boolean part4() throws Exception {
	/*
	 * Verify edit conflict handling.
	 */
	boolean result = true;

	return result;
}

private boolean part5() throws Exception {
	/*
	 * Verify page protection features.
	 */
	boolean result = true;

	WebResponse wr = m_suite.viewPage( "Wikipedia:Upload_log" );
	String text = wr.getText();
	if ( text.indexOf( "Protected page" ) < 0 ) { result = false; }
	if ( text.indexOf( "Edit this page" ) >= 0 ) { result = false; }

	return result;
}

/*
 * Add a given piece of text to the given page
 */

private WebResponse addText( String page, String text )
throws Exception {
	WebResponse wr = m_suite.editPage( page );

	WebForm editform = WikiSuite.getFormByName( wr, "editform" );
	WebRequest req = editform.getRequest( "wpSave" );
	String old = req.getParameter( "wpTextbox1" );
	req.setParameter( "wpTextbox1", old + "\n" + text );

	return m_suite.getResponse( req );
}


public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();
	EditTest wt = new EditTest( ws );
	wt.run();
}

}
