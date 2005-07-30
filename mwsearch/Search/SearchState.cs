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

	using System;
	using System.Collections;
	using System.IO;
	using System.Text.RegularExpressions;

	using Lucene.Net.Analysis;
	using Lucene.Net.Analysis.DE;
	using Lucene.Net.Analysis.RU;
	using Lucene.Net.Analysis.Standard;
	using Lucene.Net.Documents;
	using Lucene.Net.Index;
	using Lucene.Net.QueryParsers;
	using Lucene.Net.Search;
	using Lucene.Net.Store;

	/**
	 * @author Kate Turner
	 *
	 */
	public class SearchState {
		private static IDictionary openWikis;

		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);

		static SearchState() {
			openWikis = new Hashtable();
		}
		
		public static SearchState ForWiki(string dbname) {
			log.Debug("lookup " + dbname);
			SearchState t = null;
			lock (openWikis) {
				log.Debug("got lock");
				t = (SearchState)openWikis[dbname];
				if (t != null)
					return t;
				t = new SearchState(dbname);
				openWikis[dbname] = t;
				log.Debug("got " + dbname);
				return t;
			}
		}

		public static bool StateOpen(string state) {
			return openWikis[state] != null;
		}
		
		public static int ResetStates() {
			int statesReset = 0;
			lock (openWikis) {
				foreach (SearchState state in openWikis.Values) {
					state.Reopen();
					statesReset++;
				}
			}
			return statesReset;
		}

		private string mydbname;
		Searcher searcher = null;
		Analyzer analyzer = null;
		IndexReader reader = null;
		string indexpath;
		Configuration config;
		
		IndexWriter writer;
		bool writable = false;
		bool _errorOnOpen;
		
		// An in-memory directory which we'll write updates into.
		// private Lucene.Net.Store.Directory buffer;
		// private int updatesWritten;
		
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
		
		private SearchState(string dbname) {
			config = Configuration.Open();
			indexpath = string.Format(config.GetIndexString("indexpath"),
					dbname );
			if (!File.Exists(indexpath))
				System.IO.Directory.CreateDirectory(indexpath);
			
			log.Debug(dbname + ": opening state");
			analyzer = GetAnalyzerForLanguage(config.GetLanguage(dbname));
			log.Info(dbname + " using analyzer " + analyzer.GetType().FullName);
			try {
				OpenReader();
				_errorOnOpen = false;
			} catch (IOException e) {
				log.Error(dbname + ": warning: open for read failed" + e);
				_errorOnOpen = true;
			}
			mydbname = dbname;
		}

		/**
		 * @param language
		 * @return
		 */
		private Analyzer GetAnalyzerForLanguage(string language) {
			if (language.Equals("de"))
				return new GermanAnalyzer();
			if (language.Equals("eo"))
				return new EsperantoAnalyzer();
			if (language.Equals("ru"))
				return new RussianAnalyzer();
			return new EnglishAnalyzer();
		}
		
		public void Close() {
			try {
				if (writable)
					CloseWriter();
				if (reader != null)
					CloseReader();
			} catch (IOException e) {
				log.Error(mydbname + ": warning: closing index: " + e.Message);
			}
		}
		
		/**
		 * @throws IOException
		 *
		 */
		/*
		private void MergeWrites() {
			writer.Close();
			writable = false;
			
			log.Info("Merging " + updatesWritten + " updates to disk on " + mydbname);
			try {
				// Need to flush any pending deletions on the reader...
				CloseReader();
				
				// Merge updates from RAM to disk...
				IndexWriter ondisk = new IndexWriter(indexpath, analyzer, false);
				ondisk.AddIndexes(new Lucene.Net.Store.Directory[] { buffer });
				ondisk.Close();
			} finally {
				updatesWritten = 0;
				OpenReader();
				buffer.Close();
				
				// A new in-RAM buffer will be created on demand.
			}
		}
		
		private void CountOrMerge() {
			updatesWritten++;
			if (updatesWritten >= 1000)
				MergeWrites();
		}
		*/

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
			if (writable)
				CloseWriter();
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
		
		private void CloseWriter() {
			if (writable) {
				log.Info("Closing writer for " + mydbname);
				writer.Close();
				writer = null;
				writable = false;
			}
		}

		/**
		 * Open the index for writing if it's not already. This is implicitly
		 * called when addDocument() is used, so callers don't need to do it.
		 * 
		 * We won't actually write to the main index yet; we're actually opening
		 * an in-memory directory which we'll buffer updates to, and merge them
		 * in chunks to disk.
		 * 
		 * @throws IOException
		 */
		private void OpenForWrite() {
			if (writable)
				return;
			//buffer = new RAMDirectory();
			//writer = new IndexWriter(buffer, analyzer, true);
			
			Reopen();
			
			log.Info("Opening index writer for " + mydbname);
			writer = new IndexWriter(indexpath, analyzer, false);
			writable = true;
			//updatesWritten = 0;
		}
		
		/**
		 * Create a fresh new index.
		 * Any existing database will be overwritten.
		 * @throws IOException
		 */
		public void InitializeIndex() {
			log.Info("Creating new index for " + mydbname);
			new IndexWriter(indexpath, analyzer, true).Close();
			OpenReader();
		}
		
		/**
		 * Create a fresh new index if and only if the index doesn't exist yet.
		 * @throws IOException
		 */
		public void InitializeIfNew() {
			if (_errorOnOpen) {
				InitializeIndex();
				_errorOnOpen = false;
			}
		}
		
		public void AddArticle(Article article) {
			OpenForWrite();
			Document d = new Document();
			// This will be used to look up and replace entries on index updates.
			d.Add(Field.Keyword("key", article.Key));
			
			// These fields are returned with results
			d.Add(Field.Text("namespace", article.Namespace));
			d.Add(Field.Text("title", article.Title));
			
			// Bulk contents are indexed only, not stored.
			// Clients can pull up-to-date text from the source database for hit matching.
			d.Add(new Field("contents", StripWiki(article.Contents), 
					false, true, true));
			writer.AddDocument(d);
			
			// CountOrMerge();
		}
		
		/**
		 * When altering an existing index, we have to manually remove the previous
		 * version of an article when we update it, or results will include both
		 * old and new versions.
		 * 
		 * @param article
		 * @throws IOException
		 * @throws SearchDbException
		 */
		public void ReplaceArticle(Article article) {
			// WARNING: THIS WILL SUCK IF USED
			DeleteArticle(article);
			AddArticle(article);
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
			String key = article.Key;
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
		
		private static string StripWiki(string text) {
			int i = 0, j, k;
			i = text.IndexOf("[[Image:");
			if (i == -1) i = text.IndexOf("[[image:");
			int l = i;
			while (i > -1) {
				j = text.IndexOf("[[", i + 2);
				k = text.IndexOf("]]", i + 2);
				if (j != -1 && j < k && k > -1) {
					i = k;
					continue;
				}
				if (k == -1)
					text = text.Substring(0, l);
				else
					text = text.Substring(0, l) + 
					text.Substring(k + 2);
				i = text.IndexOf("[[Image:");
				if (i == -1) i = text.IndexOf("[[image:");		
				l = i;
			}

			while ((i = text.IndexOf("<!--")) != -1) {
				if ((j = text.IndexOf("-->", i)) == -1)
					break;
				if (j + 4 >= text.Length)
					text = text.Substring(0, i);
				else
					text = text.Substring(0, i) + text.Substring(j + 4);
			}
			text = Regex.Replace(text, "\\{\\|(.*?)\\|\\}", "");
			text = Regex.Replace(text, "\\[\\[[A-Za-z_-]+:([^|]+?)\\]\\]", "");
			text = Regex.Replace(text, "\\[\\[([^|]+?)\\]\\]", "$1");
			text = Regex.Replace(text, "\\[\\[([^|]+\\|)(.*?)\\]\\]", "$2");
			text = Regex.Replace(text, "(^|\n):*''[^'].*\n", "");
			text = Regex.Replace(text, "^----.*", "");
			text = Regex.Replace(text, "'''''", "");
			text = Regex.Replace(text, "('''|</?[bB]>)", "");
			text = Regex.Replace(text, "''", "");
			text = Regex.Replace(text, "</?[uU]>", "");
			return text;
		}
	
		public Query Parse(string term) {
			return QueryParser.Parse(term, "contents", analyzer);
		}
		
		public void Optimize() {
			OpenForWrite();
			writer.Optimize();
		}
	}
}
