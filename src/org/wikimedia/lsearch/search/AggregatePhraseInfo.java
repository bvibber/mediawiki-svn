package org.wikimedia.lsearch.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.PhraseInfo;
import org.wikimedia.lsearch.analyzers.AggregateAnalyzer;
import org.wikimedia.lsearch.search.AggregateMetaField.AggregateMetaFieldSource;

/** 
 * Info about aggregate phrase fields. 
 * 
 * @author rainman
 *
 */
public class AggregatePhraseInfo implements PhraseInfo {
	AggregateMetaFieldSource src = null;
	
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

}
