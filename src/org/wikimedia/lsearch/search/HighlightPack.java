package org.wikimedia.lsearch.search;

import java.io.Serializable;

import org.apache.lucene.index.Term;
import org.wikimedia.lsearch.beans.SearchResults;

public class HighlightPack implements Serializable {
	public Term[] terms;
	public int[] dfs;
	public int maxDoc;
	public SearchResults res;
	
	public HighlightPack(SearchResults res) {
		this.res = res;
	}
	public int[] getDfs() {
		return dfs;
	}
	public void setDfs(int[] dfs) {
		this.dfs = dfs;
	}
	public int getMaxDoc() {
		return maxDoc;
	}
	public void setMaxDoc(int maxDoc) {
		this.maxDoc = maxDoc;
	}
	public Term[] getTerms() {
		return terms;
	}
	public void setTerms(Term[] terms) {
		this.terms = terms;
	}
	public SearchResults getRes() {
		return res;
	}
	public void setRes(SearchResults res) {
		this.res = res;
	}
}