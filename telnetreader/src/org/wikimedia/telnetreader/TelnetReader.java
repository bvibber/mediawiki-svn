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

import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * @author Kate Turner
 *
 */
public class TelnetReader {
	private static ServerSocket sock;
	private static int port = 8023;
	
	public static void logMsg(String msg) {
		SimpleDateFormat d = new SimpleDateFormat("d-MMM-yyyy HH:mm:ss");
		String m = d.format(new Date()) + ": " + msg;
		System.out.println(m);
	}
	
	public static void main(String[] args) {
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
