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
import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.MessageFormat;
import java.util.Stack;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.queryParser.QueryParser;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Searcher;

import com.sleepycat.je.DatabaseException;

/**
 * @author Kate Turner
 *
 */
public class SearchState {
	private static Stack<SearchState> states;
	
	public static SearchState forWiki(String dbname) throws SQLException {
		if (states == null)
			states = new Stack<SearchState>();
		synchronized(states) {
			SearchState t = null;
			for (SearchState state : states)
				if (state.mydbname.equals(dbname))
					return state;
			if (states.size() > 100) {
				t = states.remove(0);
				t.close();
			}
			t = new SearchState(dbname);
			states.push(t);
			return t;
		}
	}

	public static boolean stateOpen(String state) {
		for (SearchState s : states)
			if (s.mydbname.equals(state))
				return true;
		return false;
	}
	
	public static void resetStates() {
		synchronized (states) {
			for (SearchState state : states)
				state.reopen();
		}
	}

	private String mydbname;
	Searcher searcher = null;
	Analyzer analyzer = null;
	QueryParser parser = null;
	IndexReader reader = null;		
	TitlePrefixMatcher matcher = null;
	String indexpath;
	Configuration config;
	IndexWriter writer;
	boolean writable = false;
	
	private SearchState(String dbname) throws SQLException {
		config = Configuration.open();
		indexpath = MessageFormat.format(config.getString("mwsearch.indexpath"),
				dbname);
		File f = new File(indexpath);
		if (!f.exists())
			f.mkdirs();
		
		System.out.printf("%s: opening state\n", dbname);
		analyzer = new EnglishAnalyzer();
		parser = new QueryParser("contents", analyzer);
		try {
			reader = IndexReader.open(indexpath);
			searcher = new IndexSearcher(reader);
		} catch (IOException e) {
			System.out.printf("%s: warning: open for read failed\n", dbname);
		}
		System.out.printf("%s: reading title index...\n", dbname);
		matcher = new TitlePrefixMatcher(dbname);
		mydbname = dbname;
	}
	
	private void close() {
		try {
			searcher.close();
			reader.close();
		} catch (IOException e) {
			System.err.println(mydbname + ": warning: closing index: " + e.getMessage());
		}
	}
	
	private void reopen() {
		try {
			searcher.close();
			reader.close();
			reader = IndexReader.open(indexpath);
			searcher = new IndexSearcher(reader);
		} catch (IOException e) {}
	}
	
	private void openForWrite() throws IOException {
		if (writable)
			return;
		writer = new IndexWriter(
				MessageFormat.format(config.getString("mwsearch.indexpath"), mydbname),
				new EnglishAnalyzer(), true);
		writable = true;
	}
	
	public ArticleList enumerateArticles() throws SQLException {
		DatabaseConnection dbconn = DatabaseConnection.forWiki(mydbname);
		Connection conn = dbconn.getConn();
		String query;
		PreparedStatement pstmt;
		
		if (!config.getBoolean("mwsearch.oldmediawiki"))
			query = "SELECT page_namespace,page_title,old_text,page_timestamp " +
				"FROM page, text WHERE old_id=page_latest AND page_is_redirect=0";
		else
			query = "SELECT cur_namespace,cur_title,cur_text,cur_timestamp " +
				"FROM cur WHERE cur_is_redirect=0";

		pstmt = conn.prepareStatement(query, ResultSet.TYPE_FORWARD_ONLY,
				ResultSet.CONCUR_READ_ONLY);
		pstmt.setFetchSize(Integer.MIN_VALUE);
		ResultSet rs = pstmt.executeQuery();
		return new ArticleList(mydbname, rs);
	}
	
	public void addArticle(Article article) throws IOException, DatabaseException {
		openForWrite();
		Document d = new Document();
		d.add(Field.Text("namespace", article.getNamespace()));
		d.add(Field.Text("title", article.getTitle()));
		d.add(new Field("contents", stripWiki(article.getContents()), 
				false, true, true));
		writer.addDocument(d);
		if (article.getNamespace().equals("0")) {
			matcher.addArticle(article);
		}

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
			}
			if (k == -1)
				text = text.substring(0, l);
			else
				text = text.substring(0, l) + 
				text.substring(k + 2);
			i = text.indexOf("[[Image:");
			if (i == -1) i = text.indexOf("[[image:");		
			l = i;
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
