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
import java.net.Socket;

/**
 * @author Kate Turner
 *
 */
public class TelnetReaderClient extends Thread {
	private Socket client;
	private TelnetIOStream strm;
	private AnsiTerminal term;
	
	private int project;
	private static final int
		 P_WIKIPEDIA = 0
		,P_WIKIQUOTE = 1
		,P_WIKTIONARY = 2
		,P_WIKIBOOKS = 3
		;
	
	public TelnetReaderClient(Socket newclient) {
		this.client = newclient;
	}
	
	public void run() {
		try {
			this.strm = new TelnetIOStream(client.getInputStream(), client.getOutputStream());
			strm.write("Waiting for terminal negotiation...");
			strm.waitForTermSetup();
			strm.write(" okay\r\n");
			this.term = new AnsiTerminal(strm);
			term.setBotstatus("");
			term.setTopstatus("Wikimedia TELNET Reader: No project selected");
			term.redraw();
			int choice = -1;
			while (choice == -1) {
				choice = term.getMenuChoice("Which project would you like to read?",
						new String[] {
							"Wikipedia",
							"Wiktionary",
							"Wikiquote",
							"Wikibooks"
					}
				);
			}
			if (choice == 0)
				project = P_WIKIPEDIA;
			else if (choice == 1)
				project = P_WIKTIONARY;
			else if (choice == 2)
				project = P_WIKIQUOTE;
			else if (choice == 3)
				project = P_WIKIBOOKS;
			term.setTopstatus("Wikimedia TELNET Reader: " + nameForProject(project));
			term.redraw();
			processInputForever();
		} catch (IOException e) {
			TelnetReader.logMsg("I/O error from client " + client.getRemoteSocketAddress().toString()
					+ ": " + e.getMessage());
			return;
		} finally {
			try {
				client.close();
			} catch (IOException e1) {}
		}
		TelnetReader.logMsg("Client exited normally");
		return;
	}
	
	public String nameForProject(int i) {
		switch(i) {
		case P_WIKIPEDIA: return "Wikipedia";
		case P_WIKIQUOTE: return "Wikiquote";
		case P_WIKTIONARY: return "Wiktionary";
		case P_WIKIBOOKS: return "Wikibooks";
		}
		return "Unknown";
	}
	
	void processInputForever() throws IOException {
		int i;
		while ((i = strm.read()) != -1)
			;
	}
}
