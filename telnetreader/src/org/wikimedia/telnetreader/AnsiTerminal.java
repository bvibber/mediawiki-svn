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
import java.util.List;

/**
 * @author Kate Turner
 *
 */
public class AnsiTerminal {
	TelnetIOStream strm;
	private String topstatus;
	private String botstatus;
	private List lines;
	
	public AnsiTerminal(TelnetIOStream strm) {
		this.strm = strm;
	}
	
	public String getBotstatus() {
		return botstatus;
	}
	public void setBotstatus(String botstatus) {
		this.botstatus = botstatus;
	}
	public String getTopstatus() {
		return topstatus;
	}
	public void setTopstatus(String topstatus) {
		this.topstatus = topstatus;
	}
	
	private String
		 ED = "\033[2J"		/* Erase entire display */
		,CH = "\033[H"		/* Cursor position (home) */
		;
	public void erase() throws IOException {
		strm.write(ED);
	}
	public void home() throws IOException {
		strm.write(CH);
	}
	public void moveto(int x, int y) throws IOException {
		strm.write("\033["+x+";"+y+"H");
	}
	public String makeStatusbar(String text) {
		String sbar = text;
		if (sbar.length() < strm.getTermcols()) {
			for (int i = 0, end = (strm.getTermcols() - sbar.length()-1); i < end; ++i)
				sbar += ' ';
		} else {
			sbar = sbar.substring(0, strm.getTermcols());
		}
		return sbar;
	}
	public void writeAt(int x, int y, String s) throws IOException {
		moveto(x,y);
		strm.write(s);
	}
	public void drawStatusBars() throws IOException {
		writeAt(1, 1, makeStatusbar(topstatus));
		writeAt(strm.getTermrows(), 1, makeStatusbar(botstatus));
	}
	public void redraw() throws IOException {
		erase();
		home();
		drawStatusBars();
	}
	public String makeCentre(int width, char pad, String title) {
		int diff = width - title.length();
		int each = diff/2;
		String res = "";
		int t = each;
		while (t-- > 0) res += pad;
		res += title;
		t = each;
		while (t-- > 0) res += pad;
		if (res.length() >= width)
			return res.substring(0, width);
		return res;
	}
	public int getMenuChoice(String title, String[] options) throws IOException {
		int maxwidth = 0;
		for (int i = 0; i < options.length; ++i) {
			if (maxwidth < options[i].length())
					maxwidth = options[i].length();
		}
		int maxchoice = options.length;
		if (title.length() > maxwidth)
			maxwidth = title.length();
		maxwidth = (maxwidth + 1) & 0xFFFFFFFE;
		int boxwidth = maxwidth + 6;
		String[] boxlines = new String[options.length + 2];
		boxlines[0] = '+' + makeCentre(boxwidth - 2, '-', title) + '+';
		for (int i = 0; i < options.length; ++i) {
			boxlines[i + 1] = "|  " + (char)('a'+i) + ") " + options[i];
			int j = boxwidth - boxlines[i + 1].length() - 2;
			while (j-- > 0) boxlines[i + 1] += " ";
			boxlines[i + 1] += "|";
		}
		boxlines[options.length + 1] = "+" + makeCentre(boxwidth-2, '-', "-") + "+";
		int start = (strm.getTermrows()/2) - (options.length+2)/2;
		int left = (strm.getTermcols()/2) - maxwidth/2;
		for (int i = 0; i < boxlines.length; ++i) {
			moveto(start + i, left);
			strm.write(boxlines[i]);
		}
		int choice = -1;
		while (choice == -1) {
			int i = strm.read();
			char c = (char)i;
			if (c < 'a' || c > 'a'+options.length)
				continue;
			else choice = c - 'a';
		}
		return choice;
	}
}
