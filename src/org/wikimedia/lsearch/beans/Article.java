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
	private String namespace, title, contents, timestamp;
	private boolean redirect;
	
	public Article(){
		namespace="";
		title="";
		contents="";
		timestamp="";
		redirect=false;
	}
	public Article(String dbname, String namespace_, 
			String title_, String contents_, String timestamp_, boolean isRedirect_) {
		namespace = namespace_;
		title = title_;
		contents = contents_;
		timestamp = timestamp_;
		redirect = isRedirect_;
	}
	
	public Article(Title title, String text, boolean redirect) {
		namespace = Integer.toString(title.getNamespace());
		this.title = title.getTitle();
		contents = text;
		timestamp = null;
		this.redirect = redirect;
	}
	
	public Article(int namespace, String titleText, String text, boolean redirect) {
		this.namespace = Integer.toString(namespace);
		this.title = titleText;
		contents = text;
		timestamp = null;
		this.redirect = redirect;
	}
	
	public Article(int namespace_, String title_) {
		namespace = Integer.toString(namespace_);
		title = title_;
		contents = null;
		timestamp = null;
		redirect = false;
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
	 * @return Returns the timestamp.
	 */
	public String getTimestamp() {
		return timestamp;
	}
	
	/**
	 * @return Returns the title.
	 */
	public String getTitle() {
		return title;
	}

	/**
	 * Articles need to be easily identifiable by an exact match
	 * for replacement of updated items when updating an existing
	 * search index.
	 * 
	 * @return Returns unique id.
	 */
	public String getKey() {
		return getNamespace() + ":" + getTitle();
	}
	
	public String toString() {
		return "(" + namespace + ",\"" + title + "\")";
	}
}
