package de.brightbyte.wikiword.analyzer.mangler;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * An implementation of the Mangler interface based on regular expressions.
 */
public class RegularExpressionMangler implements Mangler {
	//protected Pattern pattern;
	protected Matcher matcher;
	protected String replacement;
	
	public RegularExpressionMangler(String pattern, String replacement, int flags) {
		this(Pattern.compile(pattern, flags), replacement);
	}
	
	public RegularExpressionMangler(Pattern pattern, String replacement) {
		super();
		if (pattern.pattern().equals("") || pattern.pattern().equals("[]")) throw new IllegalArgumentException("empty pattern");
		this.matcher = pattern.matcher("");
		this.replacement = replacement;
	}

	public String toString() {
		return matcher.pattern().pattern().toString()+" -> \""+replacement+"\"";
	}
	
	public CharSequence mangle(CharSequence text) {
		matcher.reset(text);
		return matcher.replaceAll(replacement);
	}
}