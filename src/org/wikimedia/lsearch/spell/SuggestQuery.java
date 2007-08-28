package org.wikimedia.lsearch.spell;

/** Result of suggestion for a query */
public class SuggestQuery {
	protected String searchterm;
	protected String formated;
	protected boolean needsCheck;
	public SuggestQuery(String searchterm, String formated){
		this(searchterm,formated,false);
	}
	public SuggestQuery(String searchterm, String formated, boolean needsCheck) {
		this.searchterm = searchterm;
		this.formated = formated;
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
	public String getFormated() {
		return formated;
	}
	public void setFormated(String formated) {
		this.formated = formated;
	}
	@Override
	public String toString() {
		return needsCheck? formated+" [needs check]" : formated;
	}
	
	
	
	
}
