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
import java.net.ServerSocket;
import java.net.Socket;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.text.MessageFormat;
import java.util.HashMap;
import java.util.Properties;

/**
 * @author Kate Turner
 *
 */
public class MWDaemon {
	static int port = 8123;
	static String configfile = "./mwsearch.conf";
	static ServerSocket sock;
	public static String indexPath;
	private static String dburl, dbhost;
	static public Properties p;
	public static boolean latin1 = false;
	private static SearchIndexUpdater updater = null;
	public static String[] dbnames;
	public static HashMap dbconns;
	public static final int
	 MW_NEW = 1
	,MW_OLD = 2;
	public static int mw_version = MW_NEW;
	
	public static Connection getDBConn(String dbname) {
		return (Connection) dbconns.get(dbname);
	}
	public static void main(String[] args) {
		System.out.println(
				"MediaWiki Lucene search indexer - runtime search daemon.\n" +
				"Version 20041225, copyright 2004 Kate Turner.\n"
				);
		int i = 0;
		while (i < args.length - 1) {
			if (args[i].equals("-port"))
				port = Integer.valueOf(args[++i]).intValue();
			else if (args[i].equals("-configfile"))
				configfile = args[++i];
			else if (args[i].equals("-latin1"))
				latin1 = true;
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
			} else break;
			++i;
		}
		p = new Properties();
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
		dbnames = p.getProperty("mwsearch.databases").split(" ");
		System.out.println("Using database on " + dbhost);
		
		//System.out.println("Connecting to DB server...");
		dbconns = new HashMap();
		
		for (i = 0; i < dbnames.length; ++i) {
			try {
				String un = p.getProperty("mwsearch.username"),
				pw = p.getProperty("mwsearch.password");
				String[] urlargs = {dbhost, dbnames[i]};
				MessageFormat form = new MessageFormat(p.getProperty("mwsearch.dburl"));
				Connection dbconn = DriverManager.getConnection(form.format(urlargs),
						p.getProperty("mwsearch.username"),
						p.getProperty("mwsearch.password"));
				dbconns.put(dbnames[i], dbconn);
			} catch (SQLException e2) {
				System.err.println("Error: DB connection error: " + e2.getMessage());
				return;
			}
		}

		indexPath = p.getProperty("mwsearch.indexpath");
		SearchClientReader.init();
		System.out.println("Binding server to port " + port);
		
		try {
			sock = new ServerSocket(port);
		} catch (IOException e) {
			System.err.println("Error: bind error: " + e.getMessage());
			return;
		}
		updater = new SearchIndexUpdater();
		updater.start();
		Socket client;
		for (;;) {
			try {
				client = sock.accept();
			} catch (IOException e1) {
				System.err.println("Error: accept() error: " + e1.getMessage());
				return;
			}
			SearchClientReader clnt = new SearchClientReader(client);
			clnt.start();
		}

	}
}
