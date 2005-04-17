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
import java.io.UnsupportedEncodingException;
import java.net.Socket;
import java.net.URI;
import java.net.URISyntaxException;
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
	int maxoffset=10000;
	boolean headersSent;
	
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
			headersSent = false;
			try {
				handle();
				log.fine("request handled.");
			} catch (IOException e) {
				// Probably the client closed the connection after X entries.
				// This is normal, so just be silent.
			} catch (Exception e) {
				// Unexpected error; log it!
				log.warning(e.toString());
				e.printStackTrace();
			} finally {
				if (!headersSent) {
					try {
						sendHeaders(500, "Internal server error");
					} catch (IOException e) { }
				}
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
		String request = readInputLine();
		if (!request.startsWith("GET ")) {
			sendOutputLine("HTTP/1.x 400 Error");
			log.warning("Bad request: " + request);
			return;
		}
		// Ignore any remaining headers...
		for(String headerline = readInputLine(); !headerline.equals("");)
			headerline = readInputLine();
		
		String[] bits = request.split(" ");
		URI uri = null;
		try {
			uri = new URI(bits[1]);
		} catch (URISyntaxException e) {
			sendHeaders(400, "Bad request");
			log.warning("Bad URI in request: " + bits[1]);
			return;
		}
		
		String[] paths = uri.getPath().split("/", 4);
		what = paths[1];
		dbname = paths[2];
		searchterm = paths[3];
		
		log.info("query:" + bits[1] + " what:"+what+" dbname:"+dbname+" term:"+searchterm);
		Map query = new QueryStringMap(uri);
		
		state = getMyState(dbname);

		if (what.equals("titlematch")) {
			doTitleMatches();
		} else if (what.equals("titleprefix")) {
			doTitlePrefix();
		} else if (what.equals("search")) {
			int startAt = 0, endAt = 100;
			if (query.containsKey("offset"))
				startAt = Math.max(Integer.parseInt((String)query.get("offset")), 0);
			if (query.containsKey("limit"))
				endAt = Math.min(Integer.parseInt((String)query.get("limit")), maxlines);
			NamespaceFilter namespaces = new NamespaceFilter((String)query.get("namespaces"));
			doNormalSearch(startAt, endAt, namespaces);
		} else {
			sendHeaders(404, "Search type not found");
			log.warning("Unknown request type [" + what + "]; ignoring.");
		}
	}
	
	private void sendHeaders(int code, String message) throws IOException {
		sendOutputLine("HTTP/1.1 " + code + " " + message);
		sendOutputLine("Content-Type: text/plain");
		sendOutputLine("");
		headersSent = true;
	}
	
	private void doNormalSearch(int offset, int limit, NamespaceFilter namespaces) throws IOException {
		String encsearchterm = "title:(" + searchterm + ")^4 " + searchterm;
		
		long now = System.currentTimeMillis();
		Query query;
		/* If we fail to parse the query, it's probably due to illegal
		 * use of metacharacters, so we escape them all and try again.
		 */
		synchronized (state.parser) {
			try {
				query = state.parser.parse(encsearchterm);
			} catch (Exception e) {
				//String escaped = "";
				//for (int i = 0; i < searchterm.length(); ++i)
				//	escaped += "\\" + searchterm.charAt(i);
				String escaped = searchterm;
				for (int i = 0; i < specialChars.length; ++i)
					escaped = escaped.replaceAll( "/(" + specialChars[i] + ")/", "\\\\1" );
				encsearchterm = "title:(" + escaped + ")^4 " + escaped;
				try {
					query = state.parser.parse(encsearchterm); 
				} catch (Exception e2) {
					log.warning("Problem parsing search term query=[" + searchterm + "] parsed=[" + encsearchterm + "]: " + e2.getMessage() + "\n" + e2.getStackTrace());
					return;
				}
			}
		}
		Hits hits;
		try {
			hits = state.searcher.search(query);
		} catch (Exception e) {
			log.warning("Error searching [" + encsearchterm + "]: " + e.getMessage ()+ "\n" + e.getStackTrace());
			return;
		}
		
		sendHeaders(200, "OK");
		
		int numhits = hits.length();
		long delta = System.currentTimeMillis() - now;
		logRequest(searchterm, query, numhits, delta);
		sendOutputLine(Integer.toString(numhits));
		
		if (numhits == 0) {
			String spelfix = makeSpelFix(searchterm);
			sendOutputLine(URLEncoder.encode(spelfix, "UTF-8"));
		} else {
			// Lucene's filters seem to want to run over the entire
			// document set, which is really slow. We'll do namespace
			// checks as we go along, and stop once we've seen enough.
			//
			// The good side is that we can return the first N documents
			// pretty quickly. The bad side is that the total hits
			// number we return is bogus: it's for all namespaces combined.
			int matches = 0;
			for (int i = 0; i < numhits && i < maxoffset; i++) {
				Document doc = hits.doc(i);
				String namespace = doc.get("namespace");
				if (namespaces.filter(namespace)) {
					if (matches++ < offset)
						continue;
					String title = doc.get("title");
					float score = hits.score(i);
					sendResultLine(score, namespace, title);
					if (matches >= (limit + offset))
						break;
				}
			}
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
			Query query;
			synchronized(state.parser) {
				query = state.parser.parse(searchterm);
			}
			Hits hits = state.searcher.search(query);
			
			int numhits = hits.length();
			long delta = System.currentTimeMillis() - now;
			logRequest(searchterm, query, numhits, delta);
			
			sendHeaders(200, "OK");
			for (int i = 0; i < numhits && i < 10; i++) {
				Document doc = hits.doc(i);
				float score = hits.score(i);
				String namespace = doc.get("namespace");
				String title = doc.get("title");
				sendResultLine(score, namespace, title);
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
		
		sendHeaders(200, "OK");
		//for (Title match : matches) {
		for (Iterator iter = matches.iterator(); iter.hasNext();) {
			Title match = (Title)iter.next();
			sendResultLine(0.0f, Integer.toString(match.namespace), match.title);
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
	
	/**
	 * @param score
	 * @param namespace
	 * @param title
	 * @throws IOException
	 * @throws UnsupportedEncodingException
	 */
	private void sendResultLine(float score, String namespace, String title) throws UnsupportedEncodingException, IOException {
		sendOutputLine(score + " " + namespace + " " +
			URLEncoder.encode(title.replaceAll(" ", "_"), "UTF-8"));
	}
}
