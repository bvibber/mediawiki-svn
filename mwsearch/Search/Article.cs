/*
 * Copyright 2004 Kate Turner
 * Ported to C# by Brion Vibber, April 2005
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

namespace MediaWiki.Search {
	/**
	 * @author Kate Turner
	 *
	 */
	public class Article {
		private string pageNamespace, title, contents, timestamp;
		
		public Article(string dbname, string namespace_, 
				string title_, string contents_, string timestamp_) {
			pageNamespace = namespace_;
			title = title_;
			contents = contents_;
			timestamp = timestamp_;
		}
		
		public Article(int namespace_, string title_) {
			pageNamespace = namespace_.ToString();
			title = title_;
			contents = null;
			timestamp = null;
		}
		
		/**
		 * @return Returns the contents.
		 */
		public string Contents {
			get {
				return contents;
			}
		}
		
		/**
		 * @return Returns the namespace.
		 */
		public string Namespace {
			get {
				return pageNamespace;
			}
		}
		
		/**
		 * @return Returns the timestamp.
		 */
		public string Timestamp {
			get {
				return timestamp;
			}
		}
		
		/**
		 * @return Returns the title.
		 */
		public string Title {
			get {
				return title;
			}
		}
		
		/**
		 * Articles need to be easily identifiable by an exact match
		 * for replacement of updated items when updating an existing
		 * search index.
		 * 
		 * @return Returns unique id.
		 */
		public string Key {
			get {
				return Namespace + ":" + Title;
			}
		}
	}
}

