
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
	boolean result = true;
	WebResponse wr = m_suite.viewPage( "" ); /* Main page */

	WebLink l = wr.getFirstMatchingLink(
	  WebLink.MATCH_CONTAINED_TEXT, "game" );
	wr = l.click();
	WikiSuite.showResponseTitle( wr );

	l = wr.getFirstMatchingLink(
	  WebLink.MATCH_CONTAINED_TEXT, "card" );
	wr = l.click();
	WikiSuite.showResponseTitle( wr );

	l = wr.getFirstMatchingLink(
	  WebLink.MATCH_CONTAINED_TEXT, "poker" );
	wr = l.click();
	WikiSuite.showResponseTitle( wr );

	return result;
}

public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();
	LinkTest wt = new LinkTest( ws );
	wt.run();
}

}
