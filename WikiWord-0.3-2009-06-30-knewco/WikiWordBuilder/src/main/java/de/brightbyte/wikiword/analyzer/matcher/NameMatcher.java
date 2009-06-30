/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;


public interface NameMatcher extends AttributeMatcher<CharSequence> {
	public boolean matchesLine(String lines);
	
	public String getRegularExpression();
}