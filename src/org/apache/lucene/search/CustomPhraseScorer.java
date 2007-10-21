package org.apache.lucene.search;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.TermPositions;
import org.apache.lucene.search.*;

abstract public class CustomPhraseScorer extends PhraseScorer {
	protected ScoreValue val = null;
	protected PhraseInfo phInfo = null;
	protected boolean beginBoost;
	protected int phraseLen;
	// following are for debug:
	protected Scorer stemtitleScorer, relatedScorer;
	protected Weight stemtitleWeight, relatedWeight;
	protected boolean max; // if true, won't sum up, but take max individual score
	
	public final static boolean DEBUG = false; // TODO: set to false in release
	public HashMap<Integer,ArrayList<Explanation>> explanations = null;
	
	CustomPhraseScorer(Weight weight, TermPositions[] tps, int[] offsets, Similarity similarity, byte[] norms, ScoreValue val, 
			boolean beginBoost, PhraseInfo phInfo, Scorer stemtitleScorer, Scorer relatedScorer, Weight stemtitleWeight, Weight relatedWeight, 
			boolean max) {
		super(weight, tps, offsets, similarity, norms);
		this.val = val;
		this.beginBoost = beginBoost;
		this.phInfo = phInfo;
		this.phraseLen = tps.length;
		this.stemtitleScorer = stemtitleScorer;
		this.relatedScorer = relatedScorer;
		this.relatedWeight = relatedWeight;
		this.stemtitleWeight = stemtitleWeight;
		this.max = max;
	}
	
	public boolean next() throws IOException {
		if (firstTime) {
			init();
			firstTime = false;
		} else if (more) {
			more = last.next();                         // trigger further scanning
		}
		return doNext();
	}

	// next without initial increment
	private boolean doNext() throws IOException {
		while (more) {
			while (more && first.doc < last.doc) {      // find doc w/ all the terms
				more = first.skipTo(last.doc);            // skip first upto last
				firstToLast();                            // and move it to the end
			}
			if (more) {
				// skip additional scores to matched document (as if phrase is a MUST boolean clause, and these SHOULD)
				if(stemtitleScorer != null && stemtitleScorer.doc() < first.doc)
					stemtitleScorer.skipTo(first.doc);
				if(relatedScorer != null && relatedScorer.doc() < first.doc)
					relatedScorer.skipTo(first.doc);
				// found a doc with all of the terms
				freq = phraseFreq();                      // check for phrase
				if (freq == 0.0f)                         // no match
					more = last.next();                     // trigger further scanning
				else
					return true;                            // found a match
			}
		}
		return false;                                 // no more matches
	}

	public boolean skipTo(int target) throws IOException {
		if(stemtitleScorer != null){
			if(firstTime || stemtitleScorer.doc() < target)
				stemtitleScorer.skipTo(target);
		}
		if(relatedScorer != null){
			if(firstTime || relatedScorer.doc() < target)
				relatedScorer.skipTo(target);
		}
		firstTime = false;
		for (PhrasePositions pp = first; more && pp != null; pp = pp.next) {
			more = pp.skipTo(target);
		}
		if (more)
			sort();                                     // re-sort
		return doNext();
	}

	/**
	 * For a document containing all the phrase query terms, compute the
	 * frequency of the phrase in that document. 
	 * A non zero frequency means a match.
	 * <br>Note, that containing all phrase terms does not guarantee a match - they have to be found in matching locations.  
	 * @return frequency of the phrase in current doc, 0 if not found. 
	 */
	private void init() throws IOException {
		if(stemtitleScorer != null)
			stemtitleScorer.next();
		if(relatedScorer != null)
			relatedScorer.next();
		for (PhrasePositions pp = first; more && pp != null; pp = pp.next) 
			more = pp.next();
		if(more)
			sort();
	}

	private void sort() {
		pq.clear();
		for (PhrasePositions pp = first; pp != null; pp = pp.next)
			pq.put(pp);
		pqToList();
	}

	public float score() throws IOException {		
		float raw = transformFreq(freq) * value; // raw score
		//float sc = raw * Similarity.decodeNorm(norms[first.doc]); // normalize
		float sc = raw ; // no norms
		if(stemtitleScorer != null && stemtitleScorer.doc() == first.doc){
			sc *= 1 + stemtitleScorer.score();
		}
		if(relatedScorer != null && relatedScorer.doc() == first.doc){			
			sc *= 1 + relatedScorer.score();
		}

		return sc; 
	}
	
