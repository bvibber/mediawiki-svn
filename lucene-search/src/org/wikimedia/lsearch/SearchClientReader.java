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

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.queryParser.QueryParser;
import org.apache.lucene.search.FuzzyTermEnum;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.TermQuery;

/**
 * @author Kate Turner
 *
 */
public class SearchClientReader extends Thread {
	Socket client;
	String rawsearchterm;
	String searchterm;
	BufferedReader istrm;
	BufferedWriter ostrm;
	
	static Searcher searcher = null;
	static Analyzer analyzer = null;
	static QueryParser parser = null;
	static IndexReader reader = null;
	
	// lucene special chars: + - && || ! ( ) { } [ ] ^ " ~ * ? : \
	static String[] specialChars = {
			"\\+", "-", "&&", "\\|\\|", "!", "\\(", "\\)", "\\{", "\\}", "\\[", "\\]",
			"\\^", "\"", "~", "\\*", "\\?", ":", "\\\\"
			//"\\(", "\\)"
	};

	static {
		try {
			analyzer = new StandardAnalyzer();
			parser = new QueryParser("contents", analyzer);
			reader = IndexReader.open(MWDaemon.indexPath);
			searcher = new IndexSearcher(reader);
		} catch (IOException e) {
			System.err.println("Could not initialise search reader: " 
					+ e.getMessage());
			System.exit(0);
		}
	}
	
	public SearchClientReader(Socket newclient) {
		this.client = newclient;
	}
	
	public void run() {
		try {
			istrm = new BufferedReader(new InputStreamReader(client.getInputStream()));
			ostrm = new BufferedWriter(new OutputStreamWriter(client.getOutputStream()));
			rawsearchterm = istrm.readLine();
			rawsearchterm = URLDecoder.decode(rawsearchterm);
			for (int i = 0; i < specialChars.length; ++i)
				rawsearchterm = rawsearchterm.replaceAll(specialChars[i], 
						"\\" + specialChars[i]);
			searchterm = "title:(" + rawsearchterm + ")^4 OR contents:(" 
				+ rawsearchterm + ")";
			
			System.out.println("Query: " + searchterm);
			Query query = parser.parse(searchterm);
			System.out.println("Parsed: [" + query.toString() + "]");
	        Hits hits = searcher.search(query);
	        int numhits = hits.length();
	        System.out.println(numhits + " hits");
	        ostrm.write(numhits + "\n");
	        int i = 0;
	        while (i < numhits) {
	        	Document doc = hits.doc(i);
	        	float score = hits.score(i);
	        	String namespace = doc.get("namespace");
	        	String title = doc.get("title");
	        	ostrm.write(score + " " + namespace + " " + title + "\n");
	        	++i;
	        }
	        if (numhits == 0) {
	        	String spelfix = makeSpelFix(rawsearchterm);
	        	ostrm.write(URLEncoder.encode(spelfix) + "\n");
	        }
	        ostrm.flush();
		} catch (IOException e) {
		} catch (Exception e) {
			System.out.println("Unexpected exception: " + e.getMessage());
		} finally {
			try {
				istrm.close();
				ostrm.flush();
				ostrm.close();
			} catch (IOException e) {}
		}
	}
	
	String makeSpelFix(String query) {
		try {
			boolean anysuggest = false;
			String[] terms = query.split(" +");
			String ret = "";
			System.out.println("spelcheck: [" + query + "]");
			for (int i = 0; i < terms.length; ++i) {
				System.out.println("trying [" + terms[i] + "]");
				String bestmatch = terms[i];
				int bestscore = 0;
				FuzzyTermEnum enum = new FuzzyTermEnum(reader, 
						new Term("contents", terms[i]), 0.71f, 3);
				while (enum.next()) {
					Term term = enum.term();
					Query q = new TermQuery(term);
					Hits h = searcher.search(q);
					System.out.println("match: ["+term.text()+"] score " + h.length());
					if (h.length() > bestscore) {
						bestscore = h.length();
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
}
