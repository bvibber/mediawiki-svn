package org.apache.lucene.search;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.TermPositions;
import org.apache.lucene.search.*;

abstract public class CustomPhraseScorer extends PhraseScorer {
	protected int phraseLen;
	protected int phraseLenNoStopWords;
	protected QueryOptions options;
	// following are for debug:
	protected Scorer stemtitleScorer, relatedScorer;
	
	
	public final static boolean DEBUG = false; // TODO: set to false in release
	public HashMap<Integer,ArrayList<Explanation>> explanations = null;
	
	CustomPhraseScorer(Weight weight, TermPositions[] tps, int[] offsets, int stopWordCount, Similarity similarity, 
			byte[] norms, QueryOptions options, Scorer stemtitleScorer, Scorer relatedScorer) {
		super(weight, tps, offsets, similarity, norms);		
		this.options = options;
		this.phraseLen = tps.length;
		this.phraseLenNoStopWords = phraseLen - stopWordCount;
		this.stemtitleScorer = stemtitleScorer;
		this.relatedScorer = relatedScorer;
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

		float rank = options.rankMeta != null ? options.rankMeta.score(doc) : 1;
		float totalFreq = freq * rank;
		float transformed = transformFreq(totalFreq);
		float phraseFreq = (doc() == doc) ? transformed : 0.0f;		
		
		tfExplanation.setValue(phraseFreq);
		tfExplanation.setDescription("phraseFreq:");
		
		Explanation texp = new Explanation();
		texp.setValue(transformed);
		texp.setDescription("total freq(base freq=" + freq + "), product of");
		Explanation eRank = new Explanation();
		eRank.setValue(rank);
		if(options.rankMeta != null)
			eRank.setDescription("Rank (raw rank="+options.rankMeta.score(doc)+")");
		else
			eRank.setDescription("Rank (no ranking data)");
		texp.addDetail(eRank);
		Explanation eFreq = new Explanation();
		eFreq.setValue(freq);
		eFreq.setDescription("summed freq");
		texp.addDetail(eFreq);
		
		if(DEBUG && explanations != null && explanations.get(doc) != null){
			eFreq.setDescription(texp.getDescription()+", sum of:");
			for(Explanation e : explanations.get(doc))
				eFreq.addDetail(e);
		}
		tfExplanation.addDetail(texp);
		
		if(stemtitleScorer != null && stemtitleScorer.doc() == doc){
			Explanation eTitle = new Explanation();
			eTitle.setValue(1+stemtitleScorer.score());
			eTitle.setDescription("Stemtitle (raw score="+stemtitleScorer.score()+")");
			tfExplanation.addDetail(eTitle);
			tfExplanation.setValue(tfExplanation.getValue()*(1+stemtitleScorer.score()));
		}
		if(relatedScorer != null && relatedScorer.doc() == doc){
			Explanation eRelated = new Explanation();			
			eRelated.setValue(1+relatedScorer.score());
			eRelated.setDescription("Related (raw score="+relatedScorer.score()+")");
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
		if(options.takeMaxScore){
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
		if(options.useBeginBoost){
			if(offset < 25)
				begin = 16;
			else if(offset < 200)
				begin = 4;
			else if(offset < 500)
				begin = 2;
		}
		float title = 1;
		float related = 1;
		
		float exact = 1;
		if(distance == 0)
			exact = options.exactBoost;
		
		float baseScore = title * related * begin * exact * 1.0f / (distance + 1);
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
		}
		if(options.aggregateMeta != null){
			// aggregate field !
			int len = options.aggregateMeta.length(first.doc,start);
			int lenNoStopWords = options.aggregateMeta.lengthNoStopWords(first.doc,start);
			float wholeBoost = (phraseLen == len)? options.wholeBoost : 1;
			float wholeBoostNoStopWords = (phraseLenNoStopWords == lenNoStopWords)? options.wholeNoStopWordsBoost : 1;
			float boost = options.aggregateMeta.boost(first.doc,start);
			int propPhrase, propTotal;
			if(options.useStopWordLen){
				propPhrase = phraseLenNoStopWords;
				propTotal = lenNoStopWords;
			} else{
				propPhrase = phraseLen;
				propTotal = len;
			}
			float score = boost * baseScore * (propPhrase / (float)propTotal) * wholeBoost * wholeBoostNoStopWords; 
			if(DEBUG){
				Explanation e = explanations.get(first.doc).get(explanations.get(first.doc).size()-1);
				e.setValue(score);
				Explanation eInfo = new Explanation();
				eInfo.setValue(boost);
				eInfo.setDescription("Phrase boost");
				e.addDetail(eInfo);
				Explanation eLen = new Explanation();
				eLen.setValue(phraseLen / (float)len);
				eLen.setDescription("Phrase length proportion (matched="+propPhrase+", total="+propTotal+")");
				e.addDetail(eLen);
				Explanation eW = new Explanation();
				eW.setValue(wholeBoost);
				eW.setDescription("Whole phrase match boost");
				e.addDetail(eW);
				Explanation eNS = new Explanation();
				eNS.setValue(wholeBoostNoStopWords);
				eNS.setDescription("Whole phrase match boost, no stop words");
				e.addDetail(eNS);
			}
			return score;
		} else
			return baseScore;
	}

}
