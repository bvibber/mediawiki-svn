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

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.text.MessageFormat;
import java.util.HashMap;
import java.util.Map;

/**
 * @author Kate Turner
 *
 */
public class DatabaseConnection {
	private static Map<String, DatabaseConnection> dbconns;
	private static Configuration config;
	static {
		dbconns = new HashMap<String, DatabaseConnection>();
	}
	public static DatabaseConnection forWiki(String dbname) throws SQLException {
		if (config == null)
			config = Configuration.open();
		
		DatabaseConnection t = dbconns.get(dbname);
		if (t != null) {
			t.refcount++;
			return t;
		}
		t = new DatabaseConnection(dbname);
		dbconns.put(dbname, t);
		return t;
	}
	
	private Connection conn;
	private int refcount;
	
	private DatabaseConnection(String dbname) throws SQLException {
		String dburl = MessageFormat.format(config.getString("mwsearch.dburl"),
				config.getString("mwsearch.database.host"), dbname);
		Connection dbconn = DriverManager.getConnection(dburl,
				config.getString("mwsearch.username"),
				config.getString("mwsearch.password"));
		dbconn.prepareStatement("set net_read_timeout=2000").executeUpdate();
		dbconn.prepareStatement("set net_write_timeout=2000").executeUpdate();
		conn = dbconn;
		refcount = 1;
	}
	public Connection getConn() {
		return conn;
	}
	public void close() {
		if (--refcount == 0)
			try {
				conn.close();
			} catch (SQLException e) {
				System.err.println("Warning: closing database: " + e.getMessage());
			}
	}
}
