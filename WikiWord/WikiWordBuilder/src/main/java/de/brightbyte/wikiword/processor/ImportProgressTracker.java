/**
 * 
 */
package de.brightbyte.wikiword.processor;

import java.text.MessageFormat;

import de.brightbyte.job.Progress;
import de.brightbyte.job.ProgressRateTracker;

public class ImportProgressTracker extends ProgressRateTracker {
	protected long counter = 0;
	protected String name;
	
	public ImportProgressTracker(String name) {
		this.name = name;
	}
	
	public void step() {
		step(1);
	}
	
	public void step(int c) {
		counter+= c;
	}
	
	public void chunk() {
		super.progress(new Progress.Event(Progress.PROGRESS, null, name, 1, position+counter, null));
		counter = 0;
	}
	
	@Override
	public String toString() {
		return MessageFormat.format("{0}: {1,number,0} ({2,number,0.0}/sec, currently {3,number,0.0}/sec)", name, position, getAverageRate(), getCurrentRate());
	}
}