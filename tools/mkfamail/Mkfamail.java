/*
 * Copyright 2004, 2005 Kate Turner
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
 */

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.security.MessageDigest;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Iterator;
import java.util.List;
import java.util.Properties;
import java.util.TimeZone;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

/**
 * @author Kate Turner
 *
 */
public class Mkfamail {
	static String contText;
	static String faText;
	static String saText;
	static String qotdText;
	
	private static String getHTTP(String urlstr) {
		String res = "";
		try {
			URL url = new URL(urlstr);
			HttpURLConnection conn = (HttpURLConnection) url.openConnection();
			conn.setUseCaches(false);
			conn.setRequestProperty("User-Agent",
					"Wikipedia-Daily-Article-Generator/NA (contact: kate.turner@gmail.com)");
			conn.connect();
			int i = conn.getResponseCode();
			if (i != 200) {
				System.out.println("Error retrieving "+urlstr+": " + conn.getResponseMessage());
				System.exit(1);
			}
			BufferedReader dis = new BufferedReader(new
			    InputStreamReader(new BufferedInputStream(conn.getInputStream())));
			char[] data = new char[1024];
			while ((i = dis.read(data, 0, 1024)) > -1) {
				res = res + new String(data, 0, i);
			}
		} catch (Exception e) {
			System.out.println("error: " + e.getMessage());
			System.exit(1);
		}
		return res;
	}
	public static void main(String[] args) throws UnsupportedEncodingException {
		contText = "";
		
		System.out.println("Daily Article message generator (revision B).  Please wait...\n");
		String when;
		SimpleDateFormat d = new SimpleDateFormat("MMMM d, yyyy");
		d.setTimeZone(TimeZone.getTimeZone("UTC"));
		when = d.format(new Date());
		String faname = "Today's_featured_article/" + when.replaceAll(" ", "_");
		PreparedStatement pstmt;
		String text;
		faText = getHTTP("http://en.wikipedia.org/w/index.php?title=Wikipedia:"+faname+"&action=raw");
		faText = HTMLDecode(faText);
		d = new SimpleDateFormat("MMMM_d");
		d.setTimeZone(TimeZone.getTimeZone("UTC"));
		when = d.format(new Date());
		String saname = "Selected_anniversaries/" + when;
		saText = getHTTP("http://en.wikipedia.org/w/index.php?title=Wikipedia:"+saname+"&action=raw");
		saText = HTMLDecode(saText);
		d = new SimpleDateFormat("MMMM_d,_yyyy");
		d.setTimeZone(TimeZone.getTimeZone("UTC"));
		when = d.format(new Date());
		qotdText = getHTTP("http://en.wikiquote.org/w/index.php?title=Wikiquote:Quote_of_the_day/"+when+"&action=raw");
		sendPlain();
	}
	
	static String wrapWords(String text, int width) {
		width -= 5;
		text = text.trim();
		String[] words = text.split("\\s+");
		text = "  ";
		int curlen = 2;
		for (int i = 0; i < words.length; ++i) {
			if ((curlen + words[i].length() + 1) > width) {
				text += "\n  ";
				curlen = 2;
			}
			text += words[i] + " ";
			// double-space after period
			if (words[i].endsWith(".")
				/* but not for acronyms */
				&& words[i].indexOf('.') == words[i].length() - 1)
				text += " ";
			curlen += words[i].length() + 1;
		}
		return text;
	}
	
