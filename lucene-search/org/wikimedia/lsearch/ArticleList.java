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
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Iterator;
import java.util.NoSuchElementException;

/**
 * @author Kate Turner
 *
 */
//public class ArticleList implements Iterable {
public class ArticleList {
	/** Logger */
	static java.util.logging.Logger log = java.util.logging.Logger.getLogger("ArticleIterator");
	
	private ResultSet rs;
	private String dbname;
	private int lastId;
	
	private class ArticleIterator implements Iterator {
		boolean atend;
		boolean latin1;
		
		public ArticleIterator() {
			latin1 = Configuration.open().islatin1(dbname);
			try {
				atend = !rs.next();
			} catch (SQLException e) {
				log.warning(e.toString());
			}
		}
		
		public boolean hasNext() {
			return !atend;
		}

		public Object next() {
			try {
				if (atend)
					throw new NoSuchElementException();
				lastId           = rs.getInt(1);
				String namespace = rs.getString(2);
				String title     = rs.getString(3);
				String contents  = rs.getString(4);
				String timestamp = rs.getString(5);
				if (!latin1) {
					// Nasty hack; MySQL talks to us in Latin-1 so we have to
					// re-convert it to UTF-8. This may change in future.
					try {
						title = new String(title.getBytes("ISO-8859-1"), "UTF-8");
						contents = new String(contents.getBytes("ISO-8859-1"), "UTF-8");
					} catch (UnsupportedEncodingException e) {
						log.warning(e.toString());
					}
				}
				atend = !rs.next();
				return new Article(dbname, namespace, title, contents, timestamp);
			} catch (SQLException e) {
				log.warning(e.toString());
				try {
					// We have to close out the result set
					// before we try to pick up again...
					log.warning("Closing...");
					rs.close();
				} catch(SQLException e2) {}
				return null;
			}
		}

		public void remove() {
			throw new UnsupportedOperationException();
		}
	}

	public ArticleList(String dbname_, ResultSet rs_) {
		rs = rs_;
		dbname = dbname_;
		lastId = 0;
	}
	
	public ArticleIterator iterator() {
		return new ArticleIterator();
	}
	
	public int getLastId() {
		return lastId;
	}
}
