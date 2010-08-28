package net.psammead.util;

import net.psammead.functional.Function;
import net.psammead.util.annotation.ImmutableValue;

/** 
selects Strings matching my kind of pattern
<br>
<ul>syntax
<li>search consists of substrings in any order</li>
<li>substring searches are ANDed</li>
<li>a substring can be filtered out by prepending it with a '-'</li>
<li>starting a substring with | matches it at the start</li>
<li>ending a substring with | matches it at the end</li>
<li>an all lowercase substring is matched case insensitive</li>
</ul>
*/
@ImmutableValue 
public final class GooglishFilter implements Function<String,Boolean> {
	//Function<String,Function<String,Boolean>>	googlish		= Functions.<String,Function<String,Boolean>>vary(_Googlish._new_String());
	public static Function<String,Boolean> predicate(String pattern) {
		return new GooglishFilter(pattern);
	}
		
	private final String	pattern;
	private final Token[]	tokens;
	
	/** create a Googlish for a specific pattern */
	public GooglishFilter(String pattern) {
		this.pattern	= pattern;
		tokens	= parseTokens(pattern);
	}
	
	/** returns the pattern used for matches */
	public String getPattern() {
		return pattern;
	}
	
	/** match a single Choice against the Pattern */
	public Boolean apply(String str) {
		final String	lowered	= str.toLowerCase();
		for (int i=0; i<tokens.length; i++) {
			final Token		token	= tokens[i];
			final String	text	= token.low ? lowered : str;
			final boolean found	= 	token.start ? text.startsWith(token.text)	:
									token.end   ? text.endsWith(token.text)		:
								/* else */    text.indexOf(token.text) != -1;
			if (found == token.negate)	return false;
		}
		return true;
	}
	
	//------------------------------------------------------------------------------
	
	/** one token in the pattern */
	private static class Token {
		boolean	negate;	// when matching should be inverted
		boolean	low;	// when upper/lowercase makes no difference
		boolean	start;	// whether it should be matched at the start
		boolean	end;	// whether it should be martched at the end
		String	text;	// the substring to match
	}
	
	/** parse an array of Tokens from a pattern */
	private Token[] parseTokens(String pattern) {
		final String[]	descriptors	= pattern.trim().replaceAll(" +", " ").split(" ");
		final Token[]	out			= new Token[descriptors.length];
		for (int i=0; i<descriptors.length; i++) {
			out[i]	= parseToken(descriptors[i]);
		}
		return out;
	}
	
	/** parse a Token from a descriptor */
	private Token parseToken(String descriptor) {
		final Token	token	= new Token();
		
		// '-' at the start means logical not
		token.negate	= descriptor.startsWith("-") && descriptor.length() > 1;
		if (token.negate)	descriptor	= descriptor.substring(1);
		
		// '|' at the start means matching at the start
		token.start		= descriptor.startsWith("|") && descriptor.length() > 1;
		if (token.start)	descriptor	= descriptor.substring(1);
		
		// '|' at the end means matching at the end
		token.end		= descriptor.endsWith("|") && descriptor.length() > 1;
		if (token.end)	descriptor	= descriptor.substring(0, descriptor.length() - 1);
		
		// all lowercase means matching case insensitive
		token.low	= descriptor.equals(descriptor.toLowerCase());
		
		// the rest ist the text to be matched
		token.text	= descriptor;
		
		return token;
	}
}
