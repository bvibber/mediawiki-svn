package org.apache.lucene.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Comparator;
import java.util.HashMap;

import org.apache.lucene.index.TermPositions;
import org.wikimedia.lsearch.analyzers.Aggregate.Flags;

/**
 * Scorer for positional queries
 * 
 * @author rainman
 *
 */
abstract public class PositionalScorer extends Scorer {
	protected int phraseLen;
	protected int phraseLenNoStopWords;
	protected PositionalOptions options;
	protected boolean useExactBoost = true;

	protected Weight weight;
	protected byte[] norms;
	protected float value;

	protected boolean firstTime = true;
	protected boolean more = true;
	protected PhraseQueueBoost pq;
	protected PhrasePositionsBoost first, last;

	protected float freq; //phrase frequency in current doc as computed by phraseFreq().

	/** super-detailed scoring info (should be off in release) */
	public final static boolean DEBUG = false; 
	public HashMap<Integer,ArrayList<Explanation>> explanations = null;

	PositionalScorer(Weight weight, TermPositions[] tps, int[] offsets, int stopWordCount, Similarity similarity, 
			byte[] norms, PositionalOptions options) {
		super(similarity);
		this.norms = norms;
		this.weight = weight;
		this.value = weight.getValue();

		// convert tps to a list of phrase positions.
		// note: phrase-position differs from term-position in that its position
		// reflects the phrase offset: pp.pos = tp.pos - offset.
		// this allows to easily identify a matching (exact) phrase 
		// when all PhrasePositionsBoost have exactly the same position.
		for (int i = 0; i < tps.length; i++) {
			PhrasePositionsBoost pp = new PhrasePositionsBoost(tps[i], offsets[i]);
			if (last != null) {			  // add next to end of list
				last.next = pp;
			} else
				first = pp;
			last = pp;
		}

		pq = new PhraseQueueBoost(tps.length);             // construct empty pq

		this.options = options;
		this.phraseLen = tps.length;
		this.phraseLenNoStopWords = phraseLen - stopWordCount;
	}

	public int doc() { return first.doc; }

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

	public float score() throws IOException {		
		float raw = transformFreq(freq) * value; // raw score
		//float sc = raw * Similarity.decodeNorm(norms[first.doc]); // normalize
		float sc = raw ; // no norms

		return sc; 
	}

	public boolean skipTo(int target) throws IOException {
		if(!firstTime && doc() >= target){
			return more; // we are at the target or passed it
		}
		firstTime = false;
		for (PhrasePositionsBoost pp = first; more && pp != null; pp = pp.next) {
			more = pp.skipTo(target);
		}
		if (more)
			sort();                                     // re-sort
		return doNext();
	}
	protected abstract float phraseFreq() throws IOException;

	private void init() throws IOException {
		for (PhrasePositionsBoost pp = first; more && pp != null; pp = pp.next) 
			more = pp.next();
		if(more)
			sort();
	}

	private void sort() {
		pq.clear();
		for (PhrasePositionsBoost pp = first; pp != null; pp = pp.next)
			pq.put(pp);
		pqToList();
	}

	protected final void pqToList() {
		last = first = null;
		while (pq.top() != null) {
			PhrasePositionsBoost pp = (PhrasePositionsBoost) pq.pop();
			if (last != null) {			  // add next to end of list
				last.next = pp;
			} else
				first = pp;
			last = pp;
			pp.next = null;
		}
	}

	protected final void firstToLast() {
		last.next = first;			  // move first to end of list
		last = first;
		first = first.next;
		last.next = null;
	}

