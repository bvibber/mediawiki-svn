
/*
 * Test that basic navigation around the wiki with
 * internal links is working.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;

public class LinkTest extends WikiTest {

public LinkTest( WikiSuite ws ) { super(ws); }

public String testName() { return "Links"; }

protected boolean runTest() throws Exception {
	m_suite.clearCookies(); /* Make sure we aren't logged in */

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
	 * Check that we can click through from main page to games,
	 * card games, poker, world series.
	 */
	WebResponse wr = m_suite.viewPage( "" ); /* Main page */
	WebLink l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Game" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Card" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Poker" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "World Series" );
	wr = l.click();

	return true;
}

private boolean part2() throws Exception {
	/* 
	 * Poker page should have some standard links on it, and should
	 * _not_ have an upload link or user stat links on it because we
	 * aren't logged in.
	 */
	boolean result = true;

	WebResponse wr = m_suite.viewPage( "Poker" );
	WebLink l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Printable version" );
	result = (l != null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Related changes" );
	result = (l != null);

	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Upload file" );
	result = (l == null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "My watchlist" );
	result = (l == null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "My contributions" );
	result = (l == null);

	return result;
}

private boolean part3() throws Exception {
	/*
	 * Talk:Poker was not preloaded, so we should be on an edit form
	 * when we click that link from the Poker page.  Add a comment,
	 * then check for some standard links on the new talk page and
	 * the resulting history page.
	 */
	boolean result = true;

	WebResponse wr = m_suite.viewPage( "Poker" );
	WebLink l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Discuss this page" );
	wr = l.click();

	WebForm editform = WikiSuite.getFormByName( wr, "editform" );
    WebRequest req = editform.getRequest( "wpSave" );
    req.setParameter( "wpTextbox1", "Great article!" );
    wr = m_suite.getResponse( req );

	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "View article" );
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Older versions" );
	wr = l.click();

	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Current revision" );
	result = (l != null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "View discussion" );
	result = (l != null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "View article" );
	wr = l.click();

	return result;
}

private boolean part4() throws Exception {
	/*
	 * Let's log in now and verify that things are changed.
	 */
	boolean result = true;

	WebResponse wr = m_suite.viewPage( "Poker" );
	WebLink l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Log in" );
	wr = l.click();

	WebForm loginform = WikiSuite.getFormByName( wr, "userlogin" );
	WebRequest req = loginform.getRequest( "wpLoginattempt" );
	req.setParameter( "wpName", "Fred" );
	req.setParameter( "wpPassword", "Fred" );
	wr = m_suite.getResponse( req );

	String text = wr.getText();
	if ( text.indexOf( "successful" ) < 0 ) { throw new
	  WikiSuiteFailureException( "Could not log in" ); }

	/*
	 * Should have a "return to" link.
	 */
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Poker" );
	wr = l.click();

	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "My watchlist" );
	result = (l != null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "My contributions" );
	result = (l != null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "new messages" );
	result = (l == null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Upload file" );
	wr = l.click();

	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "list of uploaded images" );
	result = (l != null);
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "upload log" );
	result = (l != null);

	return result;
}

private boolean part5() throws Exception {
	/*
	 * Verify that the user page and user talk page are OK.
	 */
    boolean result = true;

	WebResponse wr = m_suite.viewPage( "" );
	WebLink l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Fred" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "User contributions" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Talk" );
	wr = l.click();

	/*
	 * Log out, clear cookies, edit talk page, then log back in and
	 * verify "new messages" link.
	 */
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Log out" );
	wr = l.click();
	m_suite.clearCookies();

	wr = m_suite.editPage( "User talk:Fred" );
	WebForm editform = WikiSuite.getFormByName( wr, "editform" );
    WebRequest req = editform.getRequest( "wpSave" );
    req.setParameter( "wpTextbox1", "Wake up!" );
    wr = m_suite.getResponse( req );

	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Main Page" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Log in" );
	wr = l.click();

	WebForm loginform = WikiSuite.getFormByName( wr, "userlogin" );
	req = loginform.getRequest( "wpLoginattempt" );
	req.setParameter( "wpName", "Fred" );
	req.setParameter( "wpPassword", "Fred" );
	wr = m_suite.getResponse( req );

	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "new messages" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "Main Page" );
	wr = l.click();
	l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT, "new messages" );
	result = (l == null);

	return result;
}

public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();
	LinkTest wt = new LinkTest( ws );
	wt.run();
}

}
