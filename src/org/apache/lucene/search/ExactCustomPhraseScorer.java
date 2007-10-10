package org.apache.lucene.search;

/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

import java.io.IOException;
import org.apache.lucene.index.*;
import org.wikimedia.lsearch.search.RankValue;

final class ExactCustomPhraseScorer extends CustomPhraseScorer {

  ExactCustomPhraseScorer(Weight weight, TermPositions[] tps, int[] offsets, Similarity similarity,
                    byte[] norms, ScoreValue val) {
    super(weight, tps, offsets, similarity, norms, val);
  }

  protected final float phraseFreq() throws IOException {
    // sort list with pq
    pq.clear();
    for (PhrasePositions pp = first; pp != null; pp = pp.next) {
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

