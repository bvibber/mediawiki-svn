package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.IndexReader;
import org.wikimedia.lsearch.analyzers.Aggregate.Flags;

/** 
 * Meta information for a field in the index.
 * 
 * Allows collections of independent entries each with
 * its own boost, length, etc...   
 * 
 */
public interface AggregateInfo {
	/** Initialize for retrieval of info */
	public void init(IndexReader reader, String field) throws IOException;
	
	/** length of phrase at position pos */
	public int length(int docid, int pos) throws IOException;	
	
	/** length of phrase at position pos, excluding stop words */
	public int lengthNoStopWords(int docid, int pos) throws IOException;
	
	/** length of phrase at position pos, with all the aliases */
	public int lengthComplete(int docid, int pos) throws IOException;
	
	/** boost for phrase at position pos*/
	public float boost(int docid, int pos) throws IOException;
	
	/** ranking boost for the whole document */
	public float rank(int docid) throws IOException;
	
	/** namespace of the document */
	public int namespace(int docid) throws IOException;
	
	/** if this meta provides ranking data */
	public boolean hasRankingData();
	
	/** get flags */
	public Flags flags(int docid, int pos) throws IOException;
}
