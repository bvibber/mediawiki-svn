
/*
 * Test that basic navigation around the wiki with
 * internal links is working.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;
import java.io.*;

public class EditTest extends WikiTest {

public EditTest( WikiSuite ws ) { super(ws); }

public String testName() { return "Editing"; }

protected boolean runTestInner() {
	try {
		m_resp = m_suite.editPage( m_conv, "Radio" );
		WikiTest.showResponse( m_resp );

		WebForm f = m_suite.getFormByName( m_resp, "editform" );
		String text = f.getParameterValue( "wpTextbox1" );

		PrintWriter w = new PrintWriter( new FileWriter("radio.txt") );
		w.println( text );
		w.close();
	} catch (Exception e) {
		System.err.println( "Exception: " + e );
		return false;
	}
	return true;
}

public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();
	EditTest wt = new EditTest( ws );
	wt.runTestAndReport();
}

}
