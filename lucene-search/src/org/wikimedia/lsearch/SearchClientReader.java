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

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintStream;
import java.net.Socket;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.util.List;

import org.apache.lucene.document.Document;
import org.apache.lucene.index.Term;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.FuzzyTermEnum;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.Query;

/**
 * Represents a single client.  Performs one search, sends the
 * results, then exits.
 * @author Kate Turner
 *
 */
public class SearchClientReader extends Thread {
	/** A socket for our client. */
	Socket client;
	/** The unprocessed search term from the client (urlencoded) */
	String rawsearchterm;
	/** The search term after urlencoding */
	String searchterm;
	/** Client input stream */
	BufferedReader istrm;
	/** Client output stream */
	PrintStream ostrm;
	/** What operation we're performing */
	String what;
	/** Client-supplied database we should operate on */
	String dbname;
	/** Search state for this database */
	SearchState state;
	
	// lucene special chars: + - && || ! ( ) { } [ ] ^ " ~ * ? : \
	static String[] specialChars = {
			"\\+", "-", "&&", "\\|\\|", "!", "\\(", "\\)", "\\{", "\\}", "\\[", "\\]",
			"\\^", "\"", "~", "\\*", "\\?", ":", "\\\\"
			//"\\(", "\\)"
	};

	public SearchClientReader(Socket newclient) {
		this.client = newclient;
	}
	
	public void run() {
		try {
			istrm = new BufferedReader(new InputStreamReader(client.getInputStream()));
			ostrm = new PrintStream(client.getOutputStream(), true, "UTF-8");
			/* The protocol format is "database\noperation\nsearchterm"
			 * Search term is urlencoded.
			 */
			dbname = istrm.readLine();
			state = SearchState.forWiki(dbname);
			if (state == null) {
				istrm.close();
				ostrm.close();
				return;
			}
			what = istrm.readLine();
			rawsearchterm = istrm.readLine();
			searchterm = URLDecoder.decode(rawsearchterm, "UTF-8");
			
			if (what.equals("TITLEMATCH")) {
				doTitleMatches();
			} else if (what.equals("TITLEPREFIX")) {
				doTitlePrefix();
			} else if (what.equals("SEARCH")) {
				doNormalSearch();
			}
		} catch (Exception e) {
			System.out.printf("Unexpected exception: %s\n", e.getMessage());
			e.printStackTrace();
		}
		try {
			istrm.close();
			ostrm.close();
			client.close();
		} catch (IOException e) {}
	}
	
	private void doNormalSearch() throws IOException {
		searchterm = "title:(" + searchterm + ")^4 " + searchterm;
		
		Query query;
		/* If we fail to parse the query, it's probably due to illegal
		 * use of metacharacters, so we escape them all and try again.
		 */
		try {
			query = state.parser.parse(searchterm);
		} catch (ParseException e) {
			String escaped = "";
			for (int i = 0; i < searchterm.length(); ++i)
				escaped += "\\" + searchterm.charAt(i);
			searchterm = "title:(" + escaped + ")^4 " + escaped;
			try {
				query = state.parser.parse(escaped); 
			} catch (ParseException e2) {
				return;
			}
		}
		Hits hits = state.searcher.search(query);
		
		int numhits = hits.length();
		UserInteraction.instance.logQuery(dbname, searchterm, query.toString(), numhits);
		
		ostrm.printf("%d\n", numhits);
		
		int i = 0;
		while (i < numhits) {
			Document doc = hits.doc(i);
			float score = hits.score(i);
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			ostrm.printf("%f %s %s\n", score, namespace, title.replaceAll(" ", "_"));
			++i;
		}
		if (numhits == 0) {
			String spelfix = makeSpelFix(rawsearchterm);
			ostrm.printf("%s\n", URLEncoder.encode(spelfix, "UTF-8"));
		}
	}
	
	void doTitleMatches() {
		String term = searchterm;
		try {
			String terms[] = term.split(" +");
			term = "";
			for (String t : terms)
				term += t + "~ ";
			searchterm = "title:(" + term + ")";
			
			Query query = state.parser.parse(searchterm);
			Hits hits = state.searcher.search(query);
			int numhits = hits.length();
			int i = 0;
			while (i < numhits && i < 10) {
				Document doc = hits.doc(i);
				float score = hits.score(i);
				String namespace = doc.get("namespace");
				String title = doc.get("title");
				ostrm.printf("%f %s %s\n", score, namespace, title.replaceAll(" ", "_"));
				++i;
			}
			ostrm.flush();
		} catch (IOException e) {
		} catch (Exception e) {
			System.out.println("Unexpected exception: " + e.getMessage());
			e.printStackTrace();
		} finally {
			try {
				istrm.close();
				ostrm.close();
			} catch (IOException e) {}
		}
	}

	void doTitlePrefix() {
		if (searchterm.length() < 1)
			return;
		
		List<Title> matches = state.matcher.getMatches(
				TitlePrefixMatcher.stripTitle(searchterm));
		for (Title match : matches) {
			ostrm.printf("0 %d %s\n", match.namespace, 
					match.title.replaceAll(" ", "_"));
		}
	}

	String makeSpelFix(String query) {
		try {
			boolean anysuggest = false;
			String[] terms = query.split(" +");
			String ret = "";
			for (int i = 0; i < terms.length; ++i) {
				String bestmatch = terms[i];
				double bestscore = -1;
				FuzzyTermEnum enums = new FuzzyTermEnum(state.reader, 
						new Term("contents", terms[i]), 0.5f, 1);
				while (enums.next()) {
					Term term = enums.term();
					int score = editDistance(terms[i], term.text(), terms[i].length(),
							term.text().length());
					int weight = state.reader.docFreq(term);
					double fscore = (score*2) * 1/Math.sqrt(weight);
					if (fscore > 4)
						continue;
					if (bestscore < 0 || fscore < bestscore) {
						bestscore = fscore;
						bestmatch = term.text();
						anysuggest = true;
					}
				}
				ret += bestmatch + " ";
			}
			if (!anysuggest) return "";
			return ret;
		} catch (IOException e) {
			return "";
		}
	}
	
	// Taken from the lucene source, distributed under the Apache Software Licinse.
	private static int ed[][] = new int[1][1];

	private static final int min(int a, int b, int c) {
        int t = (a < b) ? a : b;
        return (t < c) ? t : c;
    }

    private static final int editDistance(String s, String t, int n, int m) {
        if (ed.length <= n || ed[0].length <= m) {
            ed = new int[Math.max(ed.length, n+1)][Math.max(ed[0].length, m+1)];
        }
        int d[][] = ed; // matrix
        int i; // iterates through s
        int j; // iterates through t
        char s_i; // ith character of s

        if (n == 0) return m;
        if (m == 0) return n;

        // init matrix d
        for (i = 0; i <= n; i++) d[i][0] = i;
        for (j = 0; j <= m; j++) d[0][j] = j;

        // start computing edit distance
        for (i = 1; i <= n; i++) {
            s_i = s.charAt(i - 1);
            for (j = 1; j <= m; j++) {
                if (s_i != t.charAt(j-1))
                    d[i][j] = min(d[i-1][j], d[i][j-1], d[i-1][j-1])+1;
                else d[i][j] = min(d[i-1][j]+1, d[i][j-1]+1, d[i-1][j-1]);
            }
        }

        // we got the result!
        return d[n][m];
    }
}
