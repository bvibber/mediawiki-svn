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
import java.text.MessageFormat;
import java.util.Properties;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;

import com.sleepycat.bind.EntryBinding;
import com.sleepycat.bind.serial.SerialBinding;
import com.sleepycat.bind.serial.StoredClassCatalog;
import com.sleepycat.bind.tuple.StringBinding;
import com.sleepycat.collections.StoredSortedMap;
import com.sleepycat.je.Database;
import com.sleepycat.je.DatabaseConfig;
import com.sleepycat.je.DatabaseEntry;
import com.sleepycat.je.DatabaseException;
import com.sleepycat.je.Environment;
import com.sleepycat.je.EnvironmentConfig;
import com.sleepycat.je.Transaction;

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
	static String configfile = "./mwsearch.conf";
	static String dburl;
	static String dbhost/*, dbname*/;
	private static Connection dbconn;
	private static boolean latin1 = false;
	private static boolean dotitles = true;
	private static boolean dobodies = true;
	
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
			else if (args[i].equals("-notitles"))
				dotitles = false;
			else if (args[i].equals("-nobodies"))
				dobodies = false;
			else if (args[i].equals("-mwversion")) {
				String vers = args[++i];
				if (vers.equals("old"))
					mw_version = MW_OLD;
				else if (vers.equals("new"))
					mw_version = MW_NEW;
				else {
					System.err.println("Unknown MediaWiki version " + vers);
					return;
				}
			}
			else break;
			++i;
		}
		//dbhost = args[i++];
		//dbname = args[i++];
		
		if (what == -1) {
			System.err.println("No action specified");
			return;
		}
		
		System.out.println(
				"MWSearch Lucene search indexer - standalone index rebuilder.\n" +
				"Version 20041225, copyright 2004 Kate Turner.\n");
		System.out.println("Using configuration: " + configfile);
		System.out.println("Indexing: " + 
				(dotitles ? "titles " : "") +
				(dobodies ? "bodies " : ""));
		
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
		dbhost = p.getProperty("mwsearch.database.host");
		String[] dbnames = p.getProperty("mwsearch.databases").split(" ");
		for (i = 0; i < dbnames.length; ++i) {
			System.out.println("Running " + 
					(what == DOING_INCREMENT ? "incremental" : "full")
					+ " update on " + dbnames[i] + "@" + dbhost + "\n");
			System.out.println("Connecting to DB server...");
			try {
				String un = p.getProperty("mwsearch.username"),
				pw = p.getProperty("mwsearch.password");
				String[] urlargs = {dbhost, dbnames[i]};
				MessageFormat form = new MessageFormat(p.getProperty("mwsearch.dburl"));
				dbconn = DriverManager.getConnection(form.format(urlargs), un, pw);
			} catch (SQLException e2) {
				System.err.println("Error: DB connection error: " + e2.getMessage());
				return;
			}
			
			IndexWriter writer;
			try {
				String[] pathargs = {dbnames[i]};
				MessageFormat form = new MessageFormat(p.getProperty("mwsearch.indexpath"));
				writer= new IndexWriter(form.format(pathargs), 
						new EnglishAnalyzer(), true);
			} catch (IOException e4) {
				System.out.println("Error: could not open index path: " + e4.getMessage());
				return;
			}
			
			System.out.println("Opening titles database...");
			String pfxtemp = p.getProperty("mwsearch.titledb");
			String pfxdir;
			String[] pathargs = {dbnames[i]};
			MessageFormat form = new MessageFormat(pfxtemp);
			pfxdir = form.format(pathargs);
			File f = new File(pfxdir);
			if (!f.exists())
				f.mkdir();
			EnvironmentConfig envconfig = new EnvironmentConfig();
			envconfig.setAllowCreate(true);
			envconfig.setTransactional(true);
			Database db = null, miscdb = null;
			StoredClassCatalog catalog = null;
			Environment dbenv;
			StoredSortedMap titles;
			try {
				dbenv = new Environment(new File(pfxdir), envconfig);
				DatabaseConfig dbconfig = new DatabaseConfig();
				dbconfig.setAllowCreate(true);
				dbconfig.setTransactional(true);
				System.out.println("Title prefix database location: " + pfxdir);
				Transaction txn = dbenv.beginTransaction(null, null);
				db = dbenv.openDatabase(txn, "db", dbconfig);
				catalog = new StoredClassCatalog(db);
				EntryBinding keybind = new SerialBinding(catalog, String.class);
				EntryBinding valuebind = new SerialBinding(catalog, Title.class);
				//titles = new StoredSortedMap(db, keybind, valuebind, true);
				miscdb = dbenv.openDatabase(txn, "misc", dbconfig);
				txn.commit();
			} catch (DatabaseException e) {
				System.out.println("Database error: " + e.getMessage());
				return;
			}
			
			System.out.println("Running update...");
			long now = System.currentTimeMillis();
			long numArticles = 0;
			
			String query;
			
			if (mw_version == MW_NEW)
				query = "SELECT page_namespace,page_title,old_text,page_timestamp " +
				"FROM page, text WHERE old_id=page_latest AND page_is_redirect=0";
			else
				query = "SELECT cur_namespace,cur_title,cur_text,cur_timestamp " +
				"FROM cur WHERE cur_is_redirect=0";
			
			PreparedStatement pstmt;
			try {
				Transaction txn = dbenv.beginTransaction(null, null);
				String curTimestamp = "";
				
				// Without this, Lucene takes so long to optimise the index that
				// MySQL will time out the connection.
				pstmt = dbconn.prepareStatement("set net_read_timeout=2000");
				pstmt.executeUpdate();
				pstmt = dbconn.prepareStatement("set net_write_timeout=2000");
				pstmt.executeUpdate();
				
				pstmt = dbconn.prepareStatement(query, ResultSet.TYPE_FORWARD_ONLY,
						ResultSet.CONCUR_READ_ONLY);
				pstmt.setFetchSize(Integer.MIN_VALUE);
				ResultSet rs = pstmt.executeQuery();
				while (rs.next()) {
					String namespace = rs.getString(1);
					String title = rs.getString(2).replaceAll("_", " ");
					String content = rs.getString(3);
					String timestamp = rs.getString(4);
					
					if (timestamp.compareTo(curTimestamp) > 0) {
						DatabaseEntry dbkey = new DatabaseEntry("timestamp".getBytes("UTF-8"));
						DatabaseEntry data = new DatabaseEntry(timestamp.getBytes("UTF-8"));
						Transaction txn2 = dbenv.beginTransaction(null, null);
						db.put(txn2, dbkey, data);
						txn2.commit();
						curTimestamp = timestamp;
					}
					
					if (!latin1) {
						try {
							title = new String(title.getBytes("ISO-8859-1"), "UTF-8");
							content = new String(content.getBytes("ISO-8859-1"), "UTF-8");
						} catch (UnsupportedEncodingException e) {}
					}
					if (dobodies) {
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
					}
					if (dotitles && namespace.equals("0")) {
						try {
							String stripped = TitlePrefixMatcher.stripTitle(title);
							Title t = new Title(Integer.valueOf(namespace).intValue(), title);
							DatabaseEntry dbkey = new DatabaseEntry();
							DatabaseEntry data = new DatabaseEntry();
							StringBinding keybind = new StringBinding();
							SerialBinding binding = new SerialBinding(catalog, Title.class);
							binding.objectToEntry(t, data);
							keybind.objectToEntry(stripped, dbkey);
							db.put(txn, dbkey, data);
							//titles.put(stripped, t);
						} catch (DatabaseException e) {
							System.out.println("Database error caching titles:");
							e.printStackTrace();
						}
					}
					if ((++numArticles % 1000) == 0) {
						System.out.println(numArticles + "...");
						txn.commit();
						txn = dbenv.beginTransaction(null, null);
					}
				}
				writer.close();
				txn.commit();
				db.close();
				miscdb.close();
				dbenv.close();
			} catch (SQLException e) {
				System.out.println("Error: SQL error: " + e.getMessage());
				return;
			} catch (OutOfMemoryError em) {
				em.printStackTrace();
				return;
			} catch (IOException e) {
				System.out.println("Error: closing index: " + e.getMessage());
				return;
			} catch (DatabaseException e) {
				System.out.println("Error: database error: " + e.getMessage());
			}
			double totaltime = (System.currentTimeMillis() - now) / 1000;
			System.out.println("Done, indexed " + numArticles + " articles in "
					+ totaltime + " seconds (" + (numArticles/totaltime) + " articles/sec)");
		}
	}
	
	public static String stripWiki(String text) {
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
