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
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.PrintStream;
import java.net.Socket;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Iterator;

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
	BufferedWriter ostrm;
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
	public SearchClientReader(int i) {
		num = i;
	}
	int num;
	//Map<String, SearchState> myStates;
	Map myStates;
	private SearchState getMyState(String dbname) {
		SearchState ret = (SearchState)myStates.get(dbname);
		if (ret != null)
			return ret;
		try {
			ret = SearchState.forWiki(dbname);
		} catch (SQLException e) {
			// Couldn't load state?
			// We'll crap out later on the NULL.
		}
		myStates.put(dbname, ret);
		return ret;
	}
	
	public void run() {
		//myStates = new HashMap<String, SearchState>();
		myStates = new HashMap();
		System.out.println("starting handler #" + num);
		for (;;) {
			try {
				client = MWDaemon.sock.accept();
			} catch (IOException e) {
				System.out.println("accept() error: " + e.getMessage());
				continue;
			}
			try {
				handle();
				System.out.println("request handled.");
			} catch (IOException e) {
				try {
					istrm.close();
					ostrm.flush();
					ostrm.close();
				} catch (IOException e2) {
					e.printStackTrace();
				}
			} catch (Exception e) {
				System.out.println("Unexpected exception: " + e.getMessage());
				e.printStackTrace();
			}
		}
	}
	
	private void handle() throws IOException {
		istrm = new BufferedReader(new InputStreamReader(client.getInputStream()));
		ostrm = new BufferedWriter(new OutputStreamWriter(client.getOutputStream()));
		/* The protocol format is "database\noperation\nsearchterm"
		 * Search term is urlencoded.
		 */
		dbname = readInputLine();
		state = getMyState(dbname);
		if (state == null) {
			istrm.close();
			ostrm.flush();
			ostrm.close();
			client.close();
			--MWDaemon.numthreads;
			return;
		}
		what = readInputLine();
		System.out.println("Request type: " + what);
		rawsearchterm = readInputLine();
		searchterm = URLDecoder.decode(rawsearchterm, "UTF-8");
		System.out.println("Search term: " + rawsearchterm);
		
		if (what.equals("TITLEMATCH")) {
			doTitleMatches();
		} else if (what.equals("TITLEPREFIX")) {
			doTitlePrefix();
		} else if (what.equals("SEARCH")) {
			doNormalSearch();
		} else {
			System.out.println("Unknown request type; ignoring.");
		}
		
		try {
			ostrm.flush();
			ostrm.close();
			istrm.close();
		} catch (IOException e) {
			// Silent...
		}
	}
	
	private void doNormalSearch() throws IOException {
		String encsearchterm = "title:(" + searchterm + ")^4 " + searchterm;
		
		Query query;
		/* If we fail to parse the query, it's probably due to illegal
		 * use of metacharacters, so we escape them all and try again.
		 */
		try {
			query = state.parser.parse(encsearchterm);
		} catch (Exception e) {
			String escaped = "";
			for (int i = 0; i < searchterm.length(); ++i)
				escaped += "\\" + searchterm.charAt(i);
			encsearchterm = "title:(" + escaped + ")^4 " + escaped;
			try {
				query = state.parser.parse(encsearchterm); 
			} catch (Exception e2) {
				return;
			}
		}
		Hits hits;
		
		try {
			hits = state.searcher.search(query);
		} catch (Exception e) {
			return;
		}
		
		int numhits = hits.length();
		UserInteraction.instance.logQuery(dbname, searchterm, query.toString(), numhits);
		
		sendOutputLine(numhits + "");
		
		for (int i = 0; i < numhits; i++) {
			Document doc = hits.doc(i);
			float score = hits.score(i);
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			sendOutputLine(score + " " + namespace + " " + title.replaceAll(" ", "_"));
		}
		if (numhits == 0) {
			String spelfix = makeSpelFix(rawsearchterm);
			sendOutputLine(URLEncoder.encode(spelfix, "UTF-8"));
		}
	}
	
	void doTitleMatches() {
		String term = searchterm;
		try {
			String terms[] = term.split(" +");
			term = "";
			//for (String t : terms)
			for (int i = 0; i < terms.length; i++) {
				String t = terms[i];
				term += t + "~ ";
			}
			searchterm = "title:(" + term + ")";
			
			Query query = state.parser.parse(searchterm);
			Hits hits = state.searcher.search(query);
			int numhits = hits.length();
			for (int i = 0; i < numhits && i < 10; i++) {
				Document doc = hits.doc(i);
				float score = hits.score(i);
				String namespace = doc.get("namespace");
				String title = doc.get("title");
				sendOutputLine(score + " " + namespace + " " + title.replaceAll(" ", "_"));
			}
			ostrm.flush();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (Exception e) {
			System.out.println("Unexpected exception: " + e.getMessage());
			e.printStackTrace();
		} finally {
			try {
				istrm.close();
				ostrm.flush();
				ostrm.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
	}

	void doTitlePrefix() throws IOException {
		if (searchterm.length() < 1)
			return;
		
		//List<Title> matches = state.matcher.getMatches(
		List matches = state.matcher.getMatches(
				TitlePrefixMatcher.stripTitle(searchterm));
		//for (Title match : matches) {
		for (Iterator iter = matches.iterator(); iter.hasNext();) {
			Title match = (Title)iter.next();
			sendOutputLine("0 " + match.namespace + " " + match.title.replaceAll(" ", "_"));
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
    
    void sendOutputLine(String out) throws IOException {
        System.out.println(">>>" + out);
        ostrm.write(out + "\n");
    	}
    
    String readInputLine() throws IOException {
    		String in = istrm.readLine();
    		System.out.println("<<<" + in);
    		return in;
    }
}
