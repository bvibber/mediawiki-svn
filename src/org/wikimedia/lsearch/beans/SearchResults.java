package org.wikimedia.lsearch.beans;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;

import org.apache.lucene.index.Term;

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
	protected String suggest;
	protected ArrayList<ResultSet> titles;
	
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

	public String getSuggest() {
		return suggest;
	}
	public void setSuggest(String suggest) {
		this.suggest = suggest;
	}

	@Override
	public String toString() {
		return ((success)? "SUCC: " : "FAIL: " ) + "hits="+numHits+" "+Arrays.toString(results.toArray());
	}		
	
	
}
