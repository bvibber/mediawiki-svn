package org.wikimedia.lsearch.beans;

import java.io.Serializable;

import org.apache.lucene.search.Explanation;

/** A single search result */
public class ResultSet implements Serializable {
	public double score;
	public String namespace;
	public String title;
	Explanation explanation;
	
	public ResultSet(String key) {
		int colon = key.indexOf(':');
		this.score = 0;
		this.namespace = key.substring(0,colon);
		this.title = key.substring(colon+1);
		this.explanation = null;
	}
	public ResultSet(double score, String namespace, String title) {
		this.score = score;
		this.namespace = namespace;
		this.title = title;
		this.explanation = null;
	}
	
	public ResultSet(double score, String namespace, String title, Explanation explanation) {
		this.score = score;
		this.namespace = namespace;
		this.title = title;
		this.explanation = explanation;
	}
	
	public Explanation getExplanation() {
		return explanation;
	}

	public void setExplanation(Explanation explanation) {
		this.explanation = explanation;
	}

	@Override
	public String toString() {
		return score+" "+namespace+":"+title+(explanation==null? "" : "\n"+explanation);
	}		
	
	
}
