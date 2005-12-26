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
	using Lucene.Net.Analysis.Standard;
	using Lucene.Net.Documents;
	using Lucene.Net.Index;
	using Lucene.Net.Search;

	/**
	 * @author Kate Turner
	 *
	 */
	public class SearchState {
		//private static Stack<SearchState> states;
		//private static Map<String, SearchState> openWikis;
		private static IDictionary openWikis;

		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);

		static SearchState() {
			//openWikis = new Hashtable<string, SearchState>();
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
		
		public static void ResetStates() {
			lock (openWikis) {
				foreach (SearchState state in openWikis) {
					state.Reopen();
				}
			}
		}

		private string mydbname;
		Searcher searcher = null;
		Analyzer analyzer = null;
		IndexReader reader = null;
		string indexpath;
		Configuration config;
		IndexWriter writer;
		bool writable = false;
		
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
			indexpath = string.Format(config.GetString("mwsearch","indexpath"),
					dbname );
			if (!File.Exists(indexpath))
				Directory.CreateDirectory(indexpath);
			
			log.Debug(dbname + ": opening state");
			analyzer = new EnglishAnalyzer();
			try {
				reader = IndexReader.Open(indexpath);
				searcher = new IndexSearcher(reader);
			} catch (IOException e) {
				log.Error(dbname + ": warning: open for read failed");
			}
			mydbname = dbname;
		}
		
		private void Close() {
			try {
				searcher.Close();
				reader.Close();
			} catch (IOException e) {
				log.Error(mydbname + ": warning: closing index: " + e.Message);
			}
		}
		
		private void Reopen() {
			try {
				searcher.Close();
				reader.Close();
				reader = IndexReader.Open(indexpath);
				searcher = new IndexSearcher(reader);
			} catch (IOException e) {}
		}
		
		private void OpenForWrite() {
			if (writable)
				return;
			writer = new IndexWriter(
					string.Format(config.GetString("mwsearch", "indexpath"),
							mydbname ),
					new EnglishAnalyzer(), true);
			writable = true;
		}
		
		public void AddArticle(Article article) {
			OpenForWrite();
			Document d = new Document();
			d.Add(Field.Text("namespace", article.Namespace));
			d.Add(Field.Text("title", article.Title));
			d.Add(new Field("contents", StripWiki(article.Contents), 
					false, true, true));
			writer.AddDocument(d);
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
	}
}
