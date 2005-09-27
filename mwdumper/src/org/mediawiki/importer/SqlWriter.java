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

import java.io.OutputStream;
import java.io.PrintStream;
import java.text.MessageFormat;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Iterator;
import java.util.TimeZone;


public abstract class SqlWriter implements DumpWriter {
	protected PrintStream stream;
	
	public SqlWriter(OutputStream output) {
		stream = new PrintStream(output);
	}
	
	public void close() {
		stream.flush();
		stream.close();
	}
	
	public void writeStartWiki() {
		stream.println("-- MediaWiki XML dump converted to SQL");
	}
	
	public void writeEndWiki() {
		stream.println("-- DONE");
	}
	
	public void writeSiteinfo(Siteinfo info) {
		stream.println("");
		stream.println("-- Site: " + commentSafe(info.Sitename));
		stream.println("-- URL: " + commentSafe(info.Base));
		stream.println("-- Generator: " + commentSafe(info.Generator));
		stream.println("-- Case: " + commentSafe(info.Case));
		stream.println("--");
		stream.println("-- Namespaces:");
		for (Iterator i = info.Namespaces.keys(); i.hasNext();) {
			int key = ((Integer)i.next()).intValue();
			stream.println("-- " + key + ": " + info.Namespaces.getPrefix(key));
		}
		stream.println("");
	}
	
	public abstract void writeStartPage(Page page);
	
	public abstract void writeEndPage();
	
	public abstract void writeRevision(Revision revision);
	
	
	
	protected String commentSafe(String text) {
		// TODO
		return text;
	}
	
	protected Object insertRow(String table, Object[][] row) {
		StringBuilder sql = new StringBuilder();
		
		sql.append("INSERT INTO ");
		//sql.append(_tablePrefix);
		sql.append(table);
		sql.append(" (");
		
		for (int i = 0; i < row.length; i++) {
			String field = (String)row[i][0];
			if (i > 0)
				sql.append(',');
			sql.append(field);
		}
		sql.append(") VALUES (");
		
		for (int i = 0; i < row.length; i++) {
			Object val = row[i][1];
			if (i > 0)
				sql.append(',');
			sql.append(sqlSafe(val));
		}
		sql.append(");");
		
		stream.println(sql);
		return null;
	}
	
	protected void updateRow(String table, Object[][] row, String keyField, Object keyValue) {
		StringBuilder sql = new StringBuilder();
		
		sql.append("UPDATE ");
		//sql.append(_tablePrefix);
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
		
		sql.append(";");
		
		stream.println(sql);
	}
	
	protected String sqlSafe(Object val) {
		if (val == null)
			return "NULL";
		
		String str = val.toString();
		if (val instanceof String) {
			return sqlEscape(str);
		} else if (val instanceof Integer) {
			return str;
		} else if (val instanceof Double) {
			return str;
		} else {
			throw new IllegalArgumentException("Unknown type in SQL");
		}
	}
	
	protected String sqlEscape(String str) {
		StringBuilder sql = new StringBuilder();
		sql.append('\'');
		int len = str.length();
		for (int i = 0; i < len; i++) {
			char c = str.charAt(i);
			switch (c) {
			case '\u0000':
				sql.append("\\0");
				break;
			case '\n':
				sql.append("\\n");
				break;
			case '\r':
				sql.append("\\r");
				break;
			case '\u001a':
				sql.append("\\Z");
				break;
			case '"':
			case '\'':
			case '\\':
				sql.append('\\');
				// fall through
			default:
				sql.append(c);
				break;
			}
		}
		sql.append('\'');
		return sql.toString();
	}
	
	protected String titleFormat(String title) {
		return title.replace(" ", "_");
	}
	
	final MessageFormat timestampFormatter = new MessageFormat(
			"{0,number,0000}{1,number,00}{2,number,00}{3,number,00}{4,number,00}{5,number,00}");
	
	protected String timestampFormat(Calendar time) {
		return timestampFormatter.format(new Object[] {
				new Integer(time.get(Calendar.YEAR)),
				new Integer(time.get(Calendar.MONTH) + 1),
				new Integer(time.get(Calendar.DAY_OF_MONTH)),
				new Integer(time.get(Calendar.HOUR_OF_DAY)),
				new Integer(time.get(Calendar.MINUTE)),
				new Integer(time.get(Calendar.SECOND))});
	}
	
	protected String inverseTimestamp(Calendar time) {
		return timestampFormatter.format(new Object[] {
				new Integer(9999 - time.get(Calendar.YEAR)),
				new Integer(99 - time.get(Calendar.MONTH) - 1),
				new Integer(99 - time.get(Calendar.DAY_OF_MONTH)),
				new Integer(99 - time.get(Calendar.HOUR_OF_DAY)),
				new Integer(99 - time.get(Calendar.MINUTE)),
				new Integer(99 - time.get(Calendar.SECOND))});
	}

	TimeZone utc = TimeZone.getTimeZone("UTC");
	protected GregorianCalendar now() {
		return new GregorianCalendar(utc);
	}
}
