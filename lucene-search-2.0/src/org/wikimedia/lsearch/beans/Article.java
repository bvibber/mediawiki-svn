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
	private int rank;
	
	public Article(){
		namespace="";
		title="";
		contents="";
		pageId = 0;
		redirect=false;
		rank=0;
	}
	
	public Article(long pageId, Title title, String text, boolean redirect, int rank) {
		namespace = Integer.toString(title.getNamespace());
		this.title = title.getTitle();
		contents = text;
		this.pageId = pageId;
		this.redirect = redirect;
		this.rank = rank;
	}
	
	public Article(long pageId, int namespace, String titleText, String text, boolean redirect, int rank) {
		this.namespace = Integer.toString(namespace);
		this.title = titleText;
		contents = text;
		this.redirect = redirect;
		this.pageId = pageId;
		this.rank = rank;
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
	
	public long getPageId() {
		return pageId;
	}
	/**
	 * Articles need to be easily identifiable by an exact match
	 * for replacement of updated items when updating an existing
	 * search index.
	 * 
	 * @return Returns unique id.
	 */
	public String getKey() {
		return Long.toString(pageId);
	}
	
	public String toString() {
		return "(" + namespace + ",\"" + title + "\")";
	}
	
	public int getRank() {
		return rank;
	}
	
}
