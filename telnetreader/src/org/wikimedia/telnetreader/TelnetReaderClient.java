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
import java.io.FileReader;
import java.io.IOException;
import java.net.Socket;
import java.util.ArrayList;
import java.util.List;

/**
 * @author Kate Turner
 *
 */
public class TelnetReaderClient extends Thread {
	private Socket client;
	private TelnetIOStream strm;
	private AnsiTerminal term;
	private boolean sourceView;
	private String articleText;
	private String langcode;
	private List langcodes;

	private int project;
	private static final int
		 P_WIKIPEDIA = 0
		,P_WIKIQUOTE = 1
		,P_WIKTIONARY = 2
		,P_WIKIBOOKS = 3
		;
	
	private static final String key =
		"q - quit; g - go to article; \\ - toggle source view";
	
	public TelnetReaderClient(Socket newclient) {
		this.client = newclient;
		sourceView = false;
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
			term.redraw();
			do {
				langcode = term.readString(
						"Enter the language code you would like to read:");
			} while (!isValidLangcode(langcode));
			term.setTopstatus("Wikimedia TELNET Reader: " + nameForProject(project)
					+ " - " + langcode + " - No article selected");
			term.setBotstatus(key);
			term.redraw();
			goToArticle();
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
		while ((i = term.readKey()) != -1) {
			TelnetReader.logMsg("Read key: " + i);
			switch (i) {
			case 'g':
				goToArticle();
				break;
			case 'q':
				return;
			case '\\':
				toggleSource();
				break;
			case AnsiTerminal.KEY_DOWN:
				term.scroll(1);
				break;
			case AnsiTerminal.KEY_UP:
				term.scroll(-1);
				break;
			}
		}
	}
	protected void goToArticle() throws IOException {
		String title = term.readString("Which article would you like to read?");
		String text = getArticleText(title);
		if (text == null) {
			term.alert("Sorry, that article doesn't exist");
			return;
		}
		this.articleText = text;
		redrawDisplay();
	}
	
	protected void toggleSource() throws IOException {
		sourceView = !sourceView;
		redrawDisplay();
	}
	protected void redrawDisplay() throws IOException {
		term.setPagerText(articleText);
		term.redraw();
	}
	public boolean isValidLangcode(String code) {
		if (langcodes == null)
			readLangCodes();
		return langcodes.contains(code);
	}
	public void readLangCodes() {
		langcodes = new ArrayList();
		try {
			File            langlist = new File("/home/wikipedia/common/langlist");
			FileReader      in       = new FileReader(langlist);
			String          s        = "";
			
			int i;
			while ((i = in.read()) != -1) {
				char c = (char) i;
				if (c == '\n') {
					langcodes.add(s);
					s = "";
				} else
					s = s + c;
			}
			in.close();
		} catch (Exception e) {}
	}
	
	public String getArticleText(String title) {
		String what = "wiki";
		if (project == P_WIKIPEDIA) what = "wiki";
		else if (project == P_WIKIBOOKS) what = "wikibooks";
		else if (project == P_WIKTIONARY) what = "wiktionary";
		else if (project == P_WIKIQUOTE) what = "wikiquote";
		return TelnetReader.getArticleText(langcode + what, 0, title);
	}
}
