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

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.queryParser.QueryParser;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searcher;

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
	
	static {
		try {
			searcher = new IndexSearcher(MWDaemon.indexPath);
			analyzer = new StandardAnalyzer();			
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
			searchterm = URLDecoder.decode(rawsearchterm);
			System.out.println("Query: " + searchterm);
			Query query = QueryParser.parse(searchterm, "contents", analyzer);
	        Hits hits = searcher.search(query);
	        int numhits = hits.length();
	        ostrm.write(numhits + "\n");
	        int i = 0;
	        while (i < numhits) {
	        	Document doc = hits.doc(i);
	        	String namespace = doc.get("namespace");
	        	String title = doc.get("title");
	        	ostrm.write(namespace + " " + title + "\n");
	        	++i;
	        }
			istrm.close();
			ostrm.close();
		} catch (Exception e) {
		}
	}
}
