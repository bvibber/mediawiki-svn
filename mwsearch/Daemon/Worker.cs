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

namespace MediaWiki.Search.Daemon {
	using System;
	using System.IO;
	using System.Collections;
	using System.Net;
	using System.Net.Sockets;
	using System.Text;
	using System.Text.RegularExpressions;
	using System.Web;

	using Lucene.Net.Analysis;
	using Lucene.Net.Documents;
	using Lucene.Net.Index;
	using Lucene.Net.QueryParsers;
	using Lucene.Net.Search;
	
	using MediaWiki.Search.Prefix;

	/**
	 * Represents a single client.  Performs one search, sends the
	 * results, then exits.
	 * @author Kate Turner
	 *
	 */
	public class Worker {
		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		private static readonly Encoding utf8 = new UTF8Encoding();
		

		/** The search term after urlencoding */
		string searchterm;
		/** Client input stream */
		StreamReader istrm;
		/** Client output stream */
		StreamWriter ostrm;
		/** What operation we're performing */
		string what;
		/** Client-supplied database we should operate on */
		string dbname;
		/** Lucene search state for this database */
		SearchState state;
		
		int maxlines = 1000;
		int maxoffset = 10000;
		bool headersSent;
		
		Configuration config;
		
		// lucene special chars: + - && || ! ( ) { } [ ] ^ " ~ * ? : \.
		/*
		static string[] specialChars = {
				"\\+", "-", "&&", "\\|\\|", "!", "\\(", "\\)", "\\{", "\\}", "\\[", "\\]",
				"\\^", "\"", "~", "\\*", "\\?", ":", "\\\\"
				//"\\(", "\\)"
		};
		*/

		public Worker(Stream stream, Configuration config) {
			istrm = new StreamReader(stream);
			ostrm = new StreamWriter(stream);
			this.config = config;
		}
		
		public void Run(object par) {
			Run();
		}
		
		public void Run() {
			//using (log4net.NDC.Push(client.Client.RemoteEndPoint)) {
			headersSent = false;
			try {
				Handle();
				log.Debug("request handled.");
			} catch (IOException e) {
				log.Error("net error: " + e.Message);
			} catch (Exception e) {
				log.Error(e);
			} finally {
				if (!headersSent) {
					try {
						SendHeaders(500, "Internal server error");
					} catch (IOException e) { }
				}
				// Make sure the client is closed out.
				try {  ostrm.Close(); } catch { }
				try {  istrm.Close(); } catch { }
			}
		}
		
		private void Handle() {
			/* Simple HTTP protocol; accepts GET requests only.
			 * URL path format is /operation/database/searchterm
			 * The path should be URL-encoded UTF-8 (standard IRI).
			 * 
			 * Additional paramters may be specified in a query string:
			 *   namespaces: comma-separated list of namespace numeric keys to subset results
			 *   limit: maximum number of results to return
			 *   offset: number of matches to skip before returning results
			 * 
			 * Content-type is text/plain and results are listed.
			 */
			string request = ReadInputLine();
			if (!request.StartsWith("GET ")) {
				SendOutputLine("HTTP/1.x 400 Error");
				log.Warn("Bad request: " + request);
				return;
			}
			// Ignore any remaining headers...
			for (string headerline = ReadInputLine(); !headerline.Equals("");)
				headerline = ReadInputLine();
			
			string[] bits = request.Split(' ');
			Uri uri = null;
			try {
				uri = new Uri("http://localhost:8123" + bits[1]);
			} catch (UriFormatException e) {
				SendHeaders(400, "Bad request");
				log.Warn("Bad URI in request: " + bits[1]);
				return;
			}
			
			string[] paths = uri.AbsolutePath.Split( new char[] { '/' }, 4);
			if (paths.Length != 4) {
				SendHeaders(404, "Not found");
				log.Warn("Unknown request " + uri.AbsolutePath);
				return;
			}
			what = paths[1];
			dbname = paths[2];
			searchterm = HttpUtility.UrlDecode(paths[3], Encoding.UTF8);
			
			log.Info("query:" + bits[1] + " what:"+what+" dbname:"+dbname+" term:"+searchterm);
			IDictionary query = new QueryStringMap(uri);
			
			state = SearchState.ForWiki(dbname);

			if (what.Equals("titlematch")) {
				DoTitleMatches();
			} else if (what.Equals("titleprefix")) {
				DoTitlePrefix();
			} else if (what.Equals("search")) {
				int startAt = 0, endAt = 100;
				if (query.Contains("offset"))
					startAt = Math.Max(Int32.Parse((string)query["offset"]), 0);
				if (query.Contains("limit"))
					endAt = Math.Min(Int32.Parse((string)query["limit"]), maxlines);
				NamespaceFilter namespaces = new NamespaceFilter((string)query["namespaces"]);
				DoNormalSearch(startAt, endAt, namespaces);
			} else if (what.Equals("quit")) {
				// TEMP HACK for profiling
				System.Environment.Exit(0);
			} else {
				SendHeaders(404, "Search type not found");
				log.Warn("Unknown request type [" + what + "]; ignoring.");
			}
		}
		