	static void sendPlain() throws UnsupportedEncodingException {
		String text = faText;
		//System.err.println(text);
		text = text.replaceAll("\n", " ");
		text = text.replaceAll("<div[^>]*>.*?</div>", "");
		text = text.replaceAll("Recently featured:.*", "");
		Pattern pat = Pattern.compile("'''\\[\\[(.*?)(\\|.*?)?\\]\\][a-z']*'''");
		Matcher mat = pat.matcher(text);
		if (!mat.find()) {
			System.err.println("Could not extract article name...");
			return;
		}
		String articlename = mat.group(1);
		String articleurl = "http://en.wikipedia.org/wiki/"
				+ URLEncoder.encode(capfirst(articlename).replaceAll(" ", "_"), "ISO-8859-1");
		text = text.replaceAll("'''''(.*?)'''''", "$1");
		text = text.replaceAll("'''(.*?)'''", "$1");
		text = text.replaceAll("''(.*?)''", "$1");
		text = text.replaceAll("\\[\\[([^]\\|]*?\\||)([^]]*?)\\]\\]", "$2");
		text += "\n";
		text = wrapWords(text, 78);
		contText += text;
		contText += "\n\nRead the rest of this article:\n  ";
		contText += articleurl;
		contText += "\n\n";
		
		SimpleDateFormat d = new SimpleDateFormat("MMMM d");
		d.setTimeZone(TimeZone.getTimeZone("UTC"));
		String when = d.format(new Date());
		//mailSubject = when + ": " + articlename;
		System.out.println("Subject: " + when + ": " + capfirst(articlename) + "\n");
		
		// selected anniversaries
		text = saText;

		// rmv comments
		int st, en;
		while ((st = text.indexOf("<!--")) != -1) {
			en = text.indexOf("-->", st);
			if (en != -1)
				text = text.substring(0, st) + text.substring(en + 3);
			else
				break;
		}
		// only want lines starting "* [[date]] text..."
		pat = Pattern.compile("\\n(\\*\\s*\\[\\[(\\d|[a-zA-Z])+\\]\\][^\\n]*)");
		mat = pat.matcher(text);
		List lines = new ArrayList();
		while (mat.find()) {
			lines.add(mat.group(1));
		}
		contText += "\n_______________________________\n";
		contText += "Today's selected anniversaries:\n\n";
		for (Iterator i = lines.iterator(); i.hasNext();) {
			String evt = (String) i.next();
			// *[[date]] text...
			pat = Pattern.compile("^\\*\\s*\\[\\[([^]]*)\\]\\](\\s*-+\\s*)?(.*)\\s*$");
			mat = pat.matcher(evt);
			if (!mat.find()) {
				System.err.println("Could not parse SA entry: [" + evt + "}");
				return;
			}
			String date = mat.group(1);
			text = mat.group(3);
			contText += date + ":\n";
			pat = Pattern.compile("'''\\[\\[([^\\|]*?)(\\|.*?)?\\]\\]");
			mat = pat.matcher(text);
			articlename = null;
			if (mat.find()) {
				articlename = mat.group(1);
			}
			articleurl = null;
			if (articlename != null)
				articleurl = "http://en.wikipedia.org/wiki/"
					+ URLEncoder.encode(articlename.replaceAll(" ", "_"), "ISO-8859-1");
			text = text.replaceAll("'''''(.*?)'''''", "$1");
			text = text.replaceAll("'''(.*?)'''", "$1");
			text = text.replaceAll("''(.*?)''", "$1");
			text = text.replaceAll("\\[\\[([^]\\|]*?\\||)([^]]*?)\\]\\]", "$2");
			text = wrapWords(text, 78);
			contText += text + "\n";
			if (articleurl != null)
				contText += "  (" + articleurl + ")\n";
			contText += "\n";
		}

		// QOTD
		text = qotdText;
		text = text.replaceAll(" *\n *", " ");
		text = text.replaceAll("\\{\\{[^}]*\\}\\}", "");
		text = text.replaceAll("<[^>]*>", "");
		contText += "\n_____________________\n";
		contText += "Wikiquote of the day:\n\n";
		text = text.replaceAll("<div[^>]*>(.*)</div>", "$1");
		text = text.replaceAll(" ~ ", " -- ");
		pat = Pattern.compile("\\[\\[(.*?)(\\|.*?)?\\]\\]");
		mat = pat.matcher(text);
		articlename = null;
		if (mat.find()) {
			articlename = mat.group(1);
		}
		articleurl = null;
		if (articlename != null)
			articleurl = "http://en.wikiquote.org/wiki/"
				+ URLEncoder.encode(articlename.replaceAll(" ", "_"), "UTF-8");
		text = text.replaceAll("'''''(.*?)'''''", "$1");
		text = text.replaceAll("'''(.*?)'''", "$1");
		text = text.replaceAll("''(.*?)''", "$1");
		text = text.replaceAll("\\[\\[([^\\|]*\\||)(.*?)\\]\\]", "$2");
		qotdText = HTMLDecode(qotdText);
		text = wrapWords(text, 78);
		contText += text + "\n";
		if (articlename != null)
			contText += "  (" + articleurl + ")\n";
		contText += "\n";

		//System.out.println("Sending mail...");
		//sendMail(mailToPlain, contText, "text/plain", null);
		System.out.println(contText);
	}
	
