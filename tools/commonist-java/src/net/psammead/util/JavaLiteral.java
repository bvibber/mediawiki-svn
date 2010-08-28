package net.psammead.util;

import net.psammead.util.annotation.FullyStatic;

/** utility functions for java literals */
@FullyStatic 
public final class JavaLiteral {
	/** function collection, shall not be instantiated */
	private JavaLiteral() {}
	
	//-------------------------------------------------------------------------
	//## String
	
	/** encodes a String into a java literal */
	public static String encodeString(String in) {
		final StringBuilder	out	= new StringBuilder();
		out.append('"');
		
		final int	len	= in.length();
		for (int i=0; i<len; i++) {
			char	c	= in.charAt(i);
				 if (c == '"')				out.append("\\\"");
			else if (c == '\\')				out.append("\\\\");
			else if (c >= 32 && c < 127)	out.append(c);
			else if (c == '\r')				out.append("\\r");
			else if (c == '\n')				out.append("\\n");
			else if (c == '\t')				out.append("\\t");
			else if (c == '\f')				out.append("\\f");
			else if (c == '\b')				out.append("\\b");
			else {
				out.append("\\u");
				final String	hex	= Integer.toHexString(c);
				final int		hl	= hex.length();
				for (int j=hl; j<4; j++)	out.append('0');
				out.append(hex);
			}
		}

		out.append('"');
		return out.toString();
	}
	
	/** inverse of encodeString */
	public static String decodeString(String in) {
		if (in.length() < 2)	throw new IllegalArgumentException("too short"); 
		if (!in.startsWith("\""))	throw new IllegalArgumentException("does not start with a double quote");
		if (!in.endsWith("\""))		throw new IllegalArgumentException("does not end with a double quote");
		in	= in.substring(1, in.length()-1);
		
		final StringBuilder	out	= new StringBuilder();
		final int			len	= in.length();
		int		i	= 0;
		char	val;
		for (;;) {
			if (i >= len)	break;
			final char	c1	= in.charAt(i);
			i++;
			if (c1 == '\\') {
				if (i >= len)	throw new IllegalArgumentException("unfinished escape sequence");
				final char	c2	= in.charAt(i);
				i++;	
				switch (c2) {
					case '\\':	val	= '\\';	break;
					case 't':	val	= '\t';	break;
					case 'r':	val	= '\r';	break;
					case 'n':	val	= '\n';	break;
					case 'b':	val	= '\b';	break;
					case 'f':	val	= '\f';	break;
					case 'u':
						if (i+4 > len)	throw new IllegalArgumentException("expected 4 hexdigits");
						final String	c3	= in.substring(i,i+4);
						i += 4;
						val	= (char)Integer.parseInt(c3);
						break;
					default:	val	= c2;	break;
				}
			}
			else {
				val	= c1;
			}
			out.append(val);
		}
		return out.toString();
	}
	
	//------------------------------------------------------------------------------
	//## char
	
	/** encodes a char into a java literal */
	public static String encodeChar(char in) {
		final StringBuilder	out	= new StringBuilder();
		out.append('\'');
		
			 if (in == '\'')				out.append("\\'");
		else if (in == '\\')				out.append("\\\\");
		else if (in >= 32 && in < 127)	out.append(in);
		else if (in == '\r')				out.append("\\r");
		else if (in == '\n')				out.append("\\n");
		else if (in == '\t')				out.append("\\t");
		else if (in == '\f')				out.append("\\f");
		else if (in == '\b')				out.append("\\b");
		else {
			out.append("\\u");
			final String	hex	= Integer.toHexString(in);
			int		hl	= hex.length();
			for (int j=hl; j<4; j++)	out.append('0');
			out.append(hex);
		}
		 
		out.append('\'');
		return out.toString();
	}
	
	/** inverse of encodeChar */
	public static char decodeChar(String in) {
		if (in.length() < 3)		throw new IllegalArgumentException("too short");
		if (!in.startsWith("'"))	throw new IllegalArgumentException("does not start with a single quote");
		if (!in.endsWith("'"))		throw new IllegalArgumentException("does not end with a single quote");
		
		if (in.charAt(1) == '\\') {
			final char	desc	= in.charAt(2);
			if (desc == 'u') {
				if (in.length() != 7)	throw new IllegalArgumentException("not a single character");
				final String	hex	= in.substring(2, in.length()-1);
				return (char)Integer.parseInt(hex);
			}
			else {
				if (in.length() != 4)	throw new IllegalArgumentException("not a single character");
				switch (desc) {
					case '\\':	return '\\';
					case 't':	return '\t';
					case 'r':	return '\r';
					case 'n':	return '\n';
					case 'b':	return '\b';
					case 'f':	return '\f';
					default:	throw new IllegalArgumentException("not a single character");
				}
			}
		}
		else {
			if (in.length() != 3)		throw new IllegalArgumentException("not a single character");
			return in.charAt(1);
		}
	}
}
