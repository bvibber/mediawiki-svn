package org.wikimedia.lsearch.spell;

import java.io.Serializable;
import java.util.ArrayList;

/** Result of suggestion for a query */
public class SuggestQuery implements Serializable {
	protected String searchterm;
	protected ArrayList<Integer> ranges;
	protected ArrayList<String> similarTitles;
	
	public SuggestQuery(String searchterm, ArrayList<Integer> ranges) {
		this.searchterm = searchterm;
		this.ranges = ranges;
	}
	
	private String serializeIntList(ArrayList<Integer> list){
		StringBuilder sb = new StringBuilder();
		boolean first = true;
		for(Integer i : list){
			if(!first)
				sb.append(",");
			else
				first = false;
			sb.append(i);
		}
		return sb.toString();
	}
	
	/** the suggested search term */
	public String getSearchterm() {
		return searchterm;
	}
	public void setSearchterm(String searchterm) {
		this.searchterm = searchterm;
	}
	
	public ArrayList<Integer> getRanges() {
		return ranges;
	}
	public void setRanges(ArrayList<Integer> ranges) {
		this.ranges = ranges;
	}
	
	public String getRangesSerialized(){
		return serializeIntList(ranges);
	}
	
	@Override
	public String toString() {
		return serializeIntList(ranges)+" "+searchterm;
	}
	
	public boolean hasSuggestion(){
		return ranges.size()>0;
	}
	
	
	
	
}
