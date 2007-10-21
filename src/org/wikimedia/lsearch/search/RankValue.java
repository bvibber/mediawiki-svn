package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.io.Serializable;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.ScoreValue;
import org.wikimedia.lsearch.search.RankField.RankFieldSource;

public class RankValue implements ScoreValue, Serializable {
	protected transient RankFieldSource src;
	protected float coefficient;
	
	public RankValue(){
		this(20);
	}
	
	public RankValue(float coefficient){
		this.coefficient = coefficient;
	}
	
	/** Initialize source for a reader */
	public void init(IndexReader reader) throws IOException{
		src = RankField.getCachedSource(reader);
	}
	
	public float score(int docid, int scale){
		float rank = src.get(docid);
		return (float) (1 + Math.log(1+rank/coefficient)/Math.log(scale));
	}
	
	/** Get a rank-based score for docid */
	public float score(int docid){
		return score(docid,4);
	}
	
	public int value(int docid){
		return src.get(docid);
	}
}
