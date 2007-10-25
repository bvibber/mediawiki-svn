package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.TermPositions;

/** Scorer when there is only one term in phrase */
public class TermCustomPhraseScorer extends CustomPhraseScorer {
	
	TermCustomPhraseScorer(Weight weight, TermPositions[] tps, int[] offsets, int stopWordCount, Similarity similarity, 
  		 byte[] norms, QueryOptions options, Scorer stemtitleScorer, Scorer relatedScorer) {
		    super(weight, tps, offsets, stopWordCount, similarity, norms, options, stemtitleScorer, relatedScorer);
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
