package net.psammead.util;

import java.util.*;

import net.psammead.util.annotation.FullyStatic;

/** static text utilities */
@FullyStatic 
public final class TextUtil {
	/** function collection, shall not be instantiated */
	private TextUtil() {}
	
	/** prepends every line of a text with a tab */
	public static String indent(String s) {
		return s.replaceAll("(?m)^", "\t");
	}
	
	//------------------------------------------------------------------------------
	//## line endings
	
	public static final String MAC_LF		= "\r";
	public static final String UNIX_LF		= "\n";
	public static final String DOS_LF		= "\r\n";

	/** converts a String to macintosh linefeeds */
	public static String macLF(String text) {
		return convertLF(text, MAC_LF);
	}

	/** converts a String to unix linefeeds */
	public static String unixLF(String text) {
		return convertLF(text, UNIX_LF);
	}

	/** converts a String to dos linefeeds */
	public static String dosLF(String text) {
		return convertLF(text, DOS_LF);
	}

	/** converts a Strings linefeeds */
	public static String convertLF(String text, String lf) {
		final StringBuilder	b	= new StringBuilder();
		final Scan			t	= new Scan(text);
		while (!t.isFinished()) {
			b.append(t.line());
			if (t.eol()) b.append(lf);
		}
		return b.toString();
	}
	
	/** removes linefeeds from both ends of a string */
	public static String trimLF(String s) {
		return s.replaceAll("^\\n+", "").replaceAll("\\n+$", "");
	}

	//------------------------------------------------------------------------------
	//## trimming
	
	/** trims all whitespace from both ends */
	public static String trim(String text) {
		final int	length	= text.length();

		int	start;
		for (start=0; start<length; start++) {
			char	c	= text.charAt(start);
			if (!Character.isSpaceChar(c))	break;
		}

		int	end;
		for (end=length-1; end>start; end--) {
			char	c	= text.charAt(end);
			if (!Character.isSpaceChar(c))	break;
		}
		
		return text.substring(start, end+1);
	}

	//------------------------------------------------------------------------------
	//## tabs
	
	/** expands tabs in a LF-separated string. width may not be greater than SPACES.length() */
	public static String expandTabs(String s, int width) {
		final StringBuilder	out	= new StringBuilder();
		int pos	= 0;
		int	next;
		String	line;
		for (;;) {
			next	= s.indexOf("\n", pos);
			if (next == -1)	break;
			line	= s.substring(pos, next);
			pos		= next + 1;
			expandTabLine(line, width, out);
			out.append("\n");
		}
		next	= s.length();
		line	= s.substring(pos, next);
		expandTabLine(line, width, out);
		return out.toString();
	}

	/** expands tabs in a single line. width may not be greater than SPACES.length() */
	public static String expandTabLine(String s, int width) {
		final StringBuilder	out	= new StringBuilder();
		expandTabLine(s, width, out);
		return out.toString();
	}

	/** expands tabs in a single line. width may not be greater than SPACES.length() */
	public static void expandTabLine(String s, int width, StringBuilder out) {
		int column	= 0;
		int	spaces;
		int pos		= 0;
		int	next;
		for (;;) {
			next	= s.indexOf("\t", pos);
			if (next == -1)	break;
			out.append(s.substring(pos, next));
			column	+= next - pos;
			spaces	= width - (column % width);
			for (int i=0; i<spaces; i++) {
				out.append(' ');
			}
			column	+= spaces;
			pos = next + 1;
		}
		next	= s.length();
		out.append(s.substring(pos, next));
	}
	
	//------------------------------------------------------------------------------
	//## formatting
	
	/** word wrapping, knows nothing about tabs and expects line to end with "\r", "\n" or "\r\n"
	  * @param text a text that needs word wrapping
	  * @param width thr maximum number of characters a line may have
	  */
	public static String wordWrap(String text, int width) {
		String          normalized  = text.replaceAll("\r\n", "\n").replaceAll("\r", "\n");
		StringTokenizer tokenizer   = new StringTokenizer(normalized, " \n", true);
		StringBuilder   out         = new StringBuilder();
		int             pos         = 0;
		String          space       = "";
		while (tokenizer.hasMoreTokens()) {
			String  token   = tokenizer.nextToken();
	
			// linefeeds are left as they are
			if ("\n".equals(token)) {
				out.append('\n');
				pos     = 0;
				space   = "";
				continue;
			}
	
			// spaces are gathered
			if (" ".equals(token)) {
				space   += token;
				continue;
			}
	
			// when space and next word fit in the line, both are appended
			int maybe   = pos + space.length() + token.length();
			if (maybe <= width) {
				out.append(space);
				out.append(token);
				pos     = maybe;
				space   = "";
				continue;
			}
	
			// if the word does not fit a line, it is broken up
			while (token.length() > width) {
				out.append('\n');
				out.append(token.substring(0, width));
				token   = token.substring(width);
			}
			
			// put the word in the next line
			out.append('\n');
			out.append(token);
			pos		= token.length();
			space   = "";
		}
		return out.toString();
	}
}
