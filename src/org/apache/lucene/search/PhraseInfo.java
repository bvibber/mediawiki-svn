package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;

/** 
 * If field is aggregate of phrases provide info 
 * about the matching phrase 
 */
public interface PhraseInfo {
	/** Initialize for retrieval of info */
	public void init(IndexReader reader, String field) throws IOException;
	
	/** length of phrase at position pos */
	public int length(int docid, int pos);	
	
	/** boost for phrase at position pos*/
	public float boost(int docid, int pos);
}
