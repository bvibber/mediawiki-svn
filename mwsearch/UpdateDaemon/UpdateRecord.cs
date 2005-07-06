/*
 * Copyright 2005 Brion Vibber
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

namespace MediaWiki.Search.UpdateDaemon {
	using System;
	using MediaWiki.Search;
	
	public struct Title {
		public int Namespace;
		public string Text;
	}
	
	public interface UpdateRecord {
		string Database {
			get;
		}
		void Apply(SearchState state);
	}
	
	public class PageUpdate : UpdateRecord {
		string _database;
		Article _article;
		
		public PageUpdate(string databaseName, Title title, string text) {
			_database = databaseName;
			_article = new Article(databaseName,
				title.Namespace.ToString(), title.Text,
				text, "bogus timestamp");
		}
		
		public string Database { get { return _database; } }
		
		public void Apply(SearchState state) {
			state.ReplaceArticle(_article);
		}
		
		public override string ToString() {
			return "update on " + _database + " to " + _article;
		}
	}
	
	public class PageDeletion : UpdateRecord {
		string _database;
		Article _article;
		
		public PageDeletion(string databaseName, Title title) {
			_database = databaseName;
			_article = new Article(title.Namespace, title.Text);
		}
		
		public string Database { get { return _database; } }
		
		public void Apply(SearchState state) {
			state.DeleteArticle(_article);
		}
		
		public override string ToString() {
			return "deletion on " + _database + " to " + _article;
		}
	}
}
