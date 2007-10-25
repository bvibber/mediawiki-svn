package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.TermPositions;
import org.apache.lucene.search.CombinedPhraseQuery.CombinedPhraseWeight;

/**
 * Combine two phrase scorers, one with exact match 
 * (with all the stop words), and other with a sloppy
 * matcher (without stop words)
 * 
 * @author rainman
 *
 */
public class CombinedPhraseScorer extends Scorer {
	protected CustomPhraseScorer exact;
	protected CustomPhraseScorer sloppy;
	protected float boost; 
	protected boolean firstTime = true;
	
   public CombinedPhraseScorer(CombinedPhraseWeight weight, Similarity similarity, Scorer sloppy, Scorer exact) {
		super(similarity);
		this.boost = weight.getValue();
		this.sloppy = (CustomPhraseScorer) sloppy;
		this.exact = (CustomPhraseScorer) exact;
	}

	@Override
	public int doc() {
		return sloppy.doc();
	}

	@Override
	public Explanation explain(int doc) throws IOException {
		while (next() && doc() < doc) {}
		
		float sloppyScore = sloppy.score();
		float exactScore = sloppy.doc() == exact.doc()? exact.score() : 0;
		
		Explanation e = new Explanation(0.2f*sloppyScore+0.3f*exactScore,"combined score,weighted (0.2,0.3) sum of:");
		
		return e;
	}

	@Override
	public boolean next() throws IOException {
		// documents that match exact phase are subset of those matching sloppy!
		if(firstTime){
			firstTime = false;
			boolean hasNext = sloppy.next();
			exact.next();			
			return hasNext;
		} else{
			boolean hasNext = sloppy.next();
			if(hasNext){
				if(exact.doc() < sloppy.doc())
					exact.skipTo(sloppy.doc());
			}
			return hasNext;
		}
	}

	@Override
	public float score() throws IOException {
		float score = 0.2f*sloppy.score();
		if(exact.doc() == sloppy.doc())
			score += 0.3f*exact.score();
		return score * boost;
	}

	@Override
	public boolean skipTo(int target) throws IOException {
		if(firstTime){
			firstTime = false;
			boolean succ = sloppy.skipTo(target);
			exact.skipTo(target);
			return succ;
		} else{
			boolean succ =	sloppy.skipTo(target);
			if(exact.doc() < target)				
				exact.skipTo(target);
			return succ;
		}
	}

}
