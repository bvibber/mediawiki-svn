
/*
 * Test that basic navigation around the wiki with
 * internal links is working.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;

public class LinkTest extends WikiTest {

public LinkTest( WikiSuite ws ) { super(ws); }

public String testName() { return "Links"; }

protected boolean runTestInner() {
	try {
		m_resp = m_suite.fetchPage( m_conv, "" );
		WikiTest.showResponse( m_resp );

		WebLink l = m_resp.getFirstMatchingLink(
		  WebLink.MATCH_CONTAINED_TEXT, "physics" );
		m_resp = l.click();
		WikiTest.showResponse( m_resp );

		l = m_resp.getFirstMatchingLink(
		  WebLink.MATCH_CONTAINED_TEXT, "radio" );
		m_resp = l.click();
		WikiTest.showResponse( m_resp );
	} catch (Exception e) {
		System.err.println( "Exception: " + e );
		return false;
	}
	return true;
}

public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();
	LinkTest wt = new LinkTest( ws );
	wt.runTestAndReport();
}

}
