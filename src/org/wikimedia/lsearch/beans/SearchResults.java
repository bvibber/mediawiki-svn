package org.wikimedia.lsearch.beans;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;

import org.wikimedia.lsearch.spell.SuggestQuery;

/** Complete search results for a query, also containts
 *  info about the error if any. 
 * 
 * @author rainman
 *
 */
public class SearchResults implements Serializable {
	/** false if there is some sort of query-related error */
	protected boolean success;
	protected int numHits;
	protected ArrayList<ResultSet> results;
	protected String errorMsg;
	protected boolean retry;
	protected SuggestQuery suggest;
	protected ArrayList<ResultSet> titles;
	public enum Format { STANDARD, JSON, OPENSEARCH };
	protected Format format = Format.STANDARD;
	/** phrases (two_words) from highlight to aid spellchecking */
	protected HashSet<String> phrases = new HashSet<String>();
	/** words found together in sentence, aid spellchecking */
	protected HashSet<String> foundInContext = new HashSet<String>();
	/** If we found all query words in a single title during highlighting */ 
	protected boolean foundAllInTitle = false;
	
	public SearchResults(){
		success = false;
		numHits = 0;
		results = new ArrayList<ResultSet>();
		errorMsg = "";
		retry = false;
		suggest = null;
		titles = null;
	}

	/** Temporal error, retry the search query */
	public void retry(){
		retry = true;
	}

	public boolean isRetry() {
		return retry;
	}

	public String getErrorMsg() {
		return errorMsg;
	}

	public void setErrorMsg(String errorMsg) {
		this.errorMsg = errorMsg;
		success = false;
	}

	public int getNumHits() {
		return numHits;
	}
	public void setNumHits(int numHits) {
		this.numHits = numHits;
	}
	public boolean isSuccess() {
		return success;
	}
	public void setSuccess(boolean success) {
		this.success = success;
	}
	public ArrayList<ResultSet> getResults() {
		return results;
	}
	public void addResult(ResultSet rs){
		success = true;
		results.add(rs);
	}
	public ArrayList<ResultSet> getTitles() {
		return titles;
	}
	public void setTitles(ArrayList<ResultSet> titles) {
		this.titles = titles;
	}
	public void addTitlesResult(ResultSet rs){
		titles.add(rs);
	}

	public SuggestQuery getSuggest() {
		return suggest;
	}
	public void setSuggest(SuggestQuery suggest) {
		this.suggest = suggest;
	}
	
	public Format getFormat() {
		return format;
	}
	public void setFormat(Format format) {
		this.format = format;
	}
	public HashSet<String> getFoundInContext() {
		return foundInContext;
	}
	public void setFoundInContext(HashSet<String> foundInContext) {
		this.foundInContext = foundInContext;
	}
	public HashSet<String> getPhrases() {
		return phrases;
	}
	public void setPhrases(HashSet<String> phrases) {
		this.phrases = phrases;
	}
	public boolean isFoundAllInTitle() {
		return foundAllInTitle;
	}
	public void setFoundAllInTitle(boolean foundAllInTitle) {
		this.foundAllInTitle = foundAllInTitle;
	}

	@Override
	public String toString() {
		return ((success)? "SUCC: " : "FAIL: " ) + "hits="+numHits+" "+Arrays.toString(results.toArray());
	}		
	
	
}
