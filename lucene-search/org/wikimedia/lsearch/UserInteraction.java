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

/**
 * @author Kate Turner
 *
 */
public class UserInteraction extends Thread {
	private boolean logQueries, logFailOnly;
	public static UserInteraction instance;
	
	public void logQuery(String dbname, String query, String parsed, int hits) {
		if (!logQueries)
			return;
		if (hits > 0 && logFailOnly)
			return;
		System.out.println(dbname + ": query=[" + query +
				"] parsed=[" + parsed + "] hit=[" + hits + "]");
	}
	
	public void run() {
		instance = this;
		logQueries = logFailOnly = false;
		
		System.out.println("Ready to accept commands...");
		BufferedReader r = new BufferedReader(new InputStreamReader(System.in));
		for (;;) {
			try {
				String[] c = r.readLine().split(" ");
				if (c.length == 0)
					continue;
				if (c[0].equals("h")) {
					System.out.println(
						"Lucene search indexer runtime daemon command line interface.\n" +
						"\n" +
						"Known commands:\n" +
						"\tq           exit search daemon\n" +
						"\tlq [t|f]    Do/don't log search queries\n" +
						"\tlr [t|f]    Do/don't only log queries with no hits\n"
					);
				} else if (c[0].equals("q")) {
					System.exit(1);
				} else if (c[0].equals("lq")) {
					if (c.length < 2) {
						System.out.println("?Need argument.");
					} else {
						if (c[1].equals("t")) {
							logQueries = true;
						} else {
							logQueries = false;
						}
						System.out.println("Will " + (logQueries ? "" : "not ") +
								"log queries.");
					}
				} else if (c[0].equals("lr")) {
					if (c.length < 2) {
						System.out.println("?Need argument.");
					} else {
						if (c[1].equals("t")) {
							logFailOnly = true;
						} else {
							logFailOnly = false;
						}
						System.out.print("Will " + (!logQueries ? "" : "not ") +
							"only log queries with no results.");
					}					
				} else {
					System.out.println("?Unknown command (" + c[0] + ")");
				}
			} catch (IOException e) {
				System.out.println("Read error from stdin...");
				System.exit(1);
			}
		}
	}
}