	public Explanation explain(final int doc) throws IOException {
		Explanation tfExplanation = new Explanation();

		while (next() && doc() < doc) {}

		float phraseFreq = (doc() == doc) ? transformFreq(freq) : 0.0f;
		tfExplanation.setValue(phraseFreq);
		tfExplanation.setDescription("tf(freq=" + freq + ")");

		// add extra info about every term in the sum
		if(DEBUG && explanations != null && explanations.get(doc) != null){
			Explanation eFreq = new Explanation();
			eFreq.setValue(freq);
			eFreq.setDescription("sum of:");				
			for(Explanation e : explanations.get(doc))
				eFreq.addDetail(e);
			tfExplanation.addDetail(eFreq);
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
			return freq + add; 		// maybe repeated phrases should get less score?
	}

	/** 
	 * Calculate phrase score for certain phrase. 
	 * Favour exact matches, and phrases near beginning.
	 * @throws IOException 
	 */ 
	public float freqScore(int start, int distance) throws IOException{
		if(options.phraseQueryFallback)
			return getSimilarity().sloppyFreq(distance);
		//System.out.println("freqScore at start="+start+", dist="+distance);
		int offset = start + distance;
		float begin = 1;
		float rank = 1;
		float distanceProp = (float)Math.sqrt(1.0f / (distance + 1));

		float exact = 1;
		if(distance == 0 && useExactBoost)
			exact = options.exactBoost;

		float beginExact = 1;		

		if(options.useBeginBoost){
			if(offset < 25)
				begin = options.beginTable[0];
			else if(offset < 50)
				begin = options.beginTable[1];
			else if(offset < 200)
				begin = options.beginTable[2];
			else if(offset < 500)
				begin = options.beginTable[3];
			// only fetch ranking data on begin-of-article match
			if(begin>1 && options.rankMeta != null)
				rank = 1 + options.rankMeta.rank(doc()) * (begin/options.beginTable[0]);

			if(start <= 6 && distance>0 && distance <= 10 ){ // as good as exact match
				distanceProp = 1;
				beginExact = options.beginExactBoost;				
			}
		}
		
		float termsBoost = 1;
		for(PhrasePositionsBoost pp = first; pp != null; pp = pp.next)
			termsBoost *= pp.boost();

		float baseScore = rank * begin * beginExact * exact * distanceProp * termsBoost;
		if(DEBUG){
			// provide very extensive explanations
			if(explanations == null)
				explanations = new HashMap<Integer,ArrayList<Explanation>>();
			ArrayList<Explanation> expl = explanations.get(doc());
			if(expl == null){
				expl = new ArrayList<Explanation>();
				explanations.put(doc(),expl);
			}			
			Explanation e = new Explanation();
			e.setValue(baseScore);
			e.setDescription("Frequency, product of:");
			expl.add(e);
			Explanation eBegin = new Explanation();
			eBegin.setValue(begin);
			eBegin.setDescription("Begin (offset="+offset+", start="+start+")");
			e.addDetail(eBegin);
			Explanation eRank = new Explanation();
			eRank.setValue(rank);
			eRank.setDescription("Begin rank");
			e.addDetail(eRank);
			Explanation eBeginExact = new Explanation();
			eBeginExact.setValue(beginExact);
			eBeginExact.setDescription("Begin exact");
			e.addDetail(eBeginExact);
			Explanation eExact = new Explanation();
			eExact.setValue(exact);
			eExact.setDescription("Exact (distance="+distance+")");
			e.addDetail(eExact);
			Explanation eDist = new Explanation();
			eDist.setValue(distanceProp);
			eDist.setDescription("Distance (distance="+distance+")");
			e.addDetail(eDist);
			e.addDetail(new Explanation(termsBoost,"Term boost"));
		}
		if(options.aggregateMeta != null){
			// aggregate field !
			int pos = (start+offset)/2+1; // FIXME: in some cases start can be off by 1 in sloppy queries, probably lucene issue
			int len = options.aggregateMeta.length(doc(),pos);
			int lenNoStopWords = options.aggregateMeta.lengthNoStopWords(doc(),pos);
			int lenComplete = options.aggregateMeta.lengthComplete(doc(),pos);
			float wholeBoost = 1;
			float wholeBoostNoStopWords = 1;
			float completeBoost = 1;
			float wholeOnly = 1;
			float alttitleWhole = 1;
			boolean matchedWholeOrComplete = false;
			// use only the complete boost, not whole/wholeNoStopWords
			if(options.useCompleteOnly){
				if(phraseLen == lenComplete){
					completeBoost = options.completeBoost;
					matchedWholeOrComplete = true;
				}
			} else{
				// use whole/wholeNoStopWords/wholeMatch
				boolean matchedWhole = false;
				if(phraseLen == len && (options.allowSingleWordWholeMatch || len > 1)){ 
					// whole boost only makes sense when we have more than 1 word
					wholeBoost = options.wholeBoost;
					matchedWhole = true;				  
				} else if(phraseLenNoStopWords == lenNoStopWords && lenNoStopWords>=(len-lenNoStopWords) && lenNoStopWords>1){
					// no stop boost - only if matched words >= stop words
					wholeBoostNoStopWords = options.wholeNoStopWordsBoost;
					matchedWhole = true;
				}
				if(options.onlyWholeMatch && !matchedWhole)
					wholeOnly = 0; // zero out the score since we require whole match only
				matchedWholeOrComplete = matchedWhole;
				if( options.aggregateMeta.flags(doc(),pos) == Flags.ALTTITLE )
					alttitleWhole = options.alttileWholeExtraBoost;
			}

			float boost = 1;
			if(options.useRankForWholeMatch && matchedWholeOrComplete)
				boost = options.aggregateMeta.rank(doc());
			else
				boost = options.aggregateMeta.boost(doc(),pos);

			float namespaceBoost = 1;
			if(options.nsWholeBoost!=null && matchedWholeOrComplete)
				namespaceBoost = options.nsWholeBoost.getBoost(options.aggregateMeta.namespace(doc()));

			int propPhrase, propTotal;
			if(options.useNoStopWordLen){
				propPhrase = phraseLenNoStopWords;
				propTotal = lenNoStopWords;
			} else{
				propPhrase = phraseLen;
				propTotal = len;
			}
			float score = namespaceBoost * boost * baseScore * (propPhrase / (float)propTotal) * completeBoost * wholeBoost * wholeBoostNoStopWords * wholeOnly * alttitleWhole; 
			if(DEBUG){
				Explanation e = explanations.get(doc()).get(explanations.get(doc()).size()-1);
				e.setDescription(e.getDescription()+" (pos="+pos+")");
				e.setValue(score);
				e.addDetail(new Explanation(namespaceBoost,"Namespace boost (ns="+options.aggregateMeta.namespace(doc())+")"));
				Explanation eInfo = new Explanation();
				eInfo.setValue(boost);
				eInfo.setDescription("Phrase boost");
				e.addDetail(eInfo);
				Explanation eLen = new Explanation();
				eLen.setValue(propPhrase / (float)propTotal);
				eLen.setDescription("Phrase length proportion (matched="+propPhrase+", total="+propTotal+")");
				e.addDetail(eLen);
				Explanation eW = new Explanation();
				eW.setValue(wholeBoost);
				eW.setDescription("Whole phrase match boost (matched="+phraseLen+", total="+len+")");
				e.addDetail(eW);
				Explanation eNS = new Explanation();
				eNS.setValue(wholeBoostNoStopWords);
				eNS.setDescription("Whole phrase match boost, no stop words (matched="+phraseLenNoStopWords+", total="+lenNoStopWords+")");
				e.addDetail(eNS);
				Explanation eWO = new Explanation();
				eWO.setValue(wholeOnly);
				eWO.setDescription("Whole only (enabled="+options.onlyWholeMatch+")");
				e.addDetail(eWO);
				Explanation eC = new Explanation();
				eC.setValue(completeBoost);
				eC.setDescription("Complete boost (matched="+phraseLen+", total="+lenComplete+")");
				e.addDetail(eC);
				Explanation eAW = new Explanation();
				eAW.setValue(alttitleWhole);
				eAW.setDescription("Alttitle whole extra");
				e.addDetail(eAW);
			}
			return score;
		} else
			return baseScore;
	}

	/**
	 * Scorer when there is only one term in phrase
	 * 
	 * @author rainman
	 *
	 */
	final public static class TermScorer extends PositionalScorer {		
		TermScorer(Weight weight, TermPositions[] tps, int[] offsets, int stopWordCount, Similarity similarity, 
				byte[] norms, PositionalOptions options) {
			super(weight, tps, offsets, stopWordCount, similarity, norms, options);
			useExactBoost = false; // we won't use exact boost since all hits would be exact 
		}

		protected final float phraseFreq() throws IOException {
			float freq = 0;
			first.firstPosition();
			do{
				freq = addToFreq(freq,freqScore(first.position,0)); // match
			} while(first.nextPosition());
			return freq;
		}
	}

	/**
	 * Scorer for phrases with no slop (slop factor=0)
	 * 
	 *
	 */
	final public static class ExactScorer extends PositionalScorer {
		ExactScorer(Weight weight, TermPositions[] tps, int[] offsets, int stopWordCount, Similarity similarity, 
				byte[] norms, PositionalOptions options) {
			super(weight, tps, offsets, stopWordCount, similarity, norms, options);
		}

		protected final float phraseFreq() throws IOException {
			// sort list with pq
			pq.clear();
			for (PhrasePositionsBoost pp = first; pp != null; pp = pp.next) {
				pp.firstPosition();
				pq.put(pp);				  // build pq from list
			}
			pqToList();					  // rebuild list from pq

			// sum of scores for each phrase
			float freq = 0;
			do {					  // find position w/ all terms
				while (first.position < last.position) {	  // scan forward in first
					do {
						if (!first.nextPosition())
							return (float)freq;
					} while (first.position < last.position);
					firstToLast();
				}
				freq = addToFreq(freq,freqScore(first.position,0)); // match
			} while (last.nextPosition());

			return freq;
		}

	}

	/**
	 * Scorer for sloppy queries
	 *
	 * 
	 */
	final static public class SloppyScorer extends PositionalScorer {
		private int slop;
		private PhrasePositionsBoost repeats[];
		private boolean checkedRepeats;

		SloppyScorer(Weight weight, TermPositions[] tps, int[] offsets, int stopWordCount, Similarity similarity, int slop, 
				byte[] norms, PositionalOptions options) {
			super(weight, tps, offsets, stopWordCount, similarity, norms, options);
			this.slop = slop;
		}

		/**
		 * Score a candidate doc for all slop-valid position-combinations (matches) 
		 * encountered while traversing/hopping the PhrasePositionsBoost.
		 * <br> The score contribution of a match depends on the distance: 
		 * <br> - highest score for distance=0 (exact match).
		 * <br> - score gets lower as distance gets higher.
		 * <br>Example: for query "a b"~2, a document "x a b a y" can be scored twice: 
		 * once for "a b" (distance=0), and once for "b a" (distance=2).
		 * <br>Pssibly not all valid combinations are encountered, because for efficiency  
		 * we always propagate the least PhrasePosition. This allows to base on 
		 * PriorityQueue and move forward faster. 
		 * As result, for example, document "a b c b a"
		 * would score differently for queries "a b c"~4 and "c b a"~4, although 
		 * they really are equivalent. 
		 * Similarly, for doc "a b c b a f g", query "c b"~2 
		 * would get same score as "g f"~2, although "c b"~2 could be matched twice.
		 * We may want to fix this in the future (currently not, for performance reasons).
		 */
		protected final float phraseFreq() throws IOException {
			int end = initPhrasePositionsBoost();
			float freq = 0.0f;
			boolean done = (end<0);
			while (!done) {
				PhrasePositionsBoost pp = (PhrasePositionsBoost) pq.pop();
				int start = pp.position;
				int next = ((PhrasePositionsBoost) pq.top()).position;

				boolean tpsDiffer = true;
				for (int pos = start; pos <= next || !tpsDiffer; pos = pp.position) {
					if (pos<=next && tpsDiffer)
						start = pos;				  // advance pp to min window
						if (!pp.nextPosition()) {
							done = true;          // ran out of a term -- done
							break;
						}
						tpsDiffer = !pp.repeats || termPositionsDiffer(pp);
				}

				int matchLength = end - start;
				if (matchLength <= slop)
					freq = addToFreq(freq,freqScore(start,matchLength)); // score match

				if (pp.position > end)
					end = pp.position;
				pq.put(pp);				  // restore pq
			}

			return freq;
		}


		/**
		 * Init PhrasePositionsBoost in place.
		 * There is a one time initializatin for this scorer:
		 * <br>- Put in repeats[] each pp that has another pp with same position in the doc.
		 * <br>- Also mark each such pp by pp.repeats = true.
		 * <br>Later can consult with repeats[] in termPositionsDiffer(pp), making that check efficient.
		 * In particular, this allows to score queries with no repetiotions with no overhead due to this computation.
		 * <br>- Example 1 - query with no repetitions: "ho my"~2
		 * <br>- Example 2 - query with repetitions: "ho my my"~2
		 * <br>- Example 3 - query with repetitions: "my ho my"~2
		 * <br>Init per doc w/repeats in query, includes propagating some repeating pp's to avoid false phrase detection.  
		 * @return end (max position), or -1 if any term ran out (i.e. done) 
		 * @throws IOException 
		 */
		private int initPhrasePositionsBoost() throws IOException {
			int end = 0;

			// no repeats at all (most common case is also the simplest one)
			if (checkedRepeats && repeats==null) {
				// build queue from list
				pq.clear();
				for (PhrasePositionsBoost pp = first; pp != null; pp = pp.next) {
					pp.firstPosition();
					if (pp.position > end)
						end = pp.position;
					pq.put(pp);         // build pq from list
				}
				return end;
			}

			// position the pp's
			for (PhrasePositionsBoost pp = first; pp != null; pp = pp.next)
				pp.firstPosition();

			// one time initializatin for this scorer
			if (!checkedRepeats) {
				checkedRepeats = true;
				// check for repeats
				HashMap m = null;
				for (PhrasePositionsBoost pp = first; pp != null; pp = pp.next) {
					int tpPos = pp.position + pp.offset;
					for (PhrasePositionsBoost pp2 = pp.next; pp2 != null; pp2 = pp2.next) {
						int tpPos2 = pp2.position + pp2.offset;
						if (tpPos2 == tpPos) { 
							if (m == null)
								m = new HashMap();
							pp.repeats = true;
							pp2.repeats = true;
							m.put(pp,null);
							m.put(pp2,null);
						}
					}
				}
				if (m!=null)
					repeats = (PhrasePositionsBoost[]) m.keySet().toArray(new PhrasePositionsBoost[0]);
			}

			// with repeats must advance some repeating pp's so they all start with differing tp's       
			if (repeats!=null) {
				// must propagate higher offsets first (otherwise might miss matches).
				Arrays.sort(repeats,  new Comparator() {
					public int compare(Object x, Object y) {
						return ((PhrasePositionsBoost) y).offset - ((PhrasePositionsBoost) x).offset;
					}});
				// now advance them
				for (int i = 0; i < repeats.length; i++) {
					PhrasePositionsBoost pp = repeats[i];
					while (!termPositionsDiffer(pp)) {
						if (!pp.nextPosition())
							return -1;    // ran out of a term -- done  
					} 
				}
			}

			// build queue from list
			pq.clear();
			for (PhrasePositionsBoost pp = first; pp != null; pp = pp.next) {
				if (pp.position > end)
					end = pp.position;
				pq.put(pp);         // build pq from list
			}

			return end;
		}

		// disalow two pp's to have the same tp position, so that same word twice 
		// in query would go elswhere in the matched doc
		private boolean termPositionsDiffer(PhrasePositionsBoost pp) {
			// efficiency note: a more efficient implemention could keep a map between repeating 
			// pp's, so that if pp1a, pp1b, pp1c are repeats term1, and pp2a, pp2b are repeats 
			// of term2, pp2a would only be checked against pp2b but not against pp1a, pp1b, pp1c. 
			// However this would complicate code, for a rather rare case, so choice is to compromise here.
			int tpPos = pp.position + pp.offset;
			for (int i = 0; i < repeats.length; i++) {
				PhrasePositionsBoost pp2 = repeats[i];
				if (pp2 == pp)
					continue;
				int tpPos2 = pp2.position + pp2.offset;
				if (tpPos2 == tpPos)
					return false;
			}
			return true;
		}
	}

}