	static String capfirst(String s) {
		return s.substring(0, 1).toUpperCase() + s.substring(1);
	}
	/*static String HTMLDecode(String s) {
		int st = 0, en, next = 0;
		while ((st = s.indexOf("&", next)) != -1) {
			en = s.indexOf(";", st);
			next = st + 1;
			if (en == -1)
				break;
			int len = en - st;
			if (len > 5)
				continue;
			char c = (char)(Integer.valueOf(s.substring(st + 2, en)).intValue());
			s = s.substring(0, st) + c + s.substring(en + 1);
		}
		return s;
	}*/
	static String htmlentities[][] = {
			{"nbsp", "160"},
			{"iexcl", "161"},
			{"cent", "162"},
			{"pound", "163"},
			{"curren", "164"},
			{"yen", "165"},
			{"brvbar", "166"},
			{"sect", "167"},
			{"uml", "168"},
			{"copy", "169"},
			{"ordf", "170"},
			{"laquo", "171"},
			{"not", "172"},
			{"shy", "173"},
			{"reg", "174"},
			{"macr", "175"},
			{"deg", "176"},
			{"plusmn", "177"},
			{"sup2", "178"},
			{"sup3", "179"},
			{"acute", "180"},
			{"micro", "181"},
			{"para", "182"},
			{"middot", "183"},
			{"cedil", "184"},
			{"sup1", "185"},
			{"ordm", "186"},
			{"raquo", "187"},
			{"frac14", "188"},
			{"frac12", "189"},
			{"frac34", "190"},
			{"iquest", "191"},
			{"Agrave", "192"},
			{"Aacute", "193"},
			{"Acirc", "194"},
			{"Atilde", "195"},
			{"Auml", "196"},
			{"Aring", "197"},
			{"AElig", "198"},
			{"Ccedil", "199"},
			{"Egrave", "200"},
			{"Eacute", "201"},
			{"Ecirc", "202"},
			{"Euml", "203"},
			{"Igrave", "204"},
			{"Iacute", "205"},
			{"Icirc", "206"},
			{"Iuml", "207"},
			{"ETH", "208"},
			{"Ntilde", "209"},
			{"Ograve", "210"},
			{"Oacute", "211"},
			{"Ocirc", "212"},
			{"Otilde", "213"},
			{"Ouml", "214"},
			{"times", "215"},
			{"Oslash", "216"},
			{"Ugrave", "217"},
			{"Uacute", "218"},
			{"Ucirc", "219"},
			{"Uuml", "220"},
			{"Yacute", "221"},
			{"THORN", "222"},
			{"szlig", "223"},
			{"agrave", "224"},
			{"aacute", "225"},
			{"acirc", "226"},
			{"atilde", "227"},
			{"auml", "228"},
			{"aring", "229"},
			{"aelig", "230"},
			{"ccedil", "231"},
			{"egrave", "232"},
			{"eacute", "233"},
			{"ecirc", "234"},
			{"euml", "235"},
			{"igrave", "236"},
			{"iacute", "237"},
			{"icirc", "238"},
			{"iuml", "239"},
			{"eth", "240"},
			{"ntilde", "241"},
			{"ograve", "242"},
			{"oacute", "243"},
			{"ocirc", "244"},
			{"otilde", "245"},
			{"ouml", "246"},
			{"divide", "247"},
			{"oslash", "248"},
			{"ugrave", "249"},
			{"uacute", "250"},
			{"ucirc", "251"},
			{"uuml", "252"},
			{"yacute", "253"},
			{"thorn", "254"},
			{"yuml", "255"},
			{"ouml", "246"},
			{"divide", "247"},
			{"oslash", "248"},
			{"ugrave", "249"},
			{"uacute", "250"},
			{"ucirc", "251"},
			{"uuml", "252"},
			{"yacute", "253"},
			{"thorn", "254"},
			{"yuml", "255"},
			{"quot", "34"},
			{"amp", "38"},
			{"lt", "60"},
			{"gt", "62"},
			{"OElig", "338"},
			{"oelig", "339"},
			{"Scaron", "352"},
			{"scaron", "353"},
			{"Yuml", "376"},
			{"circ", "710"},
			{"tilde", "732"},
			{"ensp", "8194"},
			{"emsp", "8195"},
			{"thinsp", "8201"},
			{"zwnj", "8204"},
			{"zwj", "8205"},
			{"lrm", "8206"},
			{"rlm", "8207"},
			{"ndash", "8211"},
			{"mdash", "8212"},
			{"lsquo", "8216"},
			{"rsquo", "8217"},
			{"sbquo", "8218"},
			{"ldquo", "8220"},
			{"rdquo", "8221"},
			{"bdquo", "8222"},
			{"dagger", "8224"},
			{"Dagger", "8225"},
			{"permil", "8240"},
			{"lsaquo", "8249"},
			{"rsaquo", "8250"},
			{"euro", "8364"},
			{"fnof", "402"},
			{"Alpha", "913"},
			{"Beta", "914"},
			{"Gamma", "915"},
			{"Delta", "916"},
			{"Epsilon", "917"},
			{"Zeta", "918"},
			{"Eta", "919"},
			{"Theta", "920"},
			{"Iota", "921"},
			{"Kappa", "922"},
			{"Lambda", "923"},
			{"Mu", "924"},
			{"Nu", "925"},
			{"Xi", "926"},
			{"Omicron", "927"},
			{"Pi", "928"},
			{"Rho", "929"},
			{"Sigma", "931"},
			{"Tau", "932"},
			{"Upsilon", "933"},
			{"Phi", "934"},
			{"Chi", "935"},
			{"Psi", "936"},
			{"Omega", "937"},
			{"alpha", "945"},
			{"beta", "946"},
			{"gamma", "947"},
			{"delta", "948"},
			{"epsilon", "949"},
			{"zeta", "950"},
			{"eta", "951"},
			{"theta", "952"},
			{"iota", "953"},
			{"kappa", "954"},
			{"lambda", "955"},
			{"mu", "956"},
			{"nu", "957"},
			{"xi", "958"},
			{"omicron", "959"},
			{"pi", "960"},
			{"rho", "961"},
			{"sigmaf", "962"},
			{"sigma", "963"},
			{"tau", "964"},
			{"upsilon", "965"},
			{"phi", "966"},
			{"chi", "967"},
			{"psi", "968"},
			{"omega", "969"},
			{"thetasym", "977"},
			{"upsih", "978"},
			{"piv", "982"},
			{"bull", "8226"},
			{"hellip", "8230"},
			{"prime", "8242"},
			{"Prime", "8243"},
			{"oline", "8254"},
			{"frasl", "8260"},
			{"weierp", "8472"},
			{"image", "8465"},
			{"real", "8476"},
			{"trade", "8482"},
			{"alefsym", "8501"},
			{"larr", "8592"},
			{"uarr", "8593"},
			{"rarr", "8594"},
			{"darr", "8595"},
			{"harr", "8596"},
			{"crarr", "8629"},
			{"lArr", "8656"},
			{"uArr", "8657"},
			{"rArr", "8658"},
			{"dArr", "8659"},
			{"hArr", "8660"},
			{"forall", "8704"},
			{"part", "8706"},
			{"exist", "8707"},
			{"empty", "8709"},
			{"nabla", "8711"},
			{"isin", "8712"},
			{"notin", "8713"},
			{"ni", "8715"},
			{"prod", "8719"},
			{"sum", "8721"},
			{"minus", "8722"},
			{"lowast", "8727"},
			{"radic", "8730"},
			{"prop", "8733"},
			{"infin", "8734"},
			{"ang", "8736"},
			{"and", "8743"},
			{"or", "8744"},
			{"cap", "8745"},
			{"cup", "8746"},
			{"int", "8747"},
			{"there4", "8756"},
			{"sim", "8764"},
			{"cong", "8773"},
			{"asymp", "8776"},
			{"ne", "8800"},
			{"equiv", "8801"},
			{"le", "8804"},
			{"ge", "8805"},
			{"sub", "8834"},
			{"sup", "8835"},
			{"nsub", "8836"},
			{"sube", "8838"},
			{"supe", "8839"},
			{"oplus", "8853"},
			{"otimes", "8855"},
			{"perp", "8869"},
			{"sdot", "8901"},
			{"lceil", "8968"},
			{"rceil", "8969"},
			{"lfloor", "8970"},
			{"rfloor", "8971"},
			{"lang", "9001"},
			{"rang", "9002"},
			{"loz", "9674"},
			{"spades", "9824"},
			{"clubs", "9827"},
			{"hearts", "9829"},
			{"diams", "9830"},
	};
	

