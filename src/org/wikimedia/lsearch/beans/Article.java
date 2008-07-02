/*
 * Copyright 2004 Kate Turner
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * $Id: Article.java 8555 2005-04-24 00:26:38Z vibber $
 */

package org.wikimedia.lsearch.beans;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Date;
import java.util.Hashtable;

import org.wikimedia.lsearch.related.RelatedTitle;

/**
 * Wiki article. 
 * 
 * @author Kate Turner
 *
 */
public class Article implements Serializable  {
	private String namespace, title, contents;
	private long pageId;
	private int references;
	/** all page that redirect to this page with their reference count */
	private ArrayList<Redirect> redirects;	
	private Date date = null;
	
	/** sorted list of redirect names */
	private transient ArrayList<String> redirectKeywords;
	/** sorted list of ranks */
	private transient ArrayList<Integer> redirectKeywordRanks;
	/** sorted list of whole redirects */
	private transient ArrayList<Redirect> redirectsSorted;
	/** generated before indexing from the reference sto this article, and references from redirects */
	private transient int rank;
	
	/** names of articles that relate to this article  */
	private ArrayList<RelatedTitle> related;
	/** names of articles that relate to this article  */
	private Hashtable<String,Integer> anchors;
	/** If this is redirect, the redirect target namespace */
	private int redirectTargetNamespace = 0;
	/** Target if this is redirect, null otherwise */
	private String redirectTo = null;
	/** Rank of the redirect target */
	private int redirectRank = 0;
	
	public Article(){
		namespace="";
		title="";
		contents="";
		pageId = 0;
		references = 0;
		redirects=new ArrayList<Redirect>();
		related = new ArrayList<RelatedTitle>();
		anchors = new Hashtable<String,Integer>();
	}
	
	public Article(long pageId, Title title, String text, String redirectTo, int references, int redirectTargetNamespace, int redirectRank) {
		this(pageId,title.getNamespace(),title.getTitle(),text,redirectTo,references,redirectTargetNamespace,redirectRank);		
	}
	
	public Article(long pageId, int namespace, String titleText, String text, String redirectTo, int references, int redirectTargetNamespace, int redirectRank) {
		this(pageId,namespace,titleText,text,redirectTo,references,redirectTargetNamespace,redirectRank,new ArrayList<Redirect>(),new ArrayList<RelatedTitle>(),new Hashtable<String,Integer>(),null);		
	}
	
	public Article(long pageId, int namespace, String titleText, String text, String redirectTo, int references, int redirectTargetNamespace, int redirectRank,
			ArrayList<Redirect> redirects, ArrayList<RelatedTitle> related, Hashtable<String,Integer> anchorRank, Date date) {
		this.namespace = Integer.toString(namespace);
		this.title = titleText;
		this.contents = text;
		this.redirectTo = redirectTo;
		this.pageId = pageId;
		this.references = references;
		this.redirects = redirects;
		this.related = related;
		this.anchors = anchorRank;
		this.redirectTargetNamespace = redirectTargetNamespace;
		this.date = date;
		this.redirectRank = redirectRank;
	}
	
	public boolean isRedirect() {
		return redirectTo != null;
	}
	
	public String getRedirectTarget(){
		return redirectTo;
	}
	
	/**
	 * @return Returns article body
	 */
	public String getContents() {
		return contents;
	}

	/**
	 * @return Returns numerical namespace
	 */
	public String getNamespace() {
		return namespace;
	}
	
	/**
	 * @return Pain title without namespace prefix, and with spaces not underscores
	 */
	public String getTitle() {
		return title;
	}
	
	public Title getTitleObject(){
		return new Title(Integer.parseInt(namespace),title);
	}
	
	public long getPageId() {
		return pageId;
	}
	
	public String getPageIdStr(){
		return Long.toString(pageId);
	}
	/**
	 * Return page_id
	 * 
	 * @return Returns unique id.
	 */
	public String getIndexKey() {
		return Long.toString(pageId);
	}
	
	public String toStringFull(){
		return "(" + pageId + ", " + namespace + ":" + title + ", rank="+rank+", ref="+references+", redirectRank="+redirectRank+", redirects="+redirectsSorted+", date="+date+")";
	}
	
	public String toString() {
		return "(" + pageId + ", " + namespace + ":" + title + ", date="+date+")";
	}
	
	/** Page Rank: how many articles link to this article */
	public int getRank() {
		return rank;
	}
	
	public void setRank(int r){
		rank = r;
	}
	
	/** Get number of page links to this page (excluding links from redirects) */
	public int getReferences() {
		return references;
	}
	
	public void setReferences(int references) {
		this.references = references;
	}

	/** Add value to rank (useful when processing redirects to this page) */
	public void addToRank(int val){
		rank += val;
	}
	
	/** Register a redirect to this article */
	public void addRedirect(Redirect linkingArticle){
		redirects.add(linkingArticle);
	}
	
	/** Register a list of redirects to this article */
	public void addRedirects(Collection<Redirect> linkingArticles){
		redirects.addAll(linkingArticles);
	}
	
	/** Get list of articles that redirect to this article */
	public ArrayList<Redirect> getRedirects() {
		return redirects;
	}

	public void setRedirects(ArrayList<Redirect> redirects) {
		this.redirects = redirects;
	}

	/** Get redirect names without the namespace prefix, as they should be indexed */
	public ArrayList<String> getRedirectKeywords() {
		return redirectKeywords;
	}

	public void setRedirectKeywords(ArrayList<String> redirectKeywords) {
		this.redirectKeywords = redirectKeywords;
	}

	public ArrayList<Integer> getRedirectKeywordRanks() {
		return redirectKeywordRanks;
	}

	public void setRedirectKeywordRanks(ArrayList<Integer> redirectKeywordRanks) {
		this.redirectKeywordRanks = redirectKeywordRanks;
	}
	
	/** Get title object corresponding to this article */
	public Title makeTitle(){
		return new Title(Integer.parseInt(namespace),title);
	}

	public void setContents(String contents) {
		this.contents = contents;
	}

	public ArrayList<RelatedTitle> getRelated() {
		return related;
	}

	public void setRelated(ArrayList<RelatedTitle> related) {
		this.related = related;
	}

	public Hashtable<String, Integer> getAnchorRank() {
		return anchors;
	}

	public void setAnchors(Hashtable<String, Integer> anchorRank) {
		this.anchors = anchorRank;
	}

	public int getRedirectTargetNamespace() {
		return redirectTargetNamespace;
	}

	public void setRedirectTargetNamespace(int redirectTargetNamespace) {
		this.redirectTargetNamespace = redirectTargetNamespace;
	}

	public ArrayList<Redirect> getRedirectsSorted() {
		return redirectsSorted;
	}

	public void setRedirectsSorted(ArrayList<Redirect> redirectsSorted) {
		this.redirectsSorted = redirectsSorted;
	}

	public Date getDate() {
		return date;
	}

	public void setDate(Date date) {
		this.date = date;
	}

	public void setRedirectTo(String redirectTo) {
		this.redirectTo = redirectTo;
	}	
	
	public String getNsTitleKey(){
		return namespace+":"+title;
	}
	
	public void setNsTitleKey(String key){
		int col = key.indexOf(':');
		if(col == -1)
			throw new RuntimeException("Wrong key format");
		this.namespace = key.substring(0,col);
		this.title = key.substring(col+1);
	}

	public int getRedirectRank() {
		return redirectRank;
	}

	public void setRedirectRank(int redirectRank) {
		this.redirectRank = redirectRank;
	}
	

	
}
