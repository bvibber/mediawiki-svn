/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;

public class HasCategoryLikeSensor<V> extends AbstractSensor<V> {
	//protected Pattern pattern;
	protected NameMatcher matcher; 
	
	public HasCategoryLikeSensor(V value, String pattern, int flags) {
		this(value, pattern, flags, false);
	}
	
	public HasCategoryLikeSensor(V value, String pattern, int flags, boolean anchored) {
		this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, anchored));
	}
	
	public HasCategoryLikeSensor(V value, NameMatcher matcher) {
		super(value);
		this.matcher = matcher;
	}

	@Override
	public boolean sense(WikiPage page) {
		String categories = page.getCategoriesString();
		return matcher.matchesLine(categories);
	}
	
	public String toString() {
		return getClass().getName() + "(" + matcher.toString() + ")";
	}
}