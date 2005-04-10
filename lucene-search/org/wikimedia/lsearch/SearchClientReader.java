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
import java.net.Socket;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.text.MessageFormat;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Iterator;

import org.apache.lucene.document.Document;
import org.apache.lucene.index.Term;
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
	/** Logger */
	static java.util.logging.Logger log = java.util.logging.Logger.getLogger("SearchClientReader");
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
	int maxlines=1000;
	
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
	
	private SearchState getMyState(String dbname) throws SearchDbException {
		SearchState ret = (SearchState)myStates.get(dbname);
		if (ret != null) {
			return ret;
		}
		
		ret = SearchState.forWiki(dbname);
		myStates.put(dbname, ret);
		return ret;
	}
	
	public void run() {
		//myStates = new HashMap<String, SearchState>();
		myStates = new HashMap();
		log.info("starting handler #" + num);
		for (;;) {
			try {
				client = MWDaemon.sock.accept();
			} catch (IOException e) {
				log.warning("accept() error: " + e.getMessage());
				continue;
			}
			try {
				handle();
				log.fine("request handled.");
			} catch (IOException e) {
				// Probably the client closed the connection after X entries.
				// This is normal, so just be silent.
			} catch (Exception e) {
				// Unexpected error; log it!
				log.warning(e.toString());
			} finally {
				// Make sure the client is closed out, so we
				// don't leave the PHP threads hanging.
				try {  ostrm.flush(); } catch(Exception e) { }
				try {  ostrm.close(); } catch(Exception e) { }
				try {  istrm.close(); } catch(Exception e) { }
				try { client.close(); } catch(Exception e) { }
			}
		}
	}
	
	private void handle() throws IOException, SearchDbException {
		istrm = new BufferedReader(new InputStreamReader(client.getInputStream()));
		ostrm = new BufferedWriter(new OutputStreamWriter(client.getOutputStream()));
		
		/* The protocol format is "database\noperation\nsearchterm"
		 * Search term is urlencoded UTF-8.
		 */
		dbname = readInputLine();
		state = getMyState(dbname);
		
		what = readInputLine();
		log.fine("Request type: " + what);
		
		rawsearchterm = readInputLine();
		searchterm = URLDecoder.decode(rawsearchterm, "UTF-8");
		log.fine("Search term: " + rawsearchterm);
		
		if (what.equals("TITLEMATCH")) {
			doTitleMatches();
		} else if (what.equals("TITLEPREFIX")) {
			doTitlePrefix();
		} else if (what.equals("SEARCH")) {
			doNormalSearch();
		} else {
			log.warning("Unknown request type [" + what + "]; ignoring.");
		}
	}
	
	private void doNormalSearch() throws IOException {
		String encsearchterm = "title:(" + searchterm + ")^4 " + searchterm;
		
		long now = System.currentTimeMillis();
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
				log.warning("Problem parsing search term [" + encsearchterm + "]: " + e2.getMessage() + "\n" + e2.getStackTrace());
				return;
			}
		}
		Hits hits;
		try {
			hits = state.searcher.search(query);
		} catch (Exception e) {
			log.warning("Error searching [" + encsearchterm + "]: " + e.getMessage ()+ "\n" + e.getStackTrace());
			return;
		}
		
		int numhits = hits.length();
		long delta = System.currentTimeMillis() - now;
		logRequest(searchterm, query, numhits, delta);
		sendOutputLine(numhits + "");
		
		//StringBuffer buffer = new StringBuffer("");
		for (int i = 0; i < numhits && i < maxlines; i++) {
			Document doc = hits.doc(i);
			float score = hits.score(i);
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			sendOutputLine(score + " " + namespace + " " + URLEncoder.encode(title.replaceAll(" ", "_"), "UTF-8"));
			//buffer.append(score + " " + namespace + " " + URLEncoder.encode(title.replaceAll(" ", "_"), "UTF-8") + "\n");
		}
		//ostrm.write(buffer.toString());
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
			
			long now = System.currentTimeMillis();
			Query query = state.parser.parse(searchterm);
			Hits hits = state.searcher.search(query);
			
			int numhits = hits.length();
			long delta = System.currentTimeMillis() - now;
			logRequest(searchterm, query, numhits, delta);
			
			for (int i = 0; i < numhits && i < 10; i++) {
				Document doc = hits.doc(i);
				float score = hits.score(i);
				String namespace = doc.get("namespace");
				String title = doc.get("title");
				sendOutputLine(score + " " + namespace + " " + URLEncoder.encode(title.replaceAll(" ", "_"), "UTF-8"));
			}
		} catch (Exception e) {
			log.warning(e.toString());
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
			sendOutputLine("0 " + match.namespace + " " + URLEncoder.encode(match.title.replaceAll(" ", "_"), "UTF-8"));
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
		log.finest(">>>" + out);
		ostrm.write(out + "\n");
	}
	
	String readInputLine() throws IOException {
		String in = istrm.readLine();
		log.finest("<<<" + in);
		return in;
	}
	
	void logRequest(String searchterm, Query query, int numhits, long delta) {
		log.info(MessageFormat.format("{0} {1}: query=[{2}] parsed=[{3}] hit=[{4}] in {5}ms",
			new Object[] { what, dbname, searchterm, query.toString(),
				new Integer(numhits), new Long(delta) }));
	}
}
