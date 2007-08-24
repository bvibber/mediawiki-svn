package org.wikimedia.lsearch.spell;

/** Result of suggestion for a query */
public class SuggestQuery {
	protected String searchterm;
	protected boolean needsCheck;
	public SuggestQuery(String searchterm) {
		this(searchterm,false);
	}
	public SuggestQuery(String searchterm, boolean needsCheck) {
		this.searchterm = searchterm;
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
	@Override
	public String toString() {
		return needsCheck? searchterm+" [needs check]" : searchterm;
	}
	
	
	
	
}
