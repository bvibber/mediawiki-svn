/*
 * MediaWiki import/export processing tools
 * Copyright 2005 by Brion Vibber
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

package org.mediawiki.importer;

import java.io.IOException;
import java.text.MessageFormat;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Map;
import java.util.HashMap;
import java.util.Iterator;
import java.util.TimeZone;


public abstract class SqlWriter implements DumpWriter {
	private SqlStream stream;
	
	protected static final Integer ONE = new Integer(1);
	protected static final Integer ZERO = new Integer(0);
	
	// UTC_TIMESTAMP() is new in MySQL 4.1 or 5.0, so using this
	// godawful hack found in documentation comments:
	protected static final SqlLiteral TOUCHED = new SqlLiteral(
		"DATE_ADD('1970-01-01', INTERVAL UNIX_TIMESTAMP() SECOND)+0");
	protected static final SqlLiteral RANDOM = new SqlLiteral("RAND()");	
	
	public SqlWriter(SqlStream output) {
		stream = output;
	}
	
	public void close() throws IOException {
		stream.close();
	}
	
	public void writeStartWiki() throws IOException {
		stream.writeComment("-- MediaWiki XML dump converted to SQL by mwdumper");
		stream.writeStatement("BEGIN");
	}
	
	public void writeEndWiki() throws IOException {
		flushInsertBuffers();
		stream.writeStatement("COMMIT");
		stream.writeComment("-- DONE");
	}
	
	public void writeSiteinfo(Siteinfo info) throws IOException {
		stream.writeComment("");
		stream.writeComment("-- Site: " + commentSafe(info.Sitename));
		stream.writeComment("-- URL: " + commentSafe(info.Base));
		stream.writeComment("-- Generator: " + commentSafe(info.Generator));
		stream.writeComment("-- Case: " + commentSafe(info.Case));
		stream.writeComment("--");
		stream.writeComment("-- Namespaces:");
		for (Iterator i = info.Namespaces.orderedEntries(); i.hasNext();) {
			Map.Entry e = (Map.Entry)i.next();
			stream.writeComment("-- " + e.getKey() + ": " + e.getValue());
		}
		stream.writeComment("");
	}
	
	public abstract void writeStartPage(Page page) throws IOException;
	
	public abstract void writeEndPage() throws IOException;
	
	public abstract void writeRevision(Revision revision) throws IOException;
	
	
	
	protected String commentSafe(String text) {
		// TODO
		return text;
	}
	
	private HashMap insertBuffers = new HashMap();
	private static final int blockSize = 1024 * 512; // default 512k inserts
	protected void bufferInsertRow(String table, Object[][] row) throws IOException {
		StringBuffer sql = (StringBuffer)insertBuffers.get(table);
		if (sql != null) {
			if (sql.length() < blockSize) {
				sql.append(',');
				appendInsertValues(sql, row);
				return;
			} else {
				flushInsertBuffer(table);
			}
		}
		sql = new StringBuffer(blockSize);
		synchronized (sql) { //only for StringBuffer
			appendInsertStatement(sql, table, row);
			insertBuffers.put(table, sql);
		}
	}
	
	protected void flushInsertBuffer(String table) throws IOException {
		stream.writeStatement((CharSequence)insertBuffers.get(table));
		insertBuffers.remove(table);
	}
	
	protected void flushInsertBuffers() throws IOException {
		Iterator iter = insertBuffers.values().iterator();
		while (iter.hasNext()) {
			stream.writeStatement((CharSequence)iter.next());
		}
		insertBuffers.clear();
	}
	
	protected void insertRow(String table, Object[][] row) throws IOException {
		StringBuffer sql = new StringBuffer(65536);
		appendInsertStatement(sql, table, row);		
		stream.writeStatement(sql);
	}
	
	private static void appendInsertStatement(StringBuffer sql, String table, Object[][] row) {
		sql.append("INSERT INTO ");
		//sql.append(tablePrefix);
		sql.append(table);
		sql.append(" (");
		
		for (int i = 0; i < row.length; i++) {
			String field = (String)row[i][0];
			if (i > 0)
				sql.append(',');
			sql.append(field);
		}
		sql.append(") VALUES ");
		appendInsertValues(sql, row);
	}
	
	private static void appendInsertValues(StringBuffer sql, Object[][] row) {
		sql.append('(');
		for (int i = 0; i < row.length; i++) {
			Object val = row[i][1];
			if (i > 0)
				sql.append(',');
			sql.append(sqlSafe(val));
		}
		sql.append(')');
	}
	
	protected void updateRow(String table, Object[][] row, String keyField, Object keyValue) throws IOException {
		StringBuffer sql = new StringBuffer(65536);
		synchronized (sql) { //only for StringBuffer
		sql.append("UPDATE ");
		//sql.append(tablePrefix);
		sql.append(table);
		sql.append(" SET ");
		
		for (int i = 0; i < row.length; i++) {
			String field = (String)row[i][0];
			Object val = row[i][1];
			if (i > 0)
				sql.append(',');
			sql.append(field);
			sql.append('=');
			sql.append(sqlSafe(val));
		}
		
		sql.append(" WHERE ");
		sql.append(keyField);
		sql.append('=');
		sql.append(sqlSafe(keyValue));
		
		stream.writeStatement(sql);
		}
	}
	
	protected static String sqlSafe(Object val) {
		if (val == null)
			return "NULL";
		
		String str = val.toString();
		if (val instanceof String) {
			return sqlEscape(str);
		} else if (val instanceof Integer) {
			return str;
		} else if (val instanceof Double) {
			return str;
		} else if (val instanceof SqlLiteral) {
			return str;
		} else {
			throw new IllegalArgumentException("Unknown type in SQL");
		}
	}
	
	protected static String sqlEscape(String str) {
		final int slen = str.length();
		if (slen == 0)
			return "''"; //TODO "NULL",too ?
		final char[] buf = Buffer.get(3 * slen + 2);
		str.getChars(0, slen, buf, 0);
		int len = slen;
		buf[len++] = '\'';
		for (int i = 0; i < slen; i++) {
			char c = buf[i];
			switch (c) {
			case '\u0000':
				buf[len++] = '\\';
				buf[len++] = '0';
				break;
			case '\n':
				buf[len++] = '\\';
				buf[len++] = 'n';
				break;
			case '\r':
				buf[len++] = '\\';
				buf[len++] = 'r';
				break;
			case '\u001a':
				buf[len++] = '\\';
				buf[len++] = 'Z';
				break;
			case '"':
			case '\'':
			case '\\':
				buf[len++] = '\\';
				// fall through
			default:
				buf[len++] = c;
				break;
			}
		}
		buf[len++] = '\'';
		return new String(buf, slen, len - slen);
	}
	
	protected static String titleFormat(String title) {
		return title.replace(' ', '_');
	}
	
	private static final MessageFormat timestampFormatter = new MessageFormat(
			"{0,number,0000}{1,number,00}{2,number,00}{3,number,00}{4,number,00}{5,number,00}");
	
	protected static String timestampFormat(Calendar time) {
		return timestampFormatter.format(new Object[] {
				new Integer(time.get(Calendar.YEAR)),
				new Integer(time.get(Calendar.MONTH) + 1),
				new Integer(time.get(Calendar.DAY_OF_MONTH)),
				new Integer(time.get(Calendar.HOUR_OF_DAY)),
				new Integer(time.get(Calendar.MINUTE)),
				new Integer(time.get(Calendar.SECOND))});
	}
	
	protected static String inverseTimestamp(Calendar time) {
		return timestampFormatter.format(new Object[] {
				new Integer(9999 - time.get(Calendar.YEAR)),
				new Integer(99 - time.get(Calendar.MONTH) - 1),
				new Integer(99 - time.get(Calendar.DAY_OF_MONTH)),
				new Integer(99 - time.get(Calendar.HOUR_OF_DAY)),
				new Integer(99 - time.get(Calendar.MINUTE)),
				new Integer(99 - time.get(Calendar.SECOND))});
	}

	private static final TimeZone utc = TimeZone.getTimeZone("UTC");
	protected static GregorianCalendar now() {
		return new GregorianCalendar(utc);
	}

	int commitInterval = 1000; // Commit a transaction every n pages
	int pageCount = 0;
	protected void checkpoint() throws IOException {
		pageCount++;
		if (pageCount % commitInterval == 0) {
			flushInsertBuffers();
			stream.writeStatement("COMMIT");
			stream.writeStatement("BEGIN");
		}
	}
}
