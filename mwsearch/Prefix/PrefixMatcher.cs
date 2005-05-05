/*
 * Copyright 2004 Kate Turner
 * Copyright 2005 Brion Vibber
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

namespace MediaWiki.Search.Prefix {
	using System;
	using System.Collections;
	using System.Data;
	using System.IO;
	using Mono.Data.SqliteClient;

	/**
	 * @author Kate Turner
	 *
	 */
	public class PrefixMatcher {
		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		private Configuration config;
		private IDbConnection db;
		private int maxResults = 20;
		
		public static PrefixMatcher ForWiki(string dbname) {
			// todo
			return new PrefixMatcher(dbname);
		}
		
		public PrefixMatcher(string dbname) {
			config = Configuration.Open();
			
			try {
				string pfxdir;
				pfxdir = string.Format(config.GetString("mwsearch", "titledb"), dbname );
				if (!Directory.Exists(pfxdir)) {
					Directory.CreateDirectory(pfxdir);
				}
				
				string connectString = "version=3,URI=file:" + pfxdir + "/titleindex.db"; 
				log.Debug(dbname + ": sqlite connection string: " + connectString);

				db = new SqliteConnection(connectString);
				db.Open();
			} catch (Exception e) {
				log.Fatal(e);
			}
			log.Debug(dbname + ": opened title database okay");
		}
		
		public IList GetMatches(string prefix) {
			IDbCommand command = db.CreateCommand();
			command.CommandText = string.Format(
				"SELECT namespace,title FROM titles WHERE stripped LIKE '{0}' LIMIT {1}",
				Encode(StripTitle(prefix) + "%"),
				maxResults);
			IDataReader reader = command.ExecuteReader();
			
			IList l = new ArrayList();
			while (reader.Read()) {
				l.Add(new Article(int.Parse(reader.GetString(0)), reader.GetString(1)));
			}
			
			reader.Close();
			return l;
		}
		
		private static string StripTitle(string title) {
			title = title.ToLower();
			string res = "";
			for (int i = 0; i < title.Length; ++i) {
				char c = title[i];
				if (char.IsLetterOrDigit(c)) {
					res += char.ToLower(c);
				}
			}
			return res;
		}
		
		// Mono.Data.SqliteClient's parameter support is borked.
		// We have to manually encode strings for SQL...
		private static string Encode(string str) {
			return str.Replace("\0", "").Replace("'", "''");
		}
		
		public void CreateTable() {
			IDbCommand command = db.CreateCommand();
			try {
				command.CommandText = "DROP TABLE titles";
				command.ExecuteNonQuery();
			} catch {
				log.Debug("titles table not present or couldn't delete it");
			}
			command.CommandText = "CREATE TABLE titles (" +
				"stripped VARCHAR," +
				"namespace INT," +
				"title VARCHAR)";
			command.ExecuteNonQuery();
		}
		
		public void CreateIndex() {
			IDbCommand command = db.CreateCommand();
			command.CommandText = "CREATE INDEX prefix ON titles (stripped)";
			command.ExecuteNonQuery();
		}
		
		public void AddArticle(Article article) {
			// FIXME trim old entries
			string stripped = StripTitle(article.Title);
			IDbCommand command = db.CreateCommand();
			command.CommandText = string.Format(
				"INSERT INTO titles VALUES('{0}','{1}','{2}')",
				Encode(stripped),
				Encode(article.Namespace),
				Encode(article.Title));
			command.ExecuteNonQuery();
		}
		
		~PrefixMatcher() {
			db.Close();
		}
		
	}
}