		private void SendHeaders(int code, string message) {
			SendOutputLine("HTTP/1.1 " + code + " " + message);
			SendOutputLine("Content-Type: text/plain");
			SendOutputLine("");
			headersSent = true;
		}
		
		private void DoNormalSearch(int offset, int limit, NamespaceFilter namespaces) {
			string encsearchterm = "title:(" + searchterm + ")^4 " + searchterm;
			
			DateTime now = DateTime.UtcNow;
			Query query;
			/* If we fail to parse the query, it's probably due to illegal
			 * use of metacharacters, so we escape them all and try again.
			 */
			try {
				query = state.Parse(encsearchterm);
			} catch (Exception e) {
				string escaped = "";
				for (int i = 0; i < searchterm.Length; ++i)
					escaped += "\\" + searchterm[i];
				encsearchterm = "title:(" + escaped + ")^4 " + escaped;
				try {
					query = state.Parse(encsearchterm); 
				} catch (Exception e2) {
					log.Error("Problem parsing search term: " + e2.Message + "\n" + e2.StackTrace);
					return;
				}
			}
			Hits hits = null;
			
			try {
				hits = state.Searcher.Search(query);
			} catch (Exception e) {
				log.Error("Error searching: " + e.Message + "\n" + e.StackTrace);
				return;
			}
			
			SendHeaders(200, "OK");
						
			int numhits = hits.Length();
			TimeSpan delta = DateTime.UtcNow - now;
			LogRequest(searchterm, query, numhits, delta);
			
			SendOutputLine(numhits + "");
			
			if (numhits == 0) {
				string spelfix = MakeSpelFix(searchterm);
				SendOutputLine(HttpUtility.UrlEncode(spelfix, utf8));
			} else {
				// Lucene's filters seem to want to run over the entire
				// document set, which is really slow. We'll do namespace
				// checks as we go along, and stop once we've seen enough.
				//
				// The good side is that we can return the first N documents
				// pretty quickly. The bad side is that the total hits
				// number we return is bogus: it's for all namespaces combined.
				int matches = 0;
				string lastMatch = "";
				for (int i = 0; i < numhits && i < maxoffset; i++) {
					Document doc = hits.Doc(i);
					string pageNamespace = doc.Get("namespace");
					if (namespaces.filter(pageNamespace)) {
						if (matches++ < offset)
							continue;
						string title = doc.Get("title");
						string squish=pageNamespace+":"+title;
						if (lastMatch.Equals(squish)) {
							// skip duplicate results due to indexing bugs
							maxoffset++;
							matches--;
							continue;
						}
						lastMatch = squish;
						float score = hits.Score(i);
						SendResultLine(score, pageNamespace, title);
						if (matches >= (limit + offset))
							break;
					}
				}
			}

		}
		
		void DoTitleMatches() {
			string term = searchterm;
			try {
				string[] terms = Regex.Split(term, " +");
				term = "";
				foreach (string t in terms) {
					term += t + "~ ";
				}
				searchterm = "title:(" + term + ")";
				
				DateTime now = DateTime.UtcNow;
				Query query = state.Parse(searchterm);
				Hits hits = state.Searcher.Search(query);
				
				int numhits = hits.Length();
				TimeSpan delta = DateTime.UtcNow - now;
				LogRequest(searchterm, query, numhits, delta);
				
				SendHeaders(200, "OK");
				for (int i = 0; i < numhits && i < 10; i++) {
					Document doc = hits.Doc(i);
					float score = hits.Score(i);
					string pageNamespace = doc.Get("namespace");
					string title = doc.Get("title");
					SendResultLine(score, pageNamespace, title);
				}
			} catch (Exception e) {
				log.Error(e.Message + e.StackTrace);
			}
		}

