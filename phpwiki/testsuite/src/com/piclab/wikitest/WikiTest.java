
/*
 * WikiTest is the base class for all the various
 * individual tests of the installed wiki, which
 * will be called by WikiSuite.
 */

package com.piclab.wikitest;
import com.meterware.httpunit.*;

public class WikiTest {

protected WikiSuite m_suite = null;
protected long m_start, m_stop;
protected boolean m_verboseflag = false;

/* All subclasses of WikiTest should override testName()
 * to return a useful name and runTest() to perform the actual
 * tests. runTest() should return true on success. You can
 * also overrise initTest() if you like; it gets run before
 * the individual test timer is started.
 */

public String testName() { return "Error"; }

protected int initTest() throws Exception {
	return 0;
}

protected int runTest() throws Exception {
	return 0;
}

/*
 * This is the primary entry point:
 */

public void run( WikiSuite ws ) {
	m_suite = ws;
	run();
}

private void run() {
	int result = 0;

	/* assert( m_suite != null ); */

	java.util.logging.Level ll = null;
	if ( m_verboseflag ) {
		ll = WikiSuite.setLoggingLevel( java.util.logging.Level.FINE );
	}

	try {
		result = initTest();
	} catch ( Exception e ) {
		WikiSuite.error( "Exception (" + e + ") initializing test \"" +
		  testName() + "\"" );
		result = 1;
	}
	if ( result != 0 ) {
		WikiSuite.error( "Test \"" + testName() +
		  "\" failed to initialize with code " + result );
		return;
	}
	WikiSuite.info( "Started test \"" + testName() + "\"" );
	m_start = System.currentTimeMillis();

	try {
		result = runTest();
	} catch (Exception e) {
		WikiSuite.error( "Exception (" + e + ") running test \"" +
		  testName() + "\"" );
		result = 2;
	}
	m_stop = System.currentTimeMillis();
	double time = (double)(m_stop - m_start) / 1000.0;

	StringBuffer sb = new StringBuffer(100);
	sb.append( "Test \"" ).append( testName() ).append( "\" " )
	  .append( (result==0) ? "Succeeded" : "Failed   " ).append( "   (" )
	  .append( WikiSuite.threeDecimals( time ) ).append( " secs)" );
	WikiSuite.info( sb.toString() );

	if ( m_verboseflag ) {
		WikiSuite.setLoggingLevel( ll );
	}
}

/*
 * General utility function
 */

protected int fail( int code ) {
	WikiSuite.error( "Test \"" + testName() + "\" failed with code " + code );
	return code;
}

/*
 * The main method of a subclass should be able to just create 
 * an instance of itself and call runSingle() to perform a
 * standalone test, and we'll handle the commandline and
 * setting up a suite object, etc. They are of course welcome
 * to set up a more complex main if they want.
 */

public void runSingle( String[] params ) {
	/*
	 * Do command line. For now, just verbose flag.
	 */
	for ( int i = 0; i < params.length; ++i ) {
		if ( "-v".equals( params[i].substring( 0, 2 ) ) ) {
			m_verboseflag = true;
		}
	}
	run( new WikiSuite() );
}

public static void main( String params ) {
	System.out.println( "WikiTest is not a runnable class." );
}

}
