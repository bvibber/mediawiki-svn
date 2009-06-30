/**
 * 
 */
package de.brightbyte.wikiword.processor;

import java.text.MessageFormat;

import de.brightbyte.job.Progress;
import de.brightbyte.job.ProgressRateTracker;

public class MemoryTracker extends ProgressRateTracker {
	protected long baseline = 0;
	protected long used = 0;
	
	public MemoryTracker() {
	}
	
	public long getUsedMemory() {
		return Runtime.getRuntime().totalMemory() - Runtime.getRuntime().freeMemory(); 
	}
	
	public void step() {
		if (baseline<=0) baseline = getUsedMemory();
	}
	
	public void chunk() {
		if (baseline<=0) baseline = getUsedMemory();
		used = getUsedMemory();
		super.progress(new Progress.Event(Progress.PROGRESS, null, "memory", Runtime.getRuntime().totalMemory()  - baseline, used - baseline, null));
	}
	
	@Override
	public String toString() {
		//return MessageFormat.format("{0}: {1,number,0}KB ({2,number,0.0}KB/sec, currently {3,number,0.0}KB/sec)", "memory", position/1024, getAverageRate()/1024, getCurrentRate()/1024);
		return MessageFormat.format("{0}: {1,number,0}KB (free: {2,number,0}KB; used: {3,number,0}KB;)", "memory", position/1024, Runtime.getRuntime().freeMemory()/1024, used/1024);
	}
}