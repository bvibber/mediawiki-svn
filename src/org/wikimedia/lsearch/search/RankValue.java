package org.wikimedia.lsearch.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.ScoreValue;
import org.wikimedia.lsearch.search.RankField.RankFieldSource;

public class RankValue implements ScoreValue {
	protected RankFieldSource src;
	protected float coefficient;
	
	public RankValue(){
		this(15);
	}
	
	public RankValue(float coefficient){
		this.coefficient = coefficient;
	}
	
	/** Initialize source for a reader */
	public void init(IndexReader reader) throws IOException{
		src = RankField.getCachedSource(reader);
	}
	
	/** Get a rank-based score for docid */
	public float score(int docid){
		float rank = src.get(docid);
		return (float) (1 + Math.log(1+rank/coefficient)/Math.log(4));
	}
}
