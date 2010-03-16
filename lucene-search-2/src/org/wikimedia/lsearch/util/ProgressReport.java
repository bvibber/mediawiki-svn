package org.wikimedia.lsearch.util;

import java.text.MessageFormat;

/**
 * 
 * Format a progress of some operation, i.e. dictionary rebuild
 * 
 * @author rainman
 *
 */
public class ProgressReport {
	protected long start;
	protected int max;
	protected int count = 0;
	protected int report;
	protected String what;
	protected MessageFormat noMax = new MessageFormat("Processed {0} {1} ({2}/sec)");
	protected MessageFormat withMax = new MessageFormat("Processed {0} / {1} {2} ({3}/sec)");
	protected MessageFormat finished = new MessageFormat("Finished {0} {1} ({2}/sec) in {3}");
	
	/** Report on 1000 events, without target number of events */
	public ProgressReport(){
		this("terms",1000,-1);
	}
	
	/** Reports without explicit target */
	public ProgressReport(String what, int report){
		this(what,report,-1);
	}
	
	/**
	 * 
	 * @param what what is being processed (e.g. terms, titles, documents.. )
	 * @param report after how many events to print out a report
	 * @param max target number of events
	 */
	public ProgressReport(String what, int report, int max){
		this.max = max;
		this.report = report;
		this.what = what;
		start = System.currentTimeMillis();
	}
	
	public void inc(){
		count++;
		if(count % report == 0){
			long now = System.currentTimeMillis();
			if(max == -1)
				System.out.println(noMax.format(new Object[] {count, what, rate(now)}));
			else
				System.out.println(withMax.format(new Object[] {count, max, what, rate(now)}));
		}
	}
	
	private double rate(long now){
		return (count*1000.0)/(now-start);
	}
	
	public void finish(){
		long now = System.currentTimeMillis();
		System.out.println(finished.format(new Object[]{count, what, rate(now), formatTime(now-start)}));
	}
	
	public static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}
	
	
}
