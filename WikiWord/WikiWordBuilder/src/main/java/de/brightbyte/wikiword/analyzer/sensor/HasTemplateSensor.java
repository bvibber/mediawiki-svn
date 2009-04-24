/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.Map;

import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;

public class HasTemplateSensor<V> extends HasTemplateLikeSensor<V> {
	
	public HasTemplateSensor(V value, String pattern) {
		this(value, new ExactNameMatcher(pattern), null);
	}
	
	public HasTemplateSensor(V value, String pattern, String[] params) {
		this(value, new ExactNameMatcher(pattern), HasTemplateLikeSensor.<NameMatcher>paramKeyMap(params));
	}
	
	private HasTemplateSensor(V value, NameMatcher matcher, Map<String, NameMatcher> params) {
		super(value, matcher, params);
	}
}