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
	using Lucene.Net.Documents;
	using Lucene.Net.Index;
	using Lucene.Net.QueryParsers;
	using Lucene.Net.Search;
	
	using System.IO;
	
	public class SearchReader : SearchState {
		Searcher searcher = null;
		IndexReader reader = null;
		
		public const bool CreateIfNew = true;

		public Searcher Searcher {
			get {
				return searcher;
			}
		}
		
		public IndexReader Reader {
			get {
				return reader;
			}
		}
		
		public SearchReader(string dbname) {
			Init(dbname);
			OpenReader();
		}

		public SearchReader(string dbname, bool createIfNew) {
			Init(dbname);
			try {
				OpenReader();
			} catch (IOException e) {
				log.Error(dbname + ": warning: open for read failed" + e);
				if (createIfNew) {
					InitializeIndex();
					OpenReader();
				} else {
					throw e;
				}
			}
		}

		public void Close() {
			try {
				if (reader != null)
					CloseReader();
			} catch (IOException e) {
				log.Error(mydbname + ": warning: closing index: " + e.Message);
			}
		}
		
		public void Reopen() {
			try {
				Close();
				OpenReader();
			} catch (IOException e) {
				log.Error("Exception reopening " + mydbname + ": " + e);
			}
		}
		
		/**
		 * @throws IOException
		 */
		private void OpenReader() {
			if (reader == null) {
				log.Info("Opening reader for " + mydbname);
				reader = IndexReader.Open(indexpath);
			}
			if (searcher == null) {
				log.Info("Opening searcher for " + mydbname);
				searcher = new IndexSearcher(reader);
			}
		}

		/**
		 * @throws IOException
		 */
		private void CloseReader() {
			if (searcher != null) {
				log.Info("Closing searcher for " + mydbname);
				searcher.Close();
				searcher = null;
			}
			if (reader != null) {
				log.Info("Closing reader for " + mydbname);
				reader.Close();
				reader = null;
			}
		}
		
		/**
		 * Lucene doesn't let us do deletes from an IndexWriter, only an IndexReader.
		 * To avoid locking or constant open-close switches, be sure that writing
		 * is only happening on an in-memory writer.
		 * @param article
		 * @throws IOException
		 */
		public void DeleteArticle(Article article) {
			OpenReader();
			string key = article.Key;
			Hits hits = searcher.Search(new TermQuery(
					new Term("key", key)));
			if (hits.Length() == 0)
				log.Debug("Nothing to delete for " + key);
			for (int i = 0; i < hits.Length(); i++) {
				int id = hits.Id(i);
				log.Debug("Trying to delete article number " + id + " for " + key);
				try {
					reader.Delete(id);
				} catch (IOException e) {
					log.Warn("Couldn't delete article number " + 
						id + " for " + key + "... " + e);
				}
			}
		}
		
		public Query Parse(string term) {
			QueryParser parser = new QueryParser("contents", analyzer);
			parser.SetOperator(QueryParser.DEFAULT_OPERATOR_AND);
			return parser.Parse(term);
		}
		
	}
}
