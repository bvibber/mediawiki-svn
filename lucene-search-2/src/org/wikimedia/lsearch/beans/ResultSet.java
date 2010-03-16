package org.wikimedia.lsearch.beans;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Collection;

import org.apache.lucene.search.Explanation;
import org.wikimedia.lsearch.highlight.HighlightResult;

/** A single search result */
public class ResultSet implements Serializable {
	public double score;	
	public String namespace;
	public String title;
	public ArrayList<String> context;
	public Explanation explanation = null;
	public HighlightResult highlight;
	public String interwiki = null;
	public String suffix = null;
	public String namespaceTextual = ""; // e.g. "User" or "" for main
	
	public ResultSet(String key, double score) {
		int colon = key.indexOf(':');
		this.score = score;
		this.namespace = key.substring(0,colon);
		this.title = key.substring(colon+1);
	}
	public ResultSet(double score, String namespace, String title) {
		this.score = score;
		this.namespace = namespace;
		this.title = title;
	}
	
	public ResultSet(double score, String namespace, String title, String suffix, String interwiki) {
		this.score = score;
		this.namespace = namespace;
		this.title = title;
		this.interwiki = interwiki;
		this.suffix = suffix;
	}
	
	public ResultSet(double score, String namespace, String title, Explanation explanation) {
		this.score = score;
		this.namespace = namespace;
		this.title = title;
		this.explanation = explanation;
	}
	
	public Explanation getExplanation() {
		return explanation;
	}

	public void setExplanation(Explanation explanation) {
		this.explanation = explanation;
	}

	@Override
	public String toString() {
		return score+" "+namespace+":"+title+(explanation==null? "" : "\n"+explanation);
	}
	
	public void addContext(Collection<String> texts){
		if(texts == null)
			return;
		for(String t : texts)
			addContext(t); 
	}
	
	public void addContext(String text){
		if(context == null)
			context = new ArrayList<String>();
		
		context.add(text.replace('\n',' '));
	}
	
	public ArrayList<String> getContext(){
		return context;
	}
	
	public String getKey(){
		if(suffix == null)
			return namespace+":"+title;
		else
			return suffix+":"+namespace+":"+title;
	}
	public String getNamespace() {
		return namespace;
	}
	public void setNamespace(String namespace) {
		this.namespace = namespace;
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
	/** Title prefixed with textual namespace prefix */ 
	public String getPrefixedTitle(){
		if(namespaceTextual==null || namespaceTextual.equals(""))
			return title;
		else
			return namespaceTextual+":"+title;
	}
	public void setContext(ArrayList<String> context) {
		this.context = context;
	}
	public HighlightResult getHighlight() {
		return highlight;
	}
	public void setHighlight(HighlightResult highlight) {
		this.highlight = highlight;
	}
	public String getInterwiki() {
		return interwiki;
	}
	public void setInterwiki(String interwiki) {
		this.interwiki = interwiki;
	}
	public String getNamespaceTextual() {
		return namespaceTextual;
	}
	public void setNamespaceTextual(String namespaceTextual) {
		this.namespaceTextual = namespaceTextual;
	}
	public String getSuffix() {
		return suffix;
	}
	public void setSuffix(String suffix) {
		this.suffix = suffix;
	}
	
	
}
