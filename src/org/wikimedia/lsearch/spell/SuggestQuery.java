package org.wikimedia.lsearch.spell;

/** Result of suggestion for a query */
public class SuggestQuery {
	protected String searchterm;
	protected String ranges;
	protected boolean needsCheck;
	public SuggestQuery(String searchterm, String ranges){
		this(searchterm,ranges,false);
	}
	public SuggestQuery(String searchterm, String ranges, boolean needsCheck) {
		this.searchterm = searchterm;
		this.ranges = ranges;
		this.needsCheck = needsCheck;
	}
	/** Wether suggestion needs further checking (in case of individual word spell-check) */
	public boolean needsCheck() {
		return needsCheck;
	}
	public void setNeedsCheck(boolean needsCheck) {
		this.needsCheck = needsCheck;
	}
	/** the suggested search term */
	public String getSearchterm() {
		return searchterm;
	}
	public void setSearchterm(String searchterm) {
		this.searchterm = searchterm;
	}
	
	public String getRanges() {
		return ranges;
	}
	public void setRanges(String ranges) {
		this.ranges = ranges;
	}
	
	public String getSerialized(){
		return ranges+" "+searchterm;
	}
	@Override
	public String toString() {
		return needsCheck? getSerialized()+" [needs check]" : getSerialized();
	}
	
	
	
	
}
