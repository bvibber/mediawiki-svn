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

import java.io.IOException;
import java.sql.SQLException;
import java.util.Iterator;

import com.sleepycat.je.DatabaseException;

/**
 * @author Kate Turner
 *
 */
public class MWSearch {
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
			System.err.println("Must specify database name");
			return;
		}
		
		for (int i = 0; i < args.length - 1;) {
			if (args[i].equals("-rebuild"))
				what = DOING_FULL_UPDATE;
			else if (args[i].equals("-increment"))
				what = DOING_INCREMENT;
			else if (args[i].equals("-configfile"))
				Configuration.setConfigFile(args[++i]);
			else break;
			++i;
		}

		config = Configuration.open();
		if (what == -1) {
			System.err.println("No action specified");
			return;
		}
		
		System.out.println(
				"MWSearch Lucene search indexer - standalone index rebuilder.\n" +
				"Version 20050103, copyright 2004 Kate Turner.\n");
		String[] dbnames = config.getArray("mwsearch.databases");
		//for (String dbname: dbnames) {
		for (int i = 0; i < dbnames.length; i++) {
			String dbname = dbnames[i];
			SearchState state;
			try {
				state = SearchState.forWiki(dbname);
			} catch (SQLException e) {
				System.out.println("Error connecting to database: " + e.getMessage());
				return;
			}
			System.out.println(dbname + ": running " +
					(what == DOING_INCREMENT ? "incremental" : "full") + " update...");
			
			long now = System.currentTimeMillis();
			long numArticles = 0;

			try {
				ArticleList articles = state.enumerateArticles();
				//for (Article article: articles) {
				for (Iterator iter = articles.iterator(); iter.hasNext();) {
					Article article = (Article)iter.next();
					state.addArticle(article);
					if ((++numArticles % 1000) == 0) {
						System.out.print(numArticles + "...\r");
						System.out.flush();
					}
				}
			} catch (SQLException e) {
				System.out.println("Error: SQL error: " + e.getMessage());
				return;
			} catch (IOException e) {
				System.out.println("Error: IO error: " + e.getMessage());
			} catch (DatabaseException e) {
				System.out.println("Error: database error: " + e.getMessage());
			}
			double totaltime = (System.currentTimeMillis() - now) / 1000;
			//System.out.printf("%s: indexed %d articles in %f seconds (%.2f articles/sec)\n",
			//		dbname, numArticles, totaltime, numArticles/totaltime);
			System.out.println(dbname + ": indexed " + numArticles + " articles in " +
					totaltime + " seconds (" + (numArticles / totaltime) + " articles/sec)");
		}
	}
	
}
