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

import java.sql.SQLException;
import java.util.Iterator;
import java.util.ArrayList;

/**
 * @author Kate Turner
 *
 */
public class MWSearch {
	static java.util.logging.Logger log = java.util.logging.Logger.getLogger("MWSearch");
	static final int
		 DOING_FULL_UPDATE	= 1
		,DOING_INCREMENT 	= 2
		;
	static int what = -1;
	static final int
		 MW_NEW = 1
		,MW_OLD = 2;
	static int mw_version = MW_NEW;
	static Configuration config;
	
	public static void main(String[] args) {
		if (args.length < 1) {
			log.severe("Must specify an action!");
			return;
		}
		
		ArrayList dbnames = new ArrayList();
		
		for (int i = 0; i < args.length;) {
			if (args[i].equals("-rebuild")) {
				what = DOING_FULL_UPDATE;
			} else if (args[i].equals("-increment")) {
				what = DOING_INCREMENT;
			} else if (args[i].equals("-configfile")) {
				Configuration.setConfigFile(args[++i]);
			} else {
				// hope it's a database name
				dbnames.add(args[i]);
				//break;
			}
			++i;
		}

		config = Configuration.open();
		if (what == -1) {
			log.severe("No action specified");
			return;
		}
		
		System.out.println(
				"MWSearch Lucene search indexer - standalone index rebuilder.\n" +
				"Version 20050416, copyright 2004 Kate Turner.\n");
		if (dbnames.isEmpty()) {
			System.out.println("No databases specified; processing all.");
			//dbnames = config.getArray("mwsearch.databases");
			String[] foo = config.getArray("mwsearch.databases");
			for(int i = 0; i < foo.length; i++)
				dbnames.add(foo[i]);
		}
		
		//for (String dbname: dbnames) {
		for (int i = 0; i < dbnames.size(); i++) {
			String dbname = (String)dbnames.get(i);
			SearchState state;
			try {
				state = SearchState.forWiki(dbname);
			} catch (SearchDbException e) {
				log.severe("Error opening search index: " + e.toString());
				return;
			}
			System.out.println(dbname + ": running " +
					(what == DOING_INCREMENT ? "incremental" : "full") + " update...");
			
			long now = System.currentTimeMillis();
			long numArticles = 0;
			int startAt = 0;
			for (boolean done = false; !done; ) {
				try {
					ArticleList articles = state.enumerateArticles(startAt);
					try {
						//for (Article article: articles) {
						for (Iterator iter = articles.iterator(); iter.hasNext();) {
							Article article = (Article)iter.next();
							state.addArticle(article);
							if ((++numArticles % 1000) == 0) {
								System.out.print("[" + dbname + "] " + numArticles + "...\r");
								System.out.flush();
							}
						}
						done = true;
					} catch (Exception e) {
						// wtf!
						log.warning("[" + dbname + "] Error: " + e.toString());
						if(startAt == articles.getLastId()) {
							// Couldn't pick up where we left off? Leave for now...
							log.severe("[" + dbname + "] Aborting!");
							done = true;
						} else {
							startAt = articles.getLastId();
							log.warning("[" + dbname + "] Trying to pick up from page id " + startAt);
						}
					}
				} catch (SQLException e) {
					log.warning("[" + dbname + "] Error starting query: SQL error: " + e.toString());
					done = true;
					return;
				}
			}
			double totaltime = (System.currentTimeMillis() - now) / 1000;
			//System.out.printf("%s: indexed %d articles in %f seconds (%.2f articles/sec)\n",
			//		dbname, numArticles, totaltime, numArticles/totaltime);
			System.out.println(dbname + ": indexed " + numArticles + " articles in " +
					totaltime + " seconds (" + (numArticles / totaltime) + " articles/sec)");
		}
	}
	
}
