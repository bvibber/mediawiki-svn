/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;

public class HasSectionLikeSensor<V> extends AbstractSensor<V> {
	protected NameMatcher matcher;
	
	public HasSectionLikeSensor(V value, String pattern, int flags) {
		this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false));
	}
	
	public HasSectionLikeSensor(V value, NameMatcher matcher) {
		super(value);
		this.matcher = matcher;
	}

	@Override
	public boolean sense(WikiPage page) {
		String sections = page.getSectionsString();
		return matcher.matchesLine(sections);
	}
	
	public String toString() {
		return getClass().getName() + "(" + matcher.toString() + ")";
	}
}