package net.psammead.util;

import java.util.*;

import net.psammead.util.annotation.FullyStatic;

/** {@link String} utility functions */
@FullyStatic 
public final class StringUtil {
	/** function collection, shall not be instantiated */
	private StringUtil() {}
	
	/** returns an empty String for null, toString otherwise */
	public static String safeToString(Object o) {
		if (o == null)	return "";
		return o.toString();
	}
	
	//------------------------------------------------------------------------------
	
	/** escapes regexp metacharacters */
	public static String escapeRegexp(String in) {
		final int			len	= in.length();
		final StringBuilder	out	= new StringBuilder();
		for (int i=0; i<len; i++) {
			char	c	= in.charAt(i);
			switch (c) {
				case '\\':
				case '^':
				case '$':
				case '.':
				case '?':
				case '+':
				case '*':
				case '|':
				case '(':
				case ')':
				case '[':
				case ']':
				case '{':
				case '}':
					out.append('\\');
					break;
				default:
					break;
			}
			out.append(c);
		}
		return out.toString();
	}

	//------------------------------------------------------------------------------
	
	/** cut the prefix from the start of the text or returns null */
	public static String cutAtStart(String text, String prefix) {
		if (!text.startsWith(prefix))	return null;
		return text.substring(prefix.length());
	}
	
	/** cut the suffix from the start of the text or returns null */
	public static String cutAtEnd(String text, String suffix) {
		if (!text.endsWith(suffix))	return null;
		return text.substring(0,text.length()-suffix.length());
	}

	//------------------------------------------------------------------------------
	
	/** split a text at a separator char without leaving out any as String#split does */
	public static String[] split(String text, char separator) {
		final List<String>	out	= new ArrayList<String>();
		final int	len	= text.length();
		int	pos	= 0;
		for (int i=0; i<len; i++) {
			char	c	= text.charAt(i);
			if (c == separator) {
				out.add(text.substring(pos, i));
				pos	= i+1;
			}
		}
		if (pos < len) {
			out.add(text.substring(pos, len));
		}
		return out.toArray(new String[out.size()]);
	}
	
	/** splits a String into an array of Strings at every occurance of a separator */
	public static String[] split(String s, String separator) {
		final List<String>	out	= new ArrayList<String>();
		int	pos	= 0;
		for (;;) {
			pos	= s.indexOf(separator, pos);
			if (pos != -1) {
				out.add(s.substring(0, pos));
				s	= s.substring(pos+separator.length());
			}
			else {
				out.add(s);
				break;
			}

		}
		return out.toArray(new String[out.size()]);
	}
	
	/** 
	 * splits a String into an array of 2 Strings at the first occurance of a separator 
	 * returns an array of 1 String if the separator is not found
	 */
	public static String[] splitFirst(String s, String separator) {
		final int	pos	= s.indexOf(separator);
		return pos != -1
				? new String[] { s.substring(0, pos), s.substring(pos+separator.length()) }
				: new String[] { s };
	}
	
	/** 
	 * splits a String into an array of 2 Strings at the last occurance of a separator 
	 * returns an array of 1 String if the separator is not found
	 */
	public static String[] splitLast(String s, String separator) {
		final int	pos	= s.lastIndexOf(separator);
		return pos != -1
				? new String[] { s.substring(0, pos), s.substring(pos+separator.length()) }
				: new String[] { s };
	}
	
	/** join an array of Strings with a separator */
	public static String join(String[] strings, String separator) {
		final StringBuilder	out	= new StringBuilder();
		for (int i=0; i<strings.length; i++) {
			if (i != 0)	out.append(separator);
			out.append(strings[i]);
		}
		return out.toString();
	}
	
	/** join a List of Strings with a separator */
	public static String join(Iterable<String> strings, String separator) {
		final StringBuilder	out	= new StringBuilder();
		for (Iterator<String> it=strings.iterator(); it.hasNext();) {
			out.append(it.next());
			if (it.hasNext())	out.append(separator);
		}
		return out.toString();
	}
	
	//------------------------------------------------------------------------------
	
	/** create a random String from given characters */
	public static String randomString(String characters, int length) {
		final char[]		chars	= characters.toCharArray();
		final StringBuilder	out		= new StringBuilder();
		for (int i=0; i<length; i++) {
			int		index	= (int)(Math.random() * chars.length);
			char	c		= chars[index];
			out.append(c);
		}
		return out.toString();
	}
	
	//------------------------------------------------------------------------------

	/** add pad characters to the left of a string until a given length is reached */
	public static String padLeft(String s, char pad, int length) {
		final StringBuilder	out	= new StringBuilder();
		for (int i=s.length(); i<length; i++) {
			out.append(pad);
		}
		out.append(s);
		return out.toString();
	}
	
	/** add pad characters to the right of a string until a given length is reached */
	public static String padRight(String s, char pad, int length) {
		final StringBuilder	out	= new StringBuilder();
		out.append(s);
		for (int i=s.length(); i<length; i++) {
			out.append(pad);
		}
		return out.toString();
	}
	
	//------------------------------------------------------------------------------
	
	/** compute Levenshtein Distance */
	public static int levenshteinDistance(String s, String t) {
		final int	n	= s.length();
		final int	m	= t.length();
		if (n == 0)	return m;
		if (m == 0)	return n;
		
		final int	d[][]	= new int[n+1][m+1];
		for (int i=0; i<=n; i++)	d[i][0]	= i;
		for (int j=0; j<=m; j++)	d[0][j]	= j;
		
		for (int i=1; i<=n; i++) {
			final char a	= s.charAt(i-1);
			for (int j=1; j<=m; j++) {
				final char b	= t.charAt(j-1);
				d[i][j]	= minimum3(
					d[i-1][j]+1, 
					d[i][j-1]+1, 
					d[i-1][j-1] + (a == b ? 0 : 1)
				);
			}
		}
		return d[n][m];
	}
	
	/** calculate minimum of three values */
	private static int minimum3(int a, int b, int c) {
		return Math.min(a, Math.min(b, c));
	}
}
