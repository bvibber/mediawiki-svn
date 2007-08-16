package org.wikimedia.lsearch.ranks;

public class Related {
	protected CompactArticleLinks title;
	protected CompactArticleLinks relates;
	protected double score;
	public Related(CompactArticleLinks title, CompactArticleLinks relates, double score) {
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
	
	
	
}
