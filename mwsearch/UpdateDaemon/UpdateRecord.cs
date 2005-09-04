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
	
	public abstract class UpdateRecord {
		protected string _database;
		protected Article _article;
		
		public string Database {
			get {
				return _database;
			}
		}
		
		public string Key {
			get {
				return _article.Key;
			}
		}
		
		/**
		 * Updates to run on a lucene IndexReader:
		 * mark any current page record deleted.
		 */
		public void ApplyReads(SearchReader state) {
			state.DeleteArticle(_article);
		}
		
		/**
		 * Updates to run on a lucene IndexWriter.
		 * These are run in a second pass after the reads.
		 */
		public abstract void ApplyWrites(SearchWriter state);
	}
	
	public class PageUpdate : UpdateRecord {
		private KeyValue[] _metadata;
		public PageUpdate(string databaseName, Title title, string text) {
			_database = databaseName;
			_article = new Article(databaseName,
				title.Namespace.ToString(), title.Text,
				text, "bogus timestamp");
		}
		
		public PageUpdate(string databaseName, Title title, string text, KeyValue[] metadata) {
			_database = databaseName;
			_article = new Article(databaseName,
				title.Namespace.ToString(), title.Text,
				text, "bogus timestamp");
			_metadata = metadata;
		}
		
		public override void ApplyWrites(SearchWriter state) {
			state.AddArticle(_article, _metadata);
		}
		
		public override string ToString() {
			return String.Format("update on {0} to {1}", _database, _article);
		}
	}
	
	public class PageDeletion : UpdateRecord {
		public PageDeletion(string databaseName, Title title) {
			_database = databaseName;
			_article = new Article(title.Namespace, title.Text);
		}
		
		public override void ApplyWrites(SearchWriter state) {
			// do nothing
		}
		
		public override string ToString() {
			return String.Format("deletion on {0} to {1}", _database, _article);
		}
	}
}
