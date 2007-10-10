package org.wikimedia.lsearch.related;

import java.util.ArrayList;
import java.util.Collection;

import org.wikimedia.lsearch.beans.Title;

public class Related {
	protected String title;
	protected String relates;
	protected double score;
	public Related(String title, String relates, double score) {
		this.title = title;
		this.relates = relates;
		this.score = score;
	}
	
	public Related(String serialized) {
		this.title = null;
		int i = serialized.indexOf(' ');
		this.score = Double.parseDouble(serialized.substring(0,i));
		this.relates = serialized.substring(i+1);		
	}
	
	@Override
	public String toString() {
		return title+"->"+relates+" : "+score;
	}
	
	
	public static ArrayList<String> convertToStringList(Collection<Related> rel){
		ArrayList<String> ret = new ArrayList<String>();
		for(Related r : rel){
			ret.add(r.serialize());
		}
		return ret;
	}
	
	public static ArrayList<Related> convertToRelatedList(Collection<String> sl){
		ArrayList<Related> ret = new ArrayList<Related>();
		for(String s : sl){
			ret.add(new Related(s));
		}
		return ret;
	}
	
	public static ArrayList<RelatedTitle> convertToRelatedTitleList(Collection<String> sl){
		ArrayList<RelatedTitle> ret = new ArrayList<RelatedTitle>();
		for(String s : sl){
			Related r = new Related(s);			
			ret.add(new RelatedTitle(new Title(r.getRelates()),r.getScore()));
		}
		return ret;
	}
	
	public String serialize(){
		return (float)score+" "+relates;
	}
	
	public String getRelates() {
		return relates;
	}
	public void setRelates(String relates) {
		this.relates = relates;
	}
	public double getScore() {
		return score;
	}
	public void setScore(double score) {
		this.score = score;
	}
	public String getTitle() {
		return title;
	}
	public void setTitle(String title) {
		this.title = title;
	}
	
	
	
}
