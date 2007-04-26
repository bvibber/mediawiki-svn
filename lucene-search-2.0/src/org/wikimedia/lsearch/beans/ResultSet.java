package org.wikimedia.lsearch.beans;

import java.io.Serializable;

/** A single search result */
public class ResultSet implements Serializable {
	public double score;
	public String namespace;
	public String title;
	public ResultSet(double score, String namespace, String title) {
		this.score = score;
		this.namespace = namespace;
		this.title = title;
	}
	
	@Override
	public String toString() {
		return score+" "+namespace+":"+title;
	}		
	
	
}
