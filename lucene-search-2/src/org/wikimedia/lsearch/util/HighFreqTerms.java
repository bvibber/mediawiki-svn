package org.wikimedia.lsearch.util;

/**
 * Copyright 2004 The Apache Software Foundation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
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
import java.util.ArrayList;
import java.util.Collection;
import java.util.LinkedList;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermEnum;
import org.apache.lucene.index.TermPositions;
import org.apache.lucene.util.PriorityQueue;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.search.SearcherCache;

/**
 * <code>HighFreqTerms</code> class extracts terms and their frequencies out
 * of an existing Lucene index.
 *
 * @version $Id: HighFreqTerms.java 376393 2006-02-09 19:17:14Z otis $
 */
public class HighFreqTerms {

	public static IndexId getIndexPart(IndexId iid){
		IndexId targ = iid;
		if(iid.isMainsplit())
			targ = iid.getMainPart();
		else if(iid.isNssplit())
			targ = iid.getPartByNamespace(0);
		if(targ.isSubdivided()){
			targ = targ.getSubpart(0);
		}				
		return targ;
	}
	
	/** 
	 * Get terms with highest frequency from latest snapshot of iid 
	 */
	public static Collection<String> getHighFreqTerms(IndexId iid, String field, int numTerms) throws IOException {
		IndexRegistry registry = IndexRegistry.getInstance();
		IndexReader reader = null;
		IndexId part = getIndexPart(iid);
		LocalIndex snapshot = registry.getLatestSnapshot(part);
		if(snapshot != null) // try snapshot
			reader = IndexReader.open(snapshot.getPath());
		else // try import path
			reader = IndexReader.open(part.getImportPath());
		TermInfoQueue tiq = new TermInfoQueue(numTerms);
		TermEnum terms = reader.terms();
		LinkedList<String> ret = new LinkedList<String>();

		if (field != null) { 
			// collect terms from field into priority queue
			while (terms.next()) {
				if (terms.term().field().equals(field)) {
					TermPositions pos = reader.termPositions(terms.term());
					int freq = 0;
					while(pos.next())
						freq += pos.freq();
					tiq.insert(new TermInfo(terms.term(), freq));
				}
			}
		} else {
			// collect all terms
			while (terms.next()) {
				tiq.insert(new TermInfo(terms.term(), terms.docFreq()));
			}
		}

		// get higest ranked
		while (tiq.size() != 0) {
			ret.addFirst(((TermInfo) tiq.pop()).term.text());
		}

		return ret;
	}
}

final class TermInfo {
	TermInfo(Term t, int df) {
		term = t;
		docFreq = df;
	}
	int docFreq;
	Term term;
}

final class TermInfoQueue extends PriorityQueue {
	TermInfoQueue(int size) {
		initialize(size);
	}
	protected final boolean lessThan(Object a, Object b) {
		TermInfo termInfoA = (TermInfo) a;
		TermInfo termInfoB = (TermInfo) b;
		return termInfoA.docFreq < termInfoB.docFreq;
	}
}
