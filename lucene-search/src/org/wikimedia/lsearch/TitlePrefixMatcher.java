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
import java.sql.SQLException;
import java.text.MessageFormat;
import java.util.ArrayList;
import java.util.List;
import com.sleepycat.bind.EntryBinding;
import com.sleepycat.bind.serial.SerialBinding;
import com.sleepycat.bind.serial.StoredClassCatalog;
import com.sleepycat.bind.tuple.StringBinding;
import com.sleepycat.je.Cursor;
import com.sleepycat.je.Database;
import com.sleepycat.je.DatabaseConfig;
import com.sleepycat.je.DatabaseEntry;
import com.sleepycat.je.DatabaseException;
import com.sleepycat.je.Environment;
import com.sleepycat.je.EnvironmentConfig;
import com.sleepycat.je.LockMode;
import com.sleepycat.je.OperationStatus;
import com.sleepycat.je.Transaction;

/**
 * @author Kate Turner
 *
 */
public class TitlePrefixMatcher {
	protected Environment			dbenv;
	public Database				db, miscdb;
	protected StoredClassCatalog	catalog;
	EntryBinding keybind, valuebind;
	private Configuration config;
	private DatabaseConnection dbconn;

	public TitlePrefixMatcher(String dbname) throws SQLException {
		config = Configuration.open();
		
		try {
			this.dbconn = DatabaseConnection.forWiki(dbname);
		} catch (SQLException e) {
			System.err.println(dbname + ": error: " + e.getMessage());
			throw e;
		}
		try {
			String pfxdir;
			pfxdir = MessageFormat.format(config.getString("mwsearch.titledb"),
					new Object[] { dbname });
			File f = new File(pfxdir);
			if (!f.exists())
				f.mkdirs();
			
			EnvironmentConfig envconfig = new EnvironmentConfig();
			envconfig.setAllowCreate(true);
			envconfig.setTransactional(true);
			dbenv = new Environment(new File(pfxdir), envconfig);
			DatabaseConfig dbconfig = new DatabaseConfig();
			dbconfig.setAllowCreate(true);
			dbconfig.setTransactional(true);
			Transaction txn = dbenv.beginTransaction(null, null);
			System.out.println(dbname + ": title prefix database location: " +
					pfxdir);
			db = dbenv.openDatabase(txn, "db", dbconfig);
			miscdb = dbenv.openDatabase(txn, "miscdb", dbconfig);
			catalog = new StoredClassCatalog(db);
			keybind = new SerialBinding(catalog, String.class);
			valuebind = new SerialBinding(catalog, Title.class);
			txn.commit();
		} catch (DatabaseException e) {
			System.out.println(dbname + ": error: database exception: " +
					e.getMessage());
			System.exit(1);
		}
		System.out.println(dbname + ": opened title database okay");
	}
	
	//public List<Title> getMatches(String prefix) {
	public List getMatches(String prefix) {
		String first = prefix;
		String last;
		if (prefix.length() >= 2) {
			last = prefix.substring(0, prefix.length() - 1)
				+ Character.toString(
						(char)(prefix.charAt(prefix.length() - 1) + 1));
		} else {
			last = Character.toString((char)(prefix.charAt(0) + 1));
		}
		//List<Title> l = new ArrayList<Title>();
		List l = new ArrayList();
		int i = 0;
		try {
			Cursor c = db.openCursor(null, null);
			DatabaseEntry dbkey = new DatabaseEntry();
			DatabaseEntry data = new DatabaseEntry();
			StringBinding titlekeybind = new StringBinding();
			SerialBinding binding = new SerialBinding(catalog, Title.class);
			titlekeybind.objectToEntry(first, dbkey);
			if (c.getSearchKeyRange(dbkey, data, LockMode.DEFAULT) != OperationStatus.SUCCESS)
				return l;
			while (i++ < 20) {
				String rettitle = (String) titlekeybind.entryToObject(dbkey);
				if (rettitle.compareTo(last) >= 0)
					break;
				Title t = (Title) binding.entryToObject(data);
				l.add(t);
				if (c.getNext(dbkey, data, LockMode.DEFAULT) != OperationStatus.SUCCESS)
					break;
			}
			c.close();
		} catch (DatabaseException e) {
			e.printStackTrace();
			return l;
		}
		return l;
	}
	
	public static String stripTitle(String title) {
		title = title.toLowerCase();
		String res = "";
		for (int i = 0; i < title.length(); ++i) {
			char c = title.charAt(i);
			if (Character.isLetterOrDigit(c)) {
				res += Character.toLowerCase(c);
			}
		}
		return res;
	}
	
	public void addArticle(Article article) throws DatabaseException {
		Transaction txn = dbenv.beginTransaction(null, null);
		String stripped = TitlePrefixMatcher.stripTitle(article.getTitle());
		Title t = new Title(Integer.valueOf(article.getNamespace()).intValue(), article.getTitle());
		DatabaseEntry dbkey = new DatabaseEntry();
		DatabaseEntry data = new DatabaseEntry();
		StringBinding tkeybind = new StringBinding();
		SerialBinding binding = new SerialBinding(catalog, Title.class);
		binding.objectToEntry(t, data);
		tkeybind.objectToEntry(stripped, dbkey);
		db.put(txn, dbkey, data);
		txn.commit();
	}
	protected void finalize() {
		dbconn.close();
	}
}
