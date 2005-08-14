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
	using Lucene.Net.Search;
	
	using System.IO;
	using System.Text.RegularExpressions;
	
	public class SearchWriter : SearchState {
		IndexWriter writer;
		
		public SearchWriter(string dbname) {
			Init(dbname);
			OpenForWrite();
		}

		public void Close() {
			try {
				if (writer != null) {
					log.Info("Closing writer for " + mydbname);
					writer.Close();
					writer = null;
				}
			} catch (IOException e) {
				log.Error(mydbname + ": warning: closing index: " + e.Message);
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
			if (writer != null)
				return;
			
			log.Info("Opening index writer for " + mydbname);
			writer = new IndexWriter(indexpath, analyzer, false);
		}
		
		public void AddArticle(Article article) {
			AddArticle(article, null);
		}
		
		public void AddArticle(Article article, KeyValue[] metadata) {
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
			
			if (metadata != null) {
				foreach(KeyValue pair in metadata) {
					d.Add(new Field("metadata." + pair.Key, pair.Value, false, true, true));
				}
			}
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
	
		
		public void Optimize() {
			OpenForWrite();
			writer.Optimize();
		}
	}
}
