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
import java.io.UnsupportedEncodingException;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.MessageFormat;
import java.util.HashMap;
import java.util.Iterator;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.search.IndexSearcher;

import com.sleepycat.bind.serial.SerialBinding;
import com.sleepycat.bind.tuple.StringBinding;
import com.sleepycat.je.DatabaseEntry;
import com.sleepycat.je.DatabaseException;
import com.sleepycat.je.LockMode;
import com.sleepycat.je.OperationStatus;
import com.sleepycat.je.Transaction;

/**
 * @author Kate Turner
 *
 */
public class SearchIndexUpdater extends Thread {
	static private final int DEFAULT_DELAY = 10; // seconds
	private int delay;
	IndexWriter writer;
	private HashMap curTimestamps;
	
	SearchIndexUpdater() {
		this(DEFAULT_DELAY);
	}
	SearchIndexUpdater(int delay_) {
		delay = delay_;
	}
	public final void run() {
		curTimestamps = new HashMap();
		System.out.println("Search index updater starting [delay="+delay+"]...");
		for (;;) {
			try {
				Thread.sleep(delay * 1000);
			} catch (Exception e) {}

			for (int i = 0; i < MWDaemon.dbnames.length; ++i) {
				try {
					String indexpath;
					String[] urlargs = {MWDaemon.dbnames[i]};
					MessageFormat form = new MessageFormat(MWDaemon.indexPath);
					indexpath = form.format(urlargs);
					writer = new IndexWriter(indexpath, 
							new EnglishAnalyzer(), false);
				} catch (IOException e) {
					System.out.println("Error: opening writer: " + e.getMessage());
				}
				String curTimestamp = (String) curTimestamps.get(MWDaemon.dbnames[i]);
				if (curTimestamp == null) {
					try {
						PreparedStatement pstmt;
						if (MWDaemon.mw_version == MWDaemon.MW_NEW) {
							pstmt = MWDaemon.getDBConn(
								MWDaemon.dbnames[i]).prepareStatement(
								"SELECT MAX(rev_timestamp) FROM revision");
						} else {
							pstmt = MWDaemon.getDBConn(
									MWDaemon.dbnames[i]).prepareStatement(
									"SELECT MAX(cur_timestamp) FROM cur");							
						}
						ResultSet res = pstmt.executeQuery();
						res.next();
						curTimestamp = res.getString(1);
						curTimestamps.put(MWDaemon.dbnames[i], curTimestamp);
					} catch (SQLException e) {
						e.printStackTrace();
					}
				}
				System.out.println("Running update, cur_timestamp="+curTimestamp+"...");
				/*try {
					DatabaseEntry dbkey = new DatabaseEntry("timestamp".getBytes("UTF-8"));
					DatabaseEntry data = new DatabaseEntry();
					Transaction txn = s.matcher.dbenv.beginTransaction(null, null);
					OperationStatus status = s.matcher.miscdb.get(txn, dbkey, data, LockMode.DEFAULT);
					if (status != OperationStatus.NOTFOUND) {
						curTimestamp = new String(data.getData());
					}
					txn.commit();
				} catch (DatabaseException e) {
					System.out.println("Warning: database error: " + e.getMessage());
				} catch (UnsupportedEncodingException e) {}*/
				SearchClientReader.State s = (SearchClientReader.State) 
				SearchClientReader.states.get(MWDaemon.dbnames[i]);
				
				try {
					Transaction txn = s.matcher.dbenv.beginTransaction(null, null);
					PreparedStatement pstmt;
					if (MWDaemon.mw_version == MWDaemon.MW_OLD)
						pstmt = MWDaemon.getDBConn(
							MWDaemon.dbnames[i]).prepareStatement(
							"SELECT cur_title, cur_namespace, cur_text, cur_timestamp FROM cur" +
							" WHERE cur_timestamp > ?");
					else
						pstmt = MWDaemon.getDBConn(
								MWDaemon.dbnames[i]).prepareStatement(
								"SELECT page_title, page_namespace, old_text, rev_timestamp "+
								"FROM page,revision,text "+
								"WHERE page_id=rev_page AND rev_id=old_id AND rev_timestamp > ?");
					pstmt.setString(1, curTimestamp);
					ResultSet rs = pstmt.executeQuery();
					while (rs.next()) {
						String namespace = rs.getString(2);
						String title = rs.getString(1).replaceAll("_", " ");
						String content = rs.getString(3);
						String timestamp = rs.getString(4);
						System.out.println("Add: [" + namespace + ":" + title + "]");
						if (timestamp.compareTo(curTimestamp) > 0) {
							curTimestamp = timestamp;
							curTimestamps.put(MWDaemon.dbnames[i], curTimestamp);
						}
						
						if (!MWDaemon.latin1) {
							try {
								title = new String(title.getBytes("ISO-8859-1"), "UTF-8");
								content = new String(content.getBytes("ISO-8859-1"), "UTF-8");
							} catch (UnsupportedEncodingException e) {}
						}
						Document d = new Document();
						d.add(Field.Text("namespace", namespace));
						d.add(Field.Text("title", title));
						d.add(new Field("contents", MWSearch.stripWiki(content), false, true, true));
						try {
							writer.addDocument(d);
						} catch (IOException e5) {
							System.out.println("Error adding document [" + namespace
									+ ":" + title + "]: " + e5.getMessage());
							return;
						}
						try {
							String stripped = TitlePrefixMatcher.stripTitle(title);
							Title t = new Title(Integer.valueOf(namespace).intValue(), title);
							DatabaseEntry data = new DatabaseEntry();
							SerialBinding binding = new SerialBinding(
									s.matcher.catalog, Title.class);
							StringBinding keybind = new StringBinding();
							DatabaseEntry dbkey = new DatabaseEntry();
							keybind.objectToEntry(stripped, dbkey);
							binding.objectToEntry(t, data);
							s.matcher.db.put(txn, dbkey, data);
						} catch (DatabaseException e) {
							System.out.println("Database error caching titles:");
							e.printStackTrace();
						}
					}
					txn.commit();
				} catch (Exception e) {
					System.out.println("Error: " + e.getMessage());
					e.printStackTrace();
				}
				try {
					writer.close();
				} catch (IOException e) {}
			}
			for (Iterator i = SearchClientReader.states.values().iterator(); i.hasNext();) {
				SearchClientReader.State s = (SearchClientReader.State) i.next();
				try {
					s.searcher.close();
					s.reader.close();
					s.reader = IndexReader.open(s.indexpath);
					s.searcher = new IndexSearcher(s.reader);
				} catch (IOException e) {}
			}
		}
	}
}