		void DoTitlePrefix() {
			if (searchterm.Length < 1)
				return;
			PrefixMatcher matcher = PrefixMatcher.ForWiki(dbname);
			SendHeaders(200, "OK");
			foreach (Article match in matcher.GetMatches(searchterm)) {
				SendResultLine(0.0f, match.Namespace, match.Title);
			}
		}
		
		string MakeSpelFix(string query) {
			if (!config.GetBoolean("Daemon", "spelfix")) {
				return "";
			}
			try {
				bool anysuggest = false;
				string[] terms = Regex.Split(query, " +");
				string ret = "";
				for (int i = 0; i < terms.Length; ++i) {
					string bestmatch = terms[i];
					double bestscore = -1;
					FuzzyTermEnum enums = new FuzzyTermEnum(state.Reader, 
							new Term("contents", terms[i]), 0.5f, 1);
					while (enums.Next()) {
						Term term = enums.Term();
						int score = EditDistance(terms[i], term.Text(), terms[i].Length,
								term.Text().Length);
						int weight = state.Reader.DocFreq(term);
						double fscore = (score*2) * 1/Math.Sqrt(weight);
						if (fscore > 4)
							continue;
						if (bestscore < 0 || fscore < bestscore) {
							bestscore = fscore;
							bestmatch = term.Text();
							anysuggest = true;
						}
					}
					ret += bestmatch + " ";
				}
				if (!anysuggest) return "";
				return ret;
			} catch (Exception e) {
				log.Error(e);
				return "";
			}
		}
		

		// Taken from the lucene source, distributed under the Apache Software Licinse.
		private static int[,] ed = new int[1,1];

		private static int Min(int a, int b, int c) {
	        int t = (a < b) ? a : b;
	        return (t < c) ? t : c;
	    }

	    private static int EditDistance(string s, string t, int n, int m) {
	        if (ed.GetLength(0) <= n || ed.GetLength(1) <= m) {
	            //ed = new int[Math.Max(ed.Length, n+1)][Math.Max(ed[0].Length, m+1)];
	            ed = new int[Math.Max(ed.GetLength(0), n+1), Math.Max(ed.GetLength(1), m+1)];
	        }
	        int[,] d = ed; // matrix
	        int i; // iterates through s
	        int j; // iterates through t
	        char s_i; // ith character of s

	        if (n == 0) return m;
	        if (m == 0) return n;

	        // init matrix d
	        for (i = 0; i <= n; i++) d[i,0] = i;
	        for (j = 0; j <= m; j++) d[0,j] = j;

	        // start computing edit distance
	        for (i = 1; i <= n; i++) {
	            s_i = s[i - 1];
	            for (j = 1; j <= m; j++) {
	                if (s_i != t[j-1])
	                    d[i,j] = Min(d[i-1,j], d[i,j-1], d[i-1,j-1])+1;
	                else d[i,j] = Min(d[i-1,j]+1, d[i,j-1]+1, d[i-1,j-1]);
	            }
	        }

	        // we got the result!
	        return d[n,m];
	    }
	    
	    private void SendOutputLine(string sout) {
	        log.Debug(">>>" + sout);
	        ostrm.WriteLine(sout);
		}
	 	
	    private string ReadInputLine() {
			string sin = istrm.ReadLine();
			log.Debug("<<<" + sin);
			return sin;
	    }
	
		void LogRequest(String searchterm, Query query, int numhits, TimeSpan delta) {
			log.InfoFormat("{0} {1}: query=[{2}] parsed=[{3}] hit=[{4}] in {5}ms",
				what, dbname, searchterm, query.ToString(), numhits, delta.Milliseconds);
		}
		
		/**
		 * @param score
		 * @param namespace
		 * @param title
		 * @throws IOException
		 * @throws UnsupportedEncodingException
		 */
		private void SendResultLine(float score, string pageNamespace, string title) {
			SendOutputLine(score + " " + pageNamespace + " " +
				HttpUtility.UrlEncode(title.Replace(" ", "_"), Encoding.UTF8));
		}

	}
}
