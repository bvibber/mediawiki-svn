
/*
 * WikiTest is the base class for all the various
 * individual tests of the installed wiki, which
 * will be called by WikiSuite.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;

public class WikiTest implements Runnable {

protected WikiSuite m_suite;
protected long m_start, m_stop;

/* All subclasses of WikiTest should override testName()
 * to return a useful name and runTest() to perform the actual
 * tests.  runTest() hould return true on success.  You also
 * need to duplicate the constructor since that's not
 * inherited.
 */

public WikiTest(WikiSuite s) { m_suite = s; }

public String testName() { return "Basic"; }

protected boolean runTest() throws Exception {
	return true;
}

/* You generally won't want to override run(), as it does
 * all the extra stuff around the actual test invocation.
 */

public void run() {
	boolean result = false;

	StringBuffer sb = new StringBuffer(100);
	java.text.DecimalFormat df =
	  (java.text.DecimalFormat)(java.text.NumberFormat.getInstance());

	m_start = System.currentTimeMillis();

	try {
		result = runTest();
	} catch (Exception e) {
		WikiSuite.error( "Exception (" + e + ") running test \"" +
		  testName() + "\"" );
		result = false;
	}
	m_stop = System.currentTimeMillis();

	try {
		df.applyPattern( "#######0.000" );

		sb.append( "Test \"" ).append( testName() )
		  .append( "\"                  " );
		sb.setLength( 20 );
		sb.append( result ? "Succeeded" : "Failed   " ).append( "   (" )
		  .append( df.format( (double)(m_stop - m_start) / 1000.0 ) )
		  .append( " secs)" );

		WikiSuite.info( sb.toString() );
	} catch (Exception e) {
		WikiSuite.error( "Exception (" + e + ") running test \"" +
		  testName() + "\"" );
	}
}

}
