package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.TermPositions;

/** Scorer when there is only one term in phrase */
public class TermCustomPhraseScorer extends CustomPhraseScorer {
	
	  TermCustomPhraseScorer(Weight weight, TermPositions[] tps, int[] offsets, Similarity similarity,
           byte[] norms, ScoreValue val, boolean beginBoost, PhraseInfo phInfo, Scorer stemtitleScorer, Scorer relatedScorer,
           Weight stemtitleWeight, Weight relatedWeight, boolean max) {
		    super(weight, tps, offsets, similarity, norms, val, beginBoost, phInfo, stemtitleScorer, relatedScorer, stemtitleWeight, relatedWeight, max);
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