	public static String HTMLDecode(String s) {
		int i = 0, j;
		//System.out.println("Doing entities for: [" + s + "]");
		while ((i = s.indexOf("&", i)) != -1) {
			++i;
			j = s.indexOf(";", i);
			if (j == -1)
				break;
			//System.out.println("Found entity at "+i+".."+j+"=[" + s.substring(i,j) + "]");
			if ((j - i) > 10)
				continue;
			String entity = s.substring(i, j);
			if (entity.matches("^#x?[0-9]+$")) {
				int entnum;
				if (entity.charAt(1) == 'x')
					entnum = Integer.valueOf(entity.substring(2), 16).intValue();
				else
					entnum = Integer.valueOf(entity.substring(1), 10).intValue();
				s = s.substring(0, i - 1) + (char)entnum + s.substring(j + 1);
			} else {
				boolean found = false;
				for (int q = 0; q < htmlentities.length; ++q)
					if (entity.equals(htmlentities[q][0])) {
						s = s.substring(0, i - 1) +
						(char)(Integer.valueOf(htmlentities[q][1]).intValue())
						+ s.substring(j + 1);
						found = true;
						break;
					}
				if (!found)
					s = s.substring(0, i - 1) + s.substring(j + 1);
			}
			i -= (j - i);
		}
		return s;
	}
}
