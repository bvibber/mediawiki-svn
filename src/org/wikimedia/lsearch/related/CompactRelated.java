package org.wikimedia.lsearch.related;

import java.util.ArrayList;
import java.util.Collection;


public class CompactRelated {
	protected CompactArticleLinks title;
	protected CompactArticleLinks relates;
	protected double score;
	public CompactRelated(CompactArticleLinks title, CompactArticleLinks relates, double score) {
		this.title = title;
		this.relates = relates;
		this.score = score;
	}
	@Override
	public String toString() {
		return title+"->"+relates+" : "+score;
	}
	public CompactArticleLinks getRelates() {
		return relates;
	}
	public void setRelates(CompactArticleLinks relates) {
		this.relates = relates;
	}
	public double getScore() {
		return score;
	}
	public void setScore(double score) {
		this.score = score;
	}
	public CompactArticleLinks getTitle() {
		return title;
	}
	public void setTitle(CompactArticleLinks title) {
		this.title = title;
	}
	public String serialize(){
		return ((float)score)+" "+relates;
	}
	
	public static ArrayList<String> convertToStringList(Collection<CompactRelated> rel){
		ArrayList<String> ret = new ArrayList<String>();
		for(CompactRelated r : rel){
			ret.add(r.serialize());
		}
		return ret;
	}
	
	
}
