package org.apache.lucene.search;
import java.io.IOException;

import org.apache.lucene.index.TermPositions;
import org.apache.lucene.search.*;

abstract public class CustomPhraseScorer extends PhraseScorer {
	ScoreValue val = null;
	
	CustomPhraseScorer(Weight weight, TermPositions[] tps, int[] offsets, Similarity similarity, byte[] norms, ScoreValue val) {
		super(weight, tps, offsets, similarity, norms);
		this.val = val;
	}
	
	public float score() throws IOException {		
		float raw = transformFreq(freq) * value; // raw score
		float sc = raw * Similarity.decodeNorm(norms[first.doc]); // normalize
		if(val != null)
			sc *= val.score(first.doc);
		System.out.println("scoring " + first.doc+" with "+sc);
		return sc; 
	}
	
	public Explanation explain(final int doc) throws IOException {
		Explanation tfExplanation = new Explanation();

		while (next() && doc() < doc) {}

		float transformed = transformFreq(freq);
		float phraseFreq = (doc() == doc) ? transformed : 0.0f;
		float rank = (val!=null) ? val.score(doc) : 1; 
		phraseFreq *= rank;
		tfExplanation.setValue(phraseFreq);
		tfExplanation.setDescription("phraseFreq product of:");
		
		Explanation texp = new Explanation();
		texp.setValue(transformed);
		texp.setDescription("transfored(freq=" + freq + ")");
		
		Explanation rankexp = new Explanation();
		rankexp.setValue(rank);
		rankexp.setDescription("rank(rank=" + rank + ")");
		
		tfExplanation.addDetail(texp);
		tfExplanation.addDetail(rankexp);

		return tfExplanation;
	}
	
	protected float transformFreq(float freq){
		return (float) (Math.sqrt(1+freq)-1);
	}
	
	/** aggregate freq scores for phrases */
	protected float addToFreq(float freq, float add){
		// maybe repeate phrases should get less score?
		return freq + add;
	}
	
	/** 
	 * Calculate phrase score for certain phrase. 
	 * Favour exact matches, and phrases near beginning.
	 */ 
	public float freqScore(int start, int distance){
		System.out.println("freqScore at start="+start+", dist="+distance);
		int offset = start + distance;
		float begin = 1;
		if(offset < 25)
			begin = 16;
		else if(offset < 100)
			begin = 4;
		else if(offset < 250)
			begin = 2;
		
		float exact = 1;
		if(distance == 0)
			exact = 32;
		
		return begin * exact * 1.0f / (distance + 1); 		
	}

}
