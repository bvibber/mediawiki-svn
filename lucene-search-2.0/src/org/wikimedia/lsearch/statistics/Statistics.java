package org.wikimedia.lsearch.statistics;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.text.MessageFormat;

import org.apache.log4j.Logger;

/**
 * Class that reports statistics to ganglia daemon via gmetric. 
 * 
 * @author Brion Vibber
 *
 */
public class Statistics {
	static org.apache.log4j.Logger log = Logger.getLogger(Statistics.class);
	/** Calculate rates as average over the last N milliseconds */
	long maxDelta;
	
	/** Number of items to keep in the ring buffer */
	int maxItems;
	
	/** Ring buffer use count and pointers... */
	int usedItems = 0;
	int start = 0;
	int end = -1;
	
	/** For each request, records if successfully handled or discarded due to overflow */
	boolean[] handled;
	
	/** Timestamp for each request in the statistics buffer */
	long[] times;
	
	/** Milliseconds taken to service each request. */
	long[] deltas;
	
	/** Number of active threads at the time this request hit the wire */
	int[] activeThreads;
	
	/** For Ganglia callouts */
	public int GangliaPort = 0;
	public String GangliaInterface = "";
		
	public Statistics(int maxItems, long maxDelta) {
		this.maxItems = maxItems;
		this.maxDelta = maxDelta;
		
		handled = new boolean[maxItems];
		times = new long[maxItems];
		deltas = new long[maxItems];
		activeThreads = new int[maxItems];
	}
		
	synchronized public void add(boolean status, long delta, int threads) {
		end++;
		if (end == maxItems)
			end = 0;
		if (usedItems == maxItems) {
			start++;
			if (start == maxItems)
				start = 0;
		} else {
			usedItems++;
		}
		handled[end] = status;
		times[end] = System.currentTimeMillis();
		deltas[end] = delta;
		activeThreads[end] = threads;
	}
	
	/**
	 * Provide an array of data from rolling average of recent requests:
	 * 0: rate of requests handled successfully (req/sec)
	 * 1: rate of requests dropped because the queue got too long (req/sec)
	 * 2: amount of time taken to serve successfully requests (ms)
	 * 3: number of simultaneously active threads (count)
	 * @return
	 */
	synchronized public double[] collect() {
		long handledSum = 0, discardSum = 0;
		long deltaSum = 0, threadSum = 0;
		long timeDelta = maxDelta;
		long now = System.currentTimeMillis();
		if (end != -1) {
			long availableDelta = times[end] - times[start];
			if (availableDelta < maxDelta)
				timeDelta = availableDelta;
			for (int i = 0, j = start; i < usedItems; i++) {
				if (now - times[j] <= maxDelta) {
					if (handled[j]) {
						handledSum++;
						deltaSum += deltas[j];
						threadSum += activeThreads[j];
					} else {
						discardSum++;
					}
				}
				j++;
				if (j == maxItems)
					j = 0;
			}
		}
		//System.out.printf("XXX %d %d %d\n", handledSum, discardSum, timeDelta);
		double handleRate = (timeDelta == 0) ? 0.0 : (double)handledSum * 1000.0 / (double)timeDelta;
		double discardRate = (timeDelta == 0) ? 0.0 : (double)discardSum * 1000.0 / (double)timeDelta;
		double serviceTime = (handledSum == 0) ? 0.0 : (double)deltaSum / (double)handledSum;
		double threadCount = (handledSum == 0) ? 0.0 : (double)threadSum / (double)handledSum;
		return new double[] {handleRate, discardRate, serviceTime, threadCount};
	}
	
	/**
	 * Provide a formatted line summarizing the current data.
	 * @return
	 */
	public String summarize() {
		double[] data = collect();
		return MessageFormat.format("handle={0,number,#.###} discard={1,number,#.###} service={2,number,#.###} threads={3,number,#.####}",
				new Object[] {new Double(data[0]), new Double(data[1]), new Double(data[2]), new Double(data[3])});
	}
	
	synchronized public String state() {
		return MessageFormat.format("used={0} start={1} end={2}", 
				new Object[] { new Integer(usedItems), new Integer(start), new Integer(end)});
	}
	
	public void updateGanglia() {
		double[] data = collect();
		sendGanglia("search_rate", data[0], "requests/sec");
		sendGanglia("search_discards", data[1], "requests/sec");
		sendGanglia("search_time", data[2], "ms");
		sendGanglia("search_threads", data[3], "threads");
	}
	
	private void sendGanglia(String name, double value, String units) {
		String portOverride = "";
		if (GangliaPort > 0)
			portOverride = "--mcast_port " + GangliaPort;
		String ifOverride = "";
		if (GangliaInterface.length() > 0)
			ifOverride = "--mcast_if " + GangliaInterface;
		String command = MessageFormat.format(
				"/usr/bin/gmetric --name={0} --value={1,number,#.###} --type=double --units={2} --dmax={3} {4} {5}",
				new Object [] {	name, new Double(value), units, new Long(maxDelta / 1000L), portOverride, ifOverride});
		Process gmetric;
		try {
			log.debug("Executing shell command "+command);
			gmetric = Runtime.getRuntime().exec(command);
			gmetric.waitFor();
			if(gmetric.exitValue()!=0){
				log.warn("Got exit value "+gmetric.exitValue()+" while executing "+command);
				//log.warn("Error was: "+new BufferedReader(new InputStreamReader(gmetric.getInputStream())).readLine());
			}
		} catch (IOException e) {
			e.printStackTrace();
		} catch (InterruptedException e) {
			e.printStackTrace();
		}		
	}
}
