// created on 8/29/2005 at 12:41 AM

using System;
using System.Collections;
using System.IO;
using System.Text;

abstract class SqlWriter : IDumpWriter {
	protected TextWriter _stream;
	
	public SqlWriter(TextWriter output) {
		_stream = output;
	}
	
	public void WriteStartWiki() {
		_stream.WriteLine("-- MediaWiki XML dump converted to SQL");
	}
	
	public void WriteEndWiki() {
		_stream.WriteLine("-- DONE");
	}
	
	public void WriteSiteinfo(Siteinfo info) {
		_stream.WriteLine("");
		_stream.WriteLine("-- Site: " + CommentSafe(info.Sitename));
		_stream.WriteLine("-- URL: " + CommentSafe(info.Base));
		_stream.WriteLine("-- Generator: " + CommentSafe(info.Generator));
		_stream.WriteLine("-- Case: " + CommentSafe(info.Case));
		_stream.WriteLine("--");
		_stream.WriteLine("-- Namespaces:");
		foreach (int key in info.Namespaces.Keys) {
			_stream.WriteLine("-- " + key + ": " + info.Namespaces[key]);
		}
		_stream.WriteLine("");
	}
	
	public abstract void WriteStartPage(Page page);
	
	public abstract void WriteEndPage();
	
	public abstract void WriteRevision(Revision revision);
	
	
	
	protected string CommentSafe(string text) {
		// TODO
		return text;
	}
	
	protected object InsertRow(string table, IDictionary row) {
		StringBuilder sql = new StringBuilder();
		bool first;
		
		sql.Append("INSERT INTO ");
		//sql.Append(_tablePrefix);
		sql.Append(table);
		sql.Append(" (");
		
		first = true;
		foreach (string field in row.Keys) {
			if (!first)
				sql.Append(',');
			first = false;
			sql.Append(field);
		}
		sql.Append(") VALUES (");
		
		first = true;
		foreach (object val in row.Values) {
			if (!first)
				sql.Append(',');
			first = false;
			sql.Append(SqlSafe(val));
		}
		sql.Append(");");
		
		_stream.WriteLine(sql);
		return null;
	}
	
	protected void UpdateRow(string table, IDictionary row, string keyField, object keyValue) {
		StringBuilder sql = new StringBuilder();
		bool first;
		
		sql.Append("UPDATE ");
		//sql.Append(_tablePrefix);
		sql.Append(table);
		sql.Append(" SET ");
		
		first = true;
		foreach (string field in row.Keys) {
			if (!first)
				sql.Append(',');
			first = false;
			sql.Append(field);
			sql.Append('=');
			sql.Append(SqlSafe(row[field]));
		}
		
		sql.Append(" WHERE ");
		sql.Append(keyField);
		sql.Append('=');
		sql.Append(SqlSafe(keyValue));
		
		sql.Append(";");
		
		_stream.WriteLine(sql);
	}
	
	protected string SqlSafe(object val) {
		if (val == null)
			return "NULL";
		
		Type type = val.GetType();
		int i = 1;
		double d = 1.0;
		
		string str = val.ToString();
		if (type == str.GetType()) {
			return SqlEscape(str);
		} else if (type == i.GetType()) {
			return str;
		} else if (type == d.GetType()) {
			return str;
		} else {
			throw new ArgumentException("Unknown thingy in SQL");
		}
	}
	
	protected string SqlEscape(string str) {
		StringBuilder sql = new StringBuilder();
		sql.Append('\'');
		int len = str.Length;
		for (int i = 0; i < len; i++) {
			char c = str[i];
			switch (c) {
			case '\u0000':
				sql.Append("\\0");
				break;
			case '\n':
				sql.Append("\\n");
				break;
			case '\r':
				sql.Append("\\r");
				break;
			case '\u001a':
				sql.Append("\\Z");
				break;
			case '"':
			case '\'':
			case '\\':
				sql.Append('\\');
				goto default;
			default:
				sql.Append(c);
				break;
			}
		}
		sql.Append('\'');
		return sql.ToString();
	}
	
	protected string TitleFormat(string title) {
		return title.Replace(' ', '_');
	}
	
	protected string TimestampFormat(DateTime time) {
		return string.Format("{0:0000}{1:00}{2:00}{3:00}{4:00}{5:00}",
			time.Year,
			time.Month,
			time.Day,
			time.Hour,
			time.Minute,
			time.Second);
	}
	
	protected string InverseTimestamp(DateTime time) {
		return string.Format("{0:0000}{1:00}{2:00}{3:00}{4:00}{5:00}",
			9999 - time.Year,
			99 - time.Month,
			99 - time.Day,
			99 - time.Hour,
			99 - time.Minute,
			99 - time.Second);
	}
}
