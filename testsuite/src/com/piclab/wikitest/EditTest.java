
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
	boolean result = true;

	WebResponse wr = m_suite.editPage( "Agriculture" );
	WebForm editform = null;
	WebRequest req = null;

	editform = WikiSuite.getFormByName( wr, "editform" );
	req = editform.getRequest( "wpSave" );
	String text = req.getParameter( "wpTextbox1" );
	req.setParameter( "wpTextbox1", text + "\nEdited for testing." );
	wr = m_suite.getResponse( req );

	wr = m_suite.viewPage( "Special:Recentchanges" );
	text = wr.getText();
	if ( text.indexOf( "Agriculture" ) < 0 ) { result = false; }

	wr = m_suite.viewPage( "Omaha" ); /* Not preloaded */
	text = wr.getText();
	if ( text.indexOf( "no text in this page" ) < 0 ) { result = false; }

	WebLink l = wr.getFirstMatchingLink( WebLink.MATCH_CONTAINED_TEXT,
	  "Edit this page" );
	wr = l.click();

	editform = WikiSuite.getFormByName( wr, "editform" );
	req = editform.getRequest( "wpSave" );
	req.setParameter( "wpTextbox1", "'''Omaha''' is a city in [[Nebraska]]" );
	wr = m_suite.getResponse( req );

	wr = m_suite.viewPage( "Omaha" );
	text = wr.getText();
	if ( text.indexOf( "no text in this page" ) >= 0 ) { result = false; }
	if ( text.indexOf( "Nebraska" ) < 0 ) { result = false; }

	return result;
}

public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();
	EditTest wt = new EditTest( ws );
	wt.run();
}

}
