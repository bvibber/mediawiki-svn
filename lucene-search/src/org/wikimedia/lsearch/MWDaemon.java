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
	
	public static void main(String[] args) {
		int i = 0;
		while (i < args.length - 1) {
			if (args[i].equals("-port"))
				port = Integer.valueOf(args[++i]).intValue();
			else if (args[i].equals("-configfile"))
				configfile = args[++i];
			++i;
		}
		Properties p = new Properties();
		try {
			p.load(new FileInputStream(new File(configfile)));
		} catch (FileNotFoundException e3) {
			System.err.println("Error: config file " + configfile + " not found");
			return;
		} catch (IOException e3) {
			System.err.println("Error: IO error reading config: " + e3.getMessage());
			return;
		}
		indexPath = p.getProperty("mwsearch.indexpath");
		System.out.println("Binding server to port " + port);
		
		try {
			sock = new ServerSocket(port);
		} catch (IOException e) {
			System.err.println("Error: bind error: " + e.getMessage());
			return;
		}
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
