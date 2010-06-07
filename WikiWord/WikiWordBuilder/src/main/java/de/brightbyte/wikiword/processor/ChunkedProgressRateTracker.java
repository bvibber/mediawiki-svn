/**
 * 
 */
package de.brightbyte.wikiword.processor;

import java.text.MessageFormat;

import de.brightbyte.job.Progress;
import de.brightbyte.job.ProgressRateTracker;

public class ChunkedProgressRateTracker extends ProgressRateTracker {
	protected long counter;
	protected long timestamp;
	protected String name;
	
	public ChunkedProgressRateTracker(String name) {
		this.name = name;
		this.timestamp = System.currentTimeMillis();
		this.counter = 0;
	}
	
	public void step() {
		step(1);
	}
	
	public void step(int c) {
		counter+= c;
	}
	
	public long getCurrentChunkSize() {
		return counter;
	}
	
	public long getLastChunkTime() {
		return timestamp;
	}
	
	public boolean chunkIf(long counter, int sec) {
		if (sec>0 && this.timestamp<=0) this.timestamp = System.currentTimeMillis(); 
		long d = System.currentTimeMillis() - this.timestamp;
		if ((counter>0 && this.counter>=0 && this.counter >= counter) 
				|| (sec>0 && this.timestamp>0 && d>sec*1000)) {
			chunk();
			return true;
		} else {
			return false;
		}
	}
	
	public void chunk() {
		super.progress(new Progress.Event(Progress.PROGRESS, null, name, 1, position+counter, null));
		counter = 0;
		timestamp = System.currentTimeMillis();
	}
	
	@Override
	public String toString() {
		return MessageFormat.format("{0}: {1,number,0} ({2,number,0.0}/sec, currently {3,number,0.0}/sec)", name, position, getAverageRate(), getCurrentRate());
	}
}