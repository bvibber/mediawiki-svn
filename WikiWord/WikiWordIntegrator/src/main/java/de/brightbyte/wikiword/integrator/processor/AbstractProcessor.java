package de.brightbyte.wikiword.integrator.processor;

import java.util.Date;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.io.LogOutput;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.processor.ImportProgressTracker;

public abstract class AbstractProcessor<E> {
	private ImportProgressTracker itemTracker;
	private int progressTicks = 0;
	private int progressInterval = 1000;
	protected LeveledOutput out;
	
	public AbstractProcessor() {
		out = new LogOutput();
	}
	
	public void setLogLevel(int level) {
		if (out == null) return;
		if (!(out instanceof LogOutput)) return;
		
		((LogOutput)out).setLogLevel(level);
	}

	public void setLogOutput(Output out) {
		if (!(out instanceof LeveledOutput)) out = new LogOutput(out);
		this.out = (LeveledOutput)out;
	}
	
	public int getProgressInterval() {
		return progressInterval;
	}

	public void setProgressInterval(int progressInterval) {
		this.progressInterval = progressInterval;
	}
	
	public void reset() {
		itemTracker = new ImportProgressTracker("items");
		progressTicks = 0;
	}
	
	public void finish() {
		trackerChunk();
		reset();
	}
	
	public void trackerChunk() {
		itemTracker.chunk();
		progressTicks = 0;
		
		out.info("--- "+new Date()+" ---");
		out.info("- "+itemTracker);
	}
	
	public void tracerStep() {
		itemTracker.step();
		progressTicks++;
		if (progressTicks>progressInterval) {
			trackerChunk();
		}
	}
	
	public void process(DataCursor<E> cursor) throws PersistenceException {
		reset();
		
		E m;
		while ((m = cursor.next()) != null ) {
			processEntry(m);
			tracerStep();
		}
		
		finish();
	}

	protected abstract  void processEntry(E m) throws PersistenceException;
	
}
