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
		
		/** A socket for our client. */
		TcpClient client;
		/** The unprocessed search term from the client (urlencoded) */
		string rawsearchterm;
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
		
		// lucene special chars: + - && || ! ( ) { } [ ] ^ " ~ * ? : \.
		static string[] specialChars = {
				"\\+", "-", "&&", "\\|\\|", "!", "\\(", "\\)", "\\{", "\\}", "\\[", "\\]",
				"\\^", "\"", "~", "\\*", "\\?", ":", "\\\\"
				//"\\(", "\\)"
		};

		public Worker(TcpClient newclient) {
			this.client = newclient;
		}
		public Worker(int i) {
			num = i;
		}
		int num;
		
		public void Run() {
			Console.WriteLine("wtf" + num);
			log.Info("starting handler #" + num);
			//using (log4net.NDC.Push(client.Client.RemoteEndPoint)) {
			for (;;) {
				client = Daemon.NextClient();
				using (log4net.NDC.Push("thread" + num)) {
					log.Debug("Thread " + num + " accepted a client");
					try {
						Handle();
						log.Debug("request handled.");
					} catch (IOException e) {
						log.Error("net error: " + e.Message);
					} catch (Exception e) {
						log.Error(e);
					} finally {
						// Make sure the client is closed out.
						try {  ostrm.Close(); } catch { }
						try {  istrm.Close(); } catch { }
						try { client.Close(); } catch { }
					}
				}
			}
		}
		
		private void Handle() {
			istrm = new StreamReader(client.GetStream());
			ostrm = new StreamWriter(client.GetStream());
			/* The protocol format is "database\noperation\nsearchterm"
			 * Search term is urlencoded.
			 */
			dbname = ReadInputLine();
			state = SearchState.ForWiki(dbname);
			if (state == null) {
				/*
				istrm.Close();
				ostrm.Flush();
				ostrm.Close();
				client.Close();
				*/
				// ?????
				log.Error("What the hell? Null searchstate");
				--Daemon.numthreads;
				return;
			}
			what = ReadInputLine();
			log.Debug("Request type: " + what);
			
			rawsearchterm = ReadInputLine();
			searchterm = HttpUtility.UrlDecode(rawsearchterm, utf8);
			log.Debug("Search term: " + rawsearchterm);
			
			if (what.Equals("TITLEMATCH")) {
				DoTitleMatches();
			} else if (what.Equals("TITLEPREFIX")) {
				DoTitlePrefix();
			} else if (what.Equals("SEARCH")) {
				DoNormalSearch();
			} else {
				log.ErrorFormat("Unknown request type [{1}]; ignoring.", rawsearchterm);
			}
			
		}
		
		private void DoNormalSearch() {
			string encsearchterm = "title:(" + searchterm + ")^4 " + searchterm;
			
			Query query;
			/* If we fail to parse the query, it's probably due to illegal
			 * use of metacharacters, so we escape them all and try again.
			 */
			try {
				//query = state.Parser.Parse(encsearchterm);
				query = QueryParser.Parse(encsearchterm, "contents", new EnglishAnalyzer());
			} catch (Exception e) {
				string escaped = "";
				for (int i = 0; i < searchterm.Length; ++i)
					escaped += "\\" + searchterm[i];
				encsearchterm = "title:(" + escaped + ")^4 " + escaped;
				try {
					//query = state.Parser.Parse(encsearchterm); 
					query = QueryParser.Parse(encsearchterm, "contents", new EnglishAnalyzer());
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
			
			int numhits = hits.Length();
			log.InfoFormat("SEARCH {0}: query=[{1}] parsed=[{2}] hit=[{3}]",
				dbname, searchterm, query.ToString(), numhits);
			
			SendOutputLine(numhits + "");
			
			for (int i = 0; i < numhits && i < maxlines; i++) {
				Document doc = hits.Doc(i);
				float score = hits.Score(i);
				string pageNamespace = doc.Get("namespace");
				string title = doc.Get("title");
				SendTitleLine(score, pageNamespace, title);
			}
			if (numhits == 0) {
				string spelfix = MakeSpelFix(rawsearchterm);
				SendOutputLine(HttpUtility.UrlEncode(spelfix, utf8));
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
				
				//Query query = state.Parser.Parse(searchterm);
				Query query = QueryParser.Parse(searchterm, "contents", new EnglishAnalyzer());
				Hits hits = state.Searcher.Search(query);
				int numhits = hits.Length();
				log.InfoFormat("TITLEMATCH {0}: query=[{1}] parsed=[{2}] hit=[{3}]",
					dbname, searchterm, query.ToString(), numhits);
				for (int i = 0; i < numhits && i < 10; i++) {
					Document doc = hits.Doc(i);
					float score = hits.Score(i);
					string pageNamespace = doc.Get("namespace");
					string title = doc.Get("title");
					SendTitleLine(score, pageNamespace, title);
				}
				ostrm.Flush();
			} catch (Exception e) {
				log.Error(e.Message + e.StackTrace);
			}
		}

		void DoTitlePrefix() {
			if (searchterm.Length < 1)
				return;
			PrefixMatcher matcher = PrefixMatcher.ForWiki(dbname);
			foreach (Article match in matcher.GetMatches(searchterm)) {
				SendTitleLine(0.0f, match.Namespace, match.Title);
			}
		}
		
		string MakeSpelFix(String query) {
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
	 	
	 	private void SendTitleLine(float score, string pageNamespace, string title) {
			SendOutputLine(score + " " + pageNamespace + " " +
				HttpUtility.UrlEncode(title.Replace(" ", "_"), utf8));
	 	}
	 	   
	    private string ReadInputLine() {
			string sin = istrm.ReadLine();
			log.Debug("<<<" + sin);
			return sin;
	    }
	}
}
