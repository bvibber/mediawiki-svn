package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.io.Serializable;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.PhraseInfo;
import org.wikimedia.lsearch.analyzers.AggregateAnalyzer;
import org.wikimedia.lsearch.search.AggregateMetaField.AggregateMetaFieldSource;

/** 
 * Wrapper for aggregate fields info in the index. Include an instance
 * of this class into CustomPhraseQuery to use the additional meta
 * info (which is locally cached in AggregateMetaField). 
 * 
 * @author rainman
 *
 */
public class AggregatePhraseInfo implements PhraseInfo, Serializable  {
	protected transient AggregateMetaFieldSource src = null;
	
	/** Call this while (local) scorer is constructed to init cached meta info */
	public void init(IndexReader reader, String field) throws IOException {
		src = AggregateMetaField.getCachedSource(reader,field);
	}

	protected int getSlot(int pos){
		return pos / AggregateAnalyzer.TOKEN_GAP;
	}
	
	public int length(int docid, int pos) {
		return src.getLength(docid,getSlot(pos));
	}
	
	public float boost(int docid, int pos) {
		return src.getBoost(docid,getSlot(pos));
	}

	public int lengthNoStopWords(int docid, int pos) {
		return src.getLengthNoStopWords(docid,getSlot(pos));
	}

}
