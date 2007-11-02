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

import org.wikimedia.lsearch.related.RelatedTitle;

/**
 * Wiki article. 
 * 
 * @author Kate Turner
 *
 */
public class Article implements Serializable  {
	private String namespace, title, contents;
	private boolean redirect;
	private long pageId;
	private int references;
	/** all page that redirect to this page with their reference count */
	private ArrayList<Redirect> redirects;  
	/** generated before indexing from the list of redirects */
	private transient ArrayList<String> redirectKeywords;
	/** paired with previous list, ranks for each redirect */
	private transient ArrayList<Integer> redirectKeywordRanks;
	/** generated before indexing from the reference sto this article, and references from redirects */
	private transient int rank;
	/** names of articles that relate to this article  */
	private ArrayList<RelatedTitle> related;
	/** names of articles that relate to this article  */
	private ArrayList<String> anchorText;
	/** If this is redirect, the redirect target namespace */
	private int redirectTargetNamespace = 0;
	
	public Article(){
		namespace="";
		title="";
		contents="";
		pageId = 0;
		redirect=false;
		references = 0;
		redirects=new ArrayList<Redirect>();
		related = new ArrayList<RelatedTitle>();
		anchorText = new ArrayList<String>();
	}
	
	public Article(long pageId, Title title, String text, boolean redirect, int references, int redirectTargetNamespace) {
		namespace = Integer.toString(title.getNamespace());
		this.title = title.getTitle();
		contents = text;
		this.pageId = pageId;
		this.redirect = redirect;
		this.references = references;
		this.redirects = new ArrayList<Redirect>();
		this.related = new ArrayList<RelatedTitle>();
		this.anchorText = new ArrayList<String>();
		this.redirectTargetNamespace = redirectTargetNamespace;
	}
	
	public Article(long pageId, int namespace, String titleText, String text, boolean redirect, int references, int redirectTargetNamespace) {
		this.namespace = Integer.toString(namespace);
		this.title = titleText;
		contents = text;
		this.redirect = redirect;
		this.pageId = pageId;
		this.references = references;
		this.redirects = new ArrayList<Redirect>();
		this.related = new ArrayList<RelatedTitle>();
		this.anchorText = new ArrayList<String>();
		this.redirectTargetNamespace = redirectTargetNamespace;
	}
	
	public Article(long pageId, int namespace, String titleText, String text, boolean redirect, int references, int redirectTargetNamespace, 
			ArrayList<Redirect> redirects, ArrayList<RelatedTitle> related, ArrayList<String> anchorText) {
		this.namespace = Integer.toString(namespace);
		this.title = titleText;
		contents = text;
		this.redirect = redirect;
		this.pageId = pageId;
		this.references = references;
		this.redirects = redirects;
		this.related = related;
		this.anchorText = anchorText;
		this.redirectTargetNamespace = redirectTargetNamespace;
	}
	
	public boolean isRedirect() {
		return redirect;
	}
	
	public void setRedirect(boolean redirect) {
		this.redirect = redirect;
	}
	/**
	 * @return Returns the contents.
	 */
	public String getContents() {
		return contents;
	}

	/**
	 * @return Returns the namespace.
	 */
	public String getNamespace() {
		return namespace;
	}
	
	/**
	 * @return Returns the title.
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
	/**
	 * Return page_id
	 * 
	 * @return Returns unique id.
	 */
	public String getKey() {
		return Long.toString(pageId);
	}
	
	public String toString() {
		return "(" + namespace + ",\"" + title + "\")";
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

	public ArrayList<String> getAnchorText() {
		return anchorText;
	}

	public void setAnchorText(ArrayList<String> anchorText) {
		this.anchorText = anchorText;
	}

	public int getRedirectTargetNamespace() {
		return redirectTargetNamespace;
	}

	public void setRedirectTargetNamespace(int redirectTargetNamespace) {
		this.redirectTargetNamespace = redirectTargetNamespace;
	}	
	
	
	
}
