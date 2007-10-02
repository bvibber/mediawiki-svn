package org.wikimedia.lsearch.related;

import java.util.ArrayList;

import org.wikimedia.lsearch.beans.Title;

public class RelatedTitle {
	protected Title related;
	protected double score;
	protected ArrayList<String> contexts = null;
	
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
	public ArrayList<String> getContexts() {
		return contexts;
	}
	public void setContexts(ArrayList<String> contexts) {
		this.contexts = contexts;
	}
	@Override
	public String toString() {
		return related.toString()+" ("+score+")";
	}
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((related == null) ? 0 : related.hashCode());
		long temp;
		temp = Double.doubleToLongBits(score);
		result = PRIME * result + (int) (temp ^ (temp >>> 32));
		return result;
	}
	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final RelatedTitle other = (RelatedTitle) obj;
		if (related == null) {
			if (other.related != null)
				return false;
		} else if (!related.equals(other.related))
			return false;
		if (Double.doubleToLongBits(score) != Double.doubleToLongBits(other.score))
			return false;
		return true;
	}
	
	
	
	
}
