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

namespace MediaWiki.Search.Updater {
	using System;
	using System.Collections;
	using System.Data;
	using System.IO;
	
	using MySql.Data.MySqlClient;

	/**
	 * @author Kate Turner
	 *
	 */
	public class DatabaseConnection {
		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		
		//private static Map<String, DatabaseConnection> dbconns;
		private static IDictionary dbconns = new Hashtable();
		private static Configuration config;
		//static {
			////dbconns = new HashMap<String, DatabaseConnection>();
			//dbconns = new Hashtable();
		//}
		
		public static DatabaseConnection ForWiki(string dbname) {
			if (config == null)
				config = Configuration.Open();
			
			DatabaseConnection t = (DatabaseConnection)dbconns[dbname];
			if (t != null) {
				t.refcount++;
				return t;
			}
			t = new DatabaseConnection(dbname);
			dbconns[dbname] = t;
			return t;
		}
		
		private IDbConnection conn;
		private int refcount;
		private string mydbname;
		
		private DatabaseConnection(string dbname) {
			string connectString = string.Format(
				"Database={0};Server={1};Protocol={2};User Id={3};Password={4}",
				dbname,
				config.GetString("Database", "host"),
				config.GetString("Database", "protocol"),
				config.GetString("Database", "username"),
				config.GetString("Database", "password"));
			log.Debug("Connect string: " + connectString);
			IDbConnection dbconn = new MySqlConnection(connectString); 
			dbconn.Open();
			new MySqlCommand("set net_read_timeout=4000",(MySqlConnection)dbconn).ExecuteNonQuery();
			new MySqlCommand("set net_write_timeout=4000",(MySqlConnection)dbconn).ExecuteNonQuery();
			
			conn = dbconn;
			refcount = 1;
			mydbname = dbname;
		}
		
		public void Close() {
			if (--refcount == 0)
				try {
					conn.Close();
				} catch (Exception e) {
					log.Error("Warning: closing database: " + e.Message);
				}
		}

		public ArticleList EnumerateArticles() {
			return EnumerateArticles("19700101000000");
		}
		
		public ArticleList EnumerateArticles(string startDate) {
			string query;
			IDbCommand pstmt;
			string tablePrefix = config.GetString("Database", "tableprefix");
			if (tablePrefix == null) tablePrefix = "";
			
			string version = config.GetString("Database", "version");
			if (version == "" || version.Equals("1.5")) {
				// FIXME FOR TIMESTAMPS
				query = "SELECT page_namespace,page_title,old_text,page_timestamp " +
					"FROM " + tablePrefix + "page, " + tablePrefix + "text WHERE old_id=page_latest AND page_is_redirect=0";
			} else if (version.Equals("1.4")) {
				query = "SELECT cur_namespace,cur_title,cur_text,cur_timestamp " +
					"FROM " + tablePrefix + "cur FORCE INDEX (cur_timestamp) " +
					"WHERE cur_timestamp>\"" + startDate + "\" AND cur_is_redirect=0";
			} else {
				throw new Exception("Unknown MediaWiki version given in config.");
			}
			log.Debug("Executing query: " + query);
			pstmt = conn.CreateCommand();
			pstmt.CommandText = query;
			//pstmt = conn.prepareStatement(query, ResultSet.TYPE_FORWARD_ONLY,
			//		ResultSet.CONCUR_READ_ONLY);
			//pstmt.setFetchSize(Integer.MIN_VALUE);
			IDataReader rs = pstmt.ExecuteReader(CommandBehavior.SequentialAccess);
			return new ArticleList(mydbname, rs);
		}
	}
}
