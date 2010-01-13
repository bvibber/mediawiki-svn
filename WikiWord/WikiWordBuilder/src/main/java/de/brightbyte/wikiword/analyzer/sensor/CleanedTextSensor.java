/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;

public class CleanedTextSensor<V> extends AbstractSensor<V> {
	protected NameMatcher matcher;
	
	public CleanedTextSensor(V value, String pattern, int flags) {
		this(value, new PatternNameMatcher(pattern, flags, false));
	}
	
	public CleanedTextSensor(V value, NameMatcher matcher) {
		super(value);
		this.matcher = matcher;
	}

	@Override
	public boolean sense(WikiPage page) {
		return matcher.matches( page.getCleanedText(true) );
	}
	
	public String toString() {
		return getClass().getName() + "(" + matcher.toString() + ")";
	}
}