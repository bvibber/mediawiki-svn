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
package org.wikimedia.telnetreader;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Properties;

/**
 * @author Kate Turner
 *
 */
public class TelnetReader {
	private static ServerSocket sock;
	private static int port = 8023;
	private static Connection dbconn;
	private static String dburl;
	
	public static void logMsg(String msg) {
		SimpleDateFormat d = new SimpleDateFormat("d-MMM-yyyy HH:mm:ss");
		String m = d.format(new Date()) + ": " + msg;
		System.out.println(m);
	}
	
	public static String getArticleText(String dbname, int ns, String article) {
		String query = "SELECT cur_text FROM "+dbname+".cur " +
				"WHERE cur_namespace=? AND cur_title=?";
		PreparedStatement pstmt;
		try {
			pstmt = dbconn.prepareStatement(query);
			pstmt.setInt(1, ns);
			pstmt.setString(2, article);
			ResultSet rs = pstmt.executeQuery();
			if (!rs.next())
				return null;
			return rs.getString(1);
		} catch (SQLException e) {
			logMsg("Warning: SQL error: " + e.getMessage());
			return null;
		}
	}
	public static void main(String[] args) {
		Properties p = System.getProperties();
		try {
			p.load(new FileInputStream(new File("/etc/tnr.conf")));
		} catch (FileNotFoundException e3) {
			logMsg("Config file tnr.conf not found");
			return;
		} catch (IOException e3) {
			logMsg("IO error reading config: " + e3.getMessage());
			return;
		}
		dburl = args[0];
		logMsg("Connecting to DB server...");
		try {
			dbconn = DriverManager.getConnection(dburl,p.getProperty("tnr.username"),
					p.getProperty("tnr.password"));
		} catch (SQLException e2) {
			logMsg("DB connection error: " + e2.getMessage());
			return;
		}
		logMsg("Binding server to port " + port);
		try {
			sock = new ServerSocket(port);
		} catch (IOException e) {
			logMsg("Bind error: " + e.getMessage());
			return;
		}
		Socket client;
		for (;;) {
			try {
				client = sock.accept();
			} catch (IOException e1) {
				logMsg("accept() error: " + e1.getMessage());
				return;
			}
			TelnetReaderClient clnt = new TelnetReaderClient(client);
			clnt.start();
			logMsg("Accepted new client from " + client.getRemoteSocketAddress().toString());
		}
	}
}