	public Explanation explain(final int doc) throws IOException {
		Explanation tfExplanation = new Explanation();

		while (next() && doc() < doc) {}

		float transformed = transformFreq(freq);
		float phraseFreq = (doc() == doc) ? transformed : 0.0f;
		float rank = 1; 
		
		tfExplanation.setValue(phraseFreq);
		tfExplanation.setDescription("phraseFreq:");
		
		Explanation texp = new Explanation();
		texp.setValue(transformed);
		texp.setDescription("summed freq(freq=" + freq + ")");
		if(DEBUG && explanations != null && explanations.get(doc) != null){
			texp.setDescription(texp.getDescription()+", sum of:");
			for(Explanation e : explanations.get(doc))
				texp.addDetail(e);
		}
		tfExplanation.addDetail(texp);
		
		if(stemtitleScorer != null && stemtitleScorer.doc() == first.doc){
			Explanation eTitle = new Explanation();
			eTitle.setValue(1+stemtitleScorer.score());
			eTitle.setDescription("Stemtitle (raw score="+stemtitleScorer.score()+")");
			//eTitle.addDetail(stemtitleWeight.explain(reader,first.doc));
			tfExplanation.addDetail(eTitle);
			tfExplanation.setValue(tfExplanation.getValue()*(1+stemtitleScorer.score()));
		}
		if(relatedScorer != null && relatedScorer.doc() == first.doc){
			Explanation eRelated = new Explanation();			
			eRelated.setValue(1+relatedScorer.score());
			eRelated.setDescription("Related (raw score="+relatedScorer.score()+")");
			//eRelated.addDetail(relatedWeight.explain(reader,first.doc));
			tfExplanation.addDetail(eRelated);
			tfExplanation.setValue(tfExplanation.getValue()*(1+relatedScorer.score()));
		}

		return tfExplanation;
	}
	
	protected float transformFreq(float freq){
		return (float) (Math.sqrt(1+freq)-1);
	}
	
	/** aggregate freq scores for phrases */
	protected float addToFreq(float freq, float add){
		if(max){
			// return max of (freq,add)
			if(add > freq)
				return add;
			else
				return freq;
		} else
			return freq + add; 		// maybe repeate phrases should get less score?
	}
	
	/** 
	 * Calculate phrase score for certain phrase. 
	 * Favour exact matches, and phrases near beginning.
	 * @throws IOException 
	 */ 
	public float freqScore(int start, int distance) throws IOException{
		//System.out.println("freqScore at start="+start+", dist="+distance);
		int offset = start + distance;
		float begin = 1;
		if(beginBoost){
			if(offset < 25)
				begin = 16;
			else if(offset < 200)
				begin = 4;
			else if(offset < 500)
				begin = 2;
		}
		float title = 1;
		float related = 1;
		float rank = 1;
		if(val != null){
			if(begin != 1)
				rank = val.score(first.doc,2);
			else
				rank = val.score(first.doc);
				
		}
		
		float exact = 1;
		if(distance == 0)
			exact = 8;
		
		float baseScore = title * related * rank * begin * exact * 1.0f / (distance + 1);
		if(DEBUG){
			// provide very extensive explanations
			if(explanations == null)
				explanations = new HashMap<Integer,ArrayList<Explanation>>();
			ArrayList<Explanation> expl = explanations.get(first.doc);
			if(expl == null){
				expl = new ArrayList<Explanation>();
				explanations.put(first.doc,expl);
			}
			Explanation e = new Explanation();
			e.setValue(baseScore);
			e.setDescription("Frequency, product of:");
			expl.add(e);
			Explanation eBegin = new Explanation();
			eBegin.setValue(begin);
			eBegin.setDescription("Begin (offset="+offset+")");
			e.addDetail(eBegin);
			Explanation eExact = new Explanation();
			eExact.setValue(exact);
			eExact.setDescription("Exact (distance="+distance+")");
			e.addDetail(eExact);
			Explanation eDist = new Explanation();
			eDist.setValue(1.0f / (distance+1));
			eDist.setDescription("Distance (distance="+distance+")");
			e.addDetail(eDist);
			if(val != null){
				Explanation eRank = new Explanation();			
				eRank.setValue(rank);
				eRank.setDescription("Rank (raw rank="+val.value(first.doc)+")");
				e.addDetail(eRank);
			}
		}
		if(phInfo != null){
			// aggregate field !
			int len = phInfo.length(first.doc,start);
			float boost = phInfo.boost(first.doc,start);
			float score = boost * baseScore * (phraseLen / (float)len); 
			if(DEBUG){
				Explanation e = explanations.get(first.doc).get(explanations.get(first.doc).size()-1);
				e.setValue(score);
				Explanation eInfo = new Explanation();
				eInfo.setValue(boost);
				eInfo.setDescription("Phrase boost");
				e.addDetail(eInfo);
				Explanation eLen = new Explanation();
				eLen.setValue(phraseLen / (float)len);
				eLen.setDescription("Phrase length proportion (matched="+phraseLen+", total="+len+")");
				e.addDetail(eLen);
			}
			return score;
		} else
			return baseScore;
	}

}
