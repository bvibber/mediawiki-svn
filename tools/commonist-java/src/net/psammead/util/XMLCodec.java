package net.psammead.util;

import net.psammead.util.annotation.FullyStatic;

/** static utility class to encode XML entities */
@FullyStatic 
public final class XMLCodec {
	/** function collection, shall not be instantiated */
	private XMLCodec() {}
	
	/** encode minimum XML entities */
	public static String encode(String s, boolean quot, boolean apos) {	
		final StringBuilder	out	= new StringBuilder();
		final int			len	= s.length();
		for (int i=0; i<len; i++) {
			final char c = s.charAt(i);
			switch (c) {
				case '&':	out.append("&amp;");				break;
				case '<':	out.append("&lt;");					break;
				case '>':	out.append("&gt;");					break;
				case '"':	out.append(quot ? "&quot;" : c);	break;
				case '\'':	out.append(apos ? "&apos;" : c);	break; 
				default:	out.append(c);
			}
		}
		return out.toString();
	}
	
	/** decode minimum XML entities suitable */
	public static String decode(String s, boolean quot, boolean apos) throws IllegalArgumentException {
		final Scan			tok	= new Scan(s);
		final StringBuilder	out	= new StringBuilder();
		for (;;) {
			if (tok.isFinished())		break;
										out.append(tok.scan("&"));
			if (tok.isFinished())		break;
				 if (tok.is("&amp;"))			out.append('&');
			else if (tok.is("&lt;"))			out.append('<'); 
			else if (tok.is("&gt;"))			out.append('>');
			else if (quot && tok.is("&quot;"))	out.append('"');
			else if (apos && tok.is("&apos;"))	out.append('\'');
			else if (tok.is("&"))				out.append('&');
		}
		return out.toString();
	}
}
