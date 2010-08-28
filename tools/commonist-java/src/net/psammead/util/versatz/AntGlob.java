package net.psammead.util.versatz;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.regex.Pattern;

public final class AntGlob {
	public static void main(String[] args) throws IOException {
		String	line	= new BufferedReader(new InputStreamReader(System.in)).readLine();
		System.out.println(compile(line, false));
	}
	
	private AntGlob() {}
	
	public static Pattern compile(String path, boolean caseSensitive) {
		final StringBuilder	b	= new StringBuilder();
		int	pos	= 0;
		while (pos < path.length()) {
			final char	here	= path.charAt(pos);
			if (here == '?') {
				b.append("[^/]");
			}
			else if (here == '*') {
				final int pos2	= pos+1;
				if (pos2 < path.length()-1 && path.charAt(pos2) == '*') {
					b.append(".*");
					pos	= pos2;
				}
				else {
					b.append("[^/]*");
				}
			}
			else b.append(here);
			pos++;
		}
		
		return Pattern.compile(
				"^" + b.toString() + "$", 
				Pattern.DOTALL | (caseSensitive ? 0 : 
						Pattern.CASE_INSENSITIVE | Pattern.UNICODE_CASE | Pattern.CANON_EQ));
	}
}
