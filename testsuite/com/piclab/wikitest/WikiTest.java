
/*
 * WikiTest is the base class for all the various
 * individual tests of the installed wiki, which
 * will be called by WikiSuite.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;

public class WikiTest {

protected WikiSuite m_suite;
protected WebConversation m_conv;
protected WebResponse m_resp;
protected long m_start, m_stop;

public WikiTest(WikiSuite s) {
	m_suite = s;
	m_conv = new WebConversation();
}

public String testName() { return "Basic"; }

protected boolean runTestInner() {
	try {
		m_resp = m_suite.fetchPage( m_conv, "" );
		WikiTest.showResponse( m_resp );
	} catch (Exception e) {
		System.err.println( "Exception: " + e );
		return false;
	}
	return true;
}

public static void showResponse( WebResponse r ) {
	try {
		System.out.println( " Received \"" + r.getTitle() + "\": " +
 		  r.getText().length() + " bytes of " + r.getContentType() );
	} catch (Exception e) {
		System.err.println( "Exception: " + e );
	}
}

public boolean runTest() {
	m_start = System.currentTimeMillis();
	boolean ret = runTestInner();
	m_stop = System.currentTimeMillis();
	m_conv = null;
	return ret;
}

public boolean runTestAndReport() {
	if ( runTest() ) {
		reportSuccess();
		return true;
	} else {
		reportFailure();
	}
	return false;
}

private void reportTime( boolean result ) {
	StringBuffer sb = new StringBuffer(100);
	java.text.DecimalFormat df =
	  (java.text.DecimalFormat)(java.text.NumberFormat.getInstance());

	try {
		df.applyPattern( "#######0.000" );

		sb.append( "*** ").append( testName() )
		  .append( "                  " );
		sb.setLength( 20 );
		sb.append( result ? "Succeeded" : "Failed   " ).append( "   (" )
		  .append( df.format( (double)(m_stop - m_start) / 1000.0 ) )
		  .append( " secs)" );

		System.out.println( sb );
	} catch (Exception e) {
		System.err.println( "Exception: " + e );
	}
}

public void reportSuccess() { reportTime( true ); }
public void reportFailure() { reportTime( false ); }


public static void main( String[] params ) {
	WikiSuite ws = new WikiSuite();
	WikiTest wt = new WikiTest( ws );
	wt.runTestAndReport();
}

}
