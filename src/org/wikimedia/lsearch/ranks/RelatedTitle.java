package org.wikimedia.lsearch.ranks;

import org.wikimedia.lsearch.beans.Title;

public class RelatedTitle {
	protected Title related;
	protected double score;
	
	public RelatedTitle(Title related, double score) {
		this.related = related;
		this.score = score;
	}
	public Title getRelated() {
		return related;
	}
	public void setRelated(Title related) {
		this.related = related;
	}
	public double getScore() {
		return score;
	}
	public void setScore(double score) {
		this.score = score;
	}
	@Override
	public String toString() {
		return related.toString()+" ("+score+")";
	}
	
	
}
