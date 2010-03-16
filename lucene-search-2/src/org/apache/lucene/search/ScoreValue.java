package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;

/** 
 * Pluggable score value that can be incorporate into custom
 * phrase query.  
 * 
 * @author rainman
 *
 */
public interface ScoreValue {
	/** Initialize for retrieval of scores */
	public void init(IndexReader reader) throws IOException;
	
	/** Get score of document #docid */
	public float score(int docid);
	
	/** Get score of document #docid, scaled with scale */
	public float score(int docid, int scale);
	
	/** Get the raw value */
	public int value(int docid);
}
