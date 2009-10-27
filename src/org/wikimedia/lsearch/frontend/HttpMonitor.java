package org.wikimedia.lsearch.frontend;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.Hashtable;
import java.util.List;
import java.util.Map.Entry;

import org.apache.log4j.Logger;

public class HttpMonitor extends Thread {
	static Logger log = Logger.getLogger(HttpMonitor.class);
	protected static HttpMonitor instance=null;
	/** times when http request have been started */
	protected Hashtable<HttpHandler,Long> startTimes = new Hashtable<HttpHandler,Long>();
	
	/** threshold for reporting 10s */
	protected long threshold = 10000;
	
	private HttpMonitor(){}
	
	/** Get a running HttpMonitor instance */
	public synchronized static HttpMonitor getInstance(){
		if(instance == null){
			instance = new HttpMonitor();
			instance.start();
		}
			
		return instance;
	}
	
	@Override
	public void run() {
		log.info("HttpMonitor thread started");
		for(;;){
			try {				
				// sleep until next check
				Thread.sleep(threshold);
				long cur = System.currentTimeMillis();
				
				// check for long-running http request
				Hashtable<HttpHandler,Long> times = (Hashtable<HttpHandler, Long>) startTimes.clone(); // clone to avoid sync
				for(Entry<HttpHandler,Long> e : times.entrySet()){
					long timeWait = cur - e.getValue();
					if(timeWait > threshold){					
						log.warn(e.getKey()+" is waiting for "+timeWait+" ms on "+e.getKey().rawUri);
					}
				}
			} catch (InterruptedException e) {
				log.error("HttpMonitor thread interrupted",e);
			}
		}
	}
	
	/** Mark http request start */
	public void requestStart(HttpHandler thread){
		startTimes.put(thread,System.currentTimeMillis());
	}
	
	/** Mark http request end */
	public void requestEnd(HttpHandler thread){
		startTimes.remove(thread);
	}
	
	public String printReport(){
		StringBuilder sb = new StringBuilder();
		
		Hashtable<HttpHandler,Long> times = (Hashtable<HttpHandler, Long>) startTimes.clone(); // clone to avoid sync
		ArrayList<Entry<HttpHandler, Long>> sorted = new ArrayList<Entry<HttpHandler,Long>>(times.entrySet()); 
		Collections.sort(sorted, new Comparator<Entry<HttpHandler,Long>>() {
			@Override
			public int compare(Entry<HttpHandler, Long> o1,
					Entry<HttpHandler, Long> o2) {
				return (int) (o2.getValue() - o1.getValue());
			}
		});
		
		long cur = System.currentTimeMillis();
		
		for(Entry<HttpHandler,Long> e : sorted){
			long timeWait = cur - e.getValue();
			sb.append("[ "+timeWait+" ms ] "+ e.getKey().rawUri +"\n");
		}
		
		return sb.toString();
	}
	
}
