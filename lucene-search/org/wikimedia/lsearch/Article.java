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
 * $Id$
 */

package org.wikimedia.lsearch;

import java.io.UnsupportedEncodingException;

/**
 * @author Kate Turner
 *
 */
public class Article {
	private String namespace, title, contents, timestamp;
	
	public Article(String dbname, String namespace_, 
			String title_, String contents_, String timestamp_) {
		namespace = namespace_;
		title = title_;
		contents = contents_;
		timestamp = timestamp_;
		if (!Configuration.open().islatin1(dbname)) {
			try {
				title = new String(title.getBytes("ISO-8859-1"), "UTF-8");
				contents = new String(contents.getBytes("ISO-8859-1"), "UTF-8");
			} catch (UnsupportedEncodingException e) {}
		}
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
}
