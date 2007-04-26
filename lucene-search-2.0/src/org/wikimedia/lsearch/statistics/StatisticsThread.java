/*
 * Created on Jan 23, 2007
 *
 */
package org.wikimedia.lsearch.statistics;

import org.wikimedia.lsearch.frontend.SearchServer;

/**
 * Ganglia statistics thread. 
 * 
 * @author rainman
 *
 */
public class StatisticsThread extends Thread {
	/* (non-Javadoc)
	 * @see java.lang.Runnable#run()
	 */
	public void run() {
		for(;;) {
			try {
				Thread.sleep(SearchServer.statsPeriod);
			} catch (InterruptedException e) {
				// interrupted, be silent and continue
			}
			SearchServer.stats.updateGanglia();
		}		
	}
}
