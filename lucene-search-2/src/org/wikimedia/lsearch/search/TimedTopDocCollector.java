package org.wikimedia.lsearch.search;

import java.io.Serializable;

import org.apache.lucene.search.TopDocCollector;

/**
 * Simple timed implementation, uses heuristics to 
 * estimate how long will the search last for, and if
 * it's more than predefined limit, throws an exception  
 * 
 * @author rainman
 *
 */
public class TimedTopDocCollector extends TopDocCollector {
	protected int maxDoc;
	protected long limit;
	/** 
	 * heuristic stuff:
	 * - assume hits have uniform distribution over documents
	 */
	protected long start = 0;
	protected int count = 0;
	protected int nextCheck = 10; // first collect 10 hits to get a descent estimate
	protected long timeStep = 0;
		
	/**
	 * 
	 * @param numHits 
	 * @param maxDoc - maximal number of documents
	 * @param limit - time limit in milliseconds
	 */
	public TimedTopDocCollector(int numHits, long limit) {
		super(numHits);
		this.limit = limit;
		this.timeStep = limit / 5; // attempt to check every fifth of the total time interval
	}

	@Override
	public void collect(int doc, float score) {
		super.collect(doc, score);
		count++;		
		if(start == 0){
			start = System.currentTimeMillis();
			return;
		}
		if(nextCheck <= count){
			long delta = System.currentTimeMillis() - start;
			if(delta == 0){
				nextCheck *= 10;
				return;
			}
			if(delta > limit)
				throw new RuntimeException("time limit");
			
			// estimate how many documents will have to be collected to 
			// approximately match the next timestep
			nextCheck = count + (int)( (double)timeStep / ((double)delta/(double)count));
			//System.out.println("["+delta+"ms : "+count+"] Next check at "+nextCheck);
		}		
	}
}
