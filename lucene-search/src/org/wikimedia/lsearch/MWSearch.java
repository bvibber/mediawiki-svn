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

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Properties;

import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;

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
	static String configfile = "./mwsearch.conf";
	static String dburl;
	private static Connection dbconn;
	private static boolean latin1 = false;
	
	public static void main(String[] args) {
		
		if (args.length < 1) {
			System.err.println("Must specify database name");
			return;
		}
		
		int i = 0;
		while (i < args.length - 1) {
			if (args[i].equals("-rebuild"))
				what = DOING_FULL_UPDATE;
			else if (args[i].equals("-increment"))
				what = DOING_INCREMENT;
			else if (args[i].equals("-configfile"))
				configfile = args[++i];
			else if (args[i].equals("-latin1"))
				latin1 = true;
			++i;
		}
		dburl = args[i];
		
		if (what == -1) {
			System.err.println("No action specified");
			return;
		}
		
		System.out.println("MWSearch Lucene search updater.");
		System.out.println("Using configuration: " + configfile);
		System.out.println("Running " + 
			(what == DOING_INCREMENT ? "incremental" : "full")
			+ " update on " + dburl + "\n");
		
		Properties p = new Properties();
		try {
			p.load(new FileInputStream(new File(configfile)));
		} catch (FileNotFoundException e3) {
			System.err.println("Error: config file " + configfile + " not found");
			return;
		} catch (IOException e3) {
			System.err.println("Error: IO error reading config: " + e3.getMessage());
			return;
		}
		System.out.println("Connecting to DB server...");
		try {
			dbconn = DriverManager.getConnection(dburl,
					p.getProperty("mwsearch.username"),
					p.getProperty("mwsearch.password"));
		} catch (SQLException e2) {
			System.err.println("Error: DB connection error: " + e2.getMessage());
			return;
		}

		IndexWriter writer;
		try {
			writer= new IndexWriter(p.getProperty("mwsearch.indexpath"), 
					new StandardAnalyzer(), true);
		} catch (IOException e4) {
			System.out.println("Error: could not open index path: " + e4.getMessage());
			return;
		}

		System.out.println("Running update...");
		long now = System.currentTimeMillis();
		long numArticles = 0;
		
		String query = "SELECT page_namespace,page_title,old_text " +
			"FROM page, text WHERE old_id=page_latest AND page_is_redirect=0";
		PreparedStatement pstmt;
		try {
			pstmt = dbconn.prepareStatement(query);
			ResultSet rs = pstmt.executeQuery();
			while (rs.next()) {
				String namespace = rs.getString(1);
				String title = rs.getString(2).replaceAll("_", " ");
				String content = rs.getString(3);
				if (!latin1) {
					try {
						title = new String(title.getBytes("ISO-8859-1"), "UTF-8");
						content = new String(content.getBytes("ISO-8859-1"), "UTF-8");
					} catch (UnsupportedEncodingException e) {}
				}
				if (title.equals("Post-it")) {
					System.out.println("namespace="+namespace+" title="+title+
							"content=["+content+"]");
				}
				Document d = new Document();
				d.add(Field.Text("namespace", namespace));
				d.add(Field.Text("title", title));
				d.add(new Field("contents", stripWiki(content), false, true, true));
				try {
					writer.addDocument(d);
				} catch (IOException e5) {
					System.out.println("Error adding document [" + namespace
							+ ":" + title + "]: " + e5.getMessage());
					return;
				}
				if ((++numArticles % 1000) == 0) {
					System.out.println(numArticles + "...");
				}
			}
			writer.close();
		} catch (SQLException e) {
			System.out.println("Error: SQL error: " + e.getMessage());
			return;
		} catch (OutOfMemoryError em) {
			em.printStackTrace();
			return;
		} catch (IOException e) {
			System.out.println("Error: closing index: " + e.getMessage());
			return;
		}
		double totaltime = (System.currentTimeMillis() - now) / 1000;
		System.out.println("Done, indexed " + numArticles + " articles in "
				+ totaltime + " seconds");
	}
	
	private static String stripWiki(String text) {
		int i = 0, j, k;
		i = text.indexOf("[[Image:");
		if (i == -1) i = text.indexOf("[[image:");
		int l = i;
		while (i > -1) {
			j = text.indexOf("[[", i + 2);
			k = text.indexOf("]]", i + 2);
			if (j != -1 && j < k && k > -1) {
				i = k;
				continue;
			} else {
				if (k == -1)
					text = text.substring(0, l);
				else
					text = text.substring(0, l) + 
						text.substring(k + 2);
				i = text.indexOf("[[Image:");
				if (i == -1) i = text.indexOf("[[image:");		
				l = i;
			}
		}

		while ((i = text.indexOf("<!--")) != -1) {
			if ((j = text.indexOf("-->", i)) == -1)
				break;
			if (j + 4 >= text.length())
				text = text.substring(0, i);
			else
				text = text.substring(0, i) + text.substring(j + 4);
		}
		text = text.replaceAll("\\{\\|(.*?)\\|\\}", "")
			.replaceAll("\\[\\[[A-Za-z_-]+:([^|]+?)\\]\\]", "")
			.replaceAll("\\[\\[([^|]+?)\\]\\]", "$1")
			.replaceAll("\\[\\[([^|]+\\|)(.*?)\\]\\]", "$2")
			.replaceAll("(^|\n):*''[^'].*\n", "")
			.replaceAll("^----.*", "")
			.replaceAll("'''''", "")
			.replaceAll("('''|</?[bB]>)", "")
			.replaceAll("''", "")
			.replaceAll("</?[uU]>", "");
		return text;
	}
}
