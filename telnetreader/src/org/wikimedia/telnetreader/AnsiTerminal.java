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
import java.util.ArrayList;
import java.util.List;
import java.util.StringTokenizer;

/**
 * @author Kate Turner
 *
 */
public class AnsiTerminal {
	public static final int
		 KEY_UP = -2
		,KEY_DOWN = -3
		,KEY_LEFT = -4
		,KEY_RIGHT = -5
		,KEY_UNKNOWN = -6;
		;
	
	TelnetIOStream strm;
	private String topstatus;
	private String botstatus;
	private List lines;
	private int scrollpos;
	
	public AnsiTerminal(TelnetIOStream strm) {
		this.strm = strm;
		scrollpos = 0;
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
		,REV = "\033[7m"	/* Begin reverse video */
		,NORM = "\033[0m"	/* No attributes */
		,EL = "\033[2K"		/* Erase entire line */
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
		return REV + sbar + NORM;
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
		home();
		erase();
		drawStatusBars();
		redrawArticle();
	}
	public String makeCentre(int width, char pad, String title, boolean inv) {
		/*
		 * Even number of characters:
		 *   "foobar" -> 10
		 *   10-len -> 4
		 *   4/2 -> 2
		 *   -> 2 + "foobar" + 2 -> 10
		 */
		if ((title.length() & 1) == 1)
			title += pad;
		int diff = width - title.length();
		int each = diff/2;
		String res = "";
		int t = each;
		while (t-- > 0) res += pad;
		res += inv ? (REV+title+NORM) : title;
		t = each;
		while (t-- > 0) res += pad;
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
		boxlines[0] = '+' + makeCentre(boxwidth - 2, '-', title, false) + '+';
		for (int i = 0; i < options.length; ++i) {
			String s = options[i];
			if ((s.length() & 1) == 1)
				s += " ";
			boxlines[i + 1] = "|  " + (char)('a'+i) + ") " + options[i];
			int j = boxwidth - boxlines[i + 1].length() - 1;
			while (j-- > 0) boxlines[i + 1] += " ";
			boxlines[i + 1] += "|";
		}
		boxlines[options.length + 1] = "+" + makeCentre(boxwidth-2, '-', "-", false) + "+";
		int start = (strm.getTermrows()/2) - (options.length+2)/2;
		int left = (strm.getTermcols()/2) - maxwidth/2;
		for (int i = 0; i < boxlines.length; ++i) {
			moveto(start + i, left);
			strm.write(boxlines[i]);
		}
		int choice = -1;
		while (choice == -1) {
			int i = readKey();
			char c = (char)i;
			if (c < 'a' || c > 'a'+options.length)
				continue;
			else choice = c - 'a';
		}
		return choice;
	}
	public String readString(String prompt) throws IOException {
		int left = (int) (strm.getTermcols() * 0.25);
		int width = max(prompt.length() + 4, strm.getTermcols() / 2);
		width = (width + 1) & 0xFFFFFFFE;
		String title = "+" + makeCentre(width - 2, '-', prompt, false) + "+";
		String foot = "+" + makeCentre(width - 2, '-', "", false) + "+";
		String middle = "| " + REV + makeCentre(width - 4, ' ', "", false) + NORM + " |";
		int hstart = strm.getTermrows() / 2 - 2;
		writeAt(hstart, left, title);
		writeAt(hstart + 1, left, middle);
		writeAt(hstart + 2, left, foot);
		moveto(hstart + 1, left + 2);
		int i;
		int curpos = 0;
		String result = "";
		while ((i = strm.read()) != -1) {
			char c = (char)i;
			switch(c) {
			case '\n': case '\r':
				return result;
			default:
				result += c;
				++curpos;
				writeAt(hstart + 1, left + 1 + curpos, REV + String.valueOf(c));
			}
		}
		return "";
	}
	public void alert(String err) throws IOException {
		int width = err.length() + 4;
		width = (width + 1) & 0xFFFFFFFE;
		String title = "+" + makeCentre(width - 2, '-', "Error", false) + "+";
		String foot = "+" + makeCentre(width - 2, '-', "[ OK ]", true) + "+";
		String middle = "| " + makeCentre(width - 4, ' ', err, false) + " |";
		int hstart = strm.getTermrows() / 2 - 2;
		int left = (strm.getTermcols()/2 - width/2);
		writeAt(hstart, left, title);
		writeAt(hstart + 1, left, middle);
		writeAt(hstart + 2, left, foot);
		while (strm.read() != '\r');
		redraw();
	}
	public static final class KeyMap {
		public String esc;
		public int val;
		public KeyMap(String esc, int val) {
			this.esc = esc; this.val = val;
		}
	}
	private static final KeyMap[] keymaps = {
			new KeyMap("[A", KEY_UP),	/* Up */
			new KeyMap("[B", KEY_DOWN),	/* Down */
			new KeyMap("[D", KEY_LEFT),	/* Left */
			new KeyMap("[C", KEY_RIGHT)	/* Right */
	};
	public int readKey() throws IOException {
		int i = strm.read();
		if (i != '\033')
			return i;
		String key = "";
		while ((i = strm.read()) != -1) {
			key += (char)i;
			if (key.length() > 2) {
				return KEY_UNKNOWN;
			}
			for (int j = 0; j < keymaps.length; ++j) {
				if (key.equals(keymaps[j].esc))
					return keymaps[j].val;
			}
		}
		return i;
	}
	public void setPagerText(String text) throws IOException {
		lines = new ArrayList();
		String thisline = "";
		int wid = strm.getTermcols();
		String[] lines1 = text.split("\n\n");
		for (int i = 0; i < lines1.length; ++i) {
			lines1[i] = lines1[i].replaceAll("\n", " ");
			StringTokenizer st = new StringTokenizer(lines1[i], " +");
			while (st.hasMoreTokens()) {
				String next = st.nextToken();
				if (thisline.length() + next.length() + 1 > wid) {
					if (thisline.length() > 0) {
						lines.add(thisline);
						thisline = next;
					} else {
						lines.add(next.substring(0, wid));
						thisline = next.substring(wid);
					}
				} else {
					thisline += next + " ";
				}
			}
			lines.add("");
		}
		redrawArticle();
	}
	public void redrawArticle() throws IOException {
		if (lines == null)
			return;
		int ln = lines.size();
		for (int i = 0; i < strm.getTermrows() - 2; ++i) {
			String x = EL;
			if (i+scrollpos < ln)
				x += (String)lines.get(i + scrollpos);
			writeAt(i + 2, 1, x);
		}
	}
	public void scroll(int howmuch) throws IOException {
		if ((scrollpos + howmuch) < 0)
			return;
		this.scrollpos += howmuch;
		redrawArticle();
	}
	private int min(int x, int y) {
		return x<y ? x : y;
	}
	private int max(int x, int y) {
		return x<y ? y : x;
	}
}
