
/*
 * WikiFetchThread is the background thread that fetches
 * pages from the preload list until the wuite is done.
 */

package com.piclab.wikitest;

import com.meterware.httpunit.*;

public class WikiFetchThread extends Thread {

private WikiSuite m_suite;
private WebConversation m_conv;
private volatile boolean m_running;

public WikiFetchThread(WikiSuite s) {
	m_suite = s;
	m_conv = new WebConversation();
}

public void run() {
	int index = 0;

	WikiSuite.fine( "Started background page-fetch thread." );
	m_running = true;

	while ( m_suite.stillRunning() ) {
		String url = WikiSuite.editUrl( WikiSuite.preloadedPages[index] );
		try {
			WebResponse wr = m_conv.getResponse( url );
		} catch (Exception e) {
			WikiSuite.warning( "Error (" + e + ") fetching \"" +
			  WikiSuite.preloadedPages[index] + "\"" );
		}

		WikiSuite.fine( "Fetched \"" + WikiSuite.preloadedPages[index] + "\"" );
		if ( ++index >= WikiSuite.preloadedPages.length ) { index = 0; }
	}
	m_running = false;

	WikiSuite.fine( "Terminated background page-fetch thread." );
}


/*
 * After suite sets stillRunning() to false, this thread will
 * eventually quit, but we have suite call this function to wait
 * for it so that we don't get fetches after the final report.
 */

public void waitfor() {
	do {
	} while (m_running);
}

}
