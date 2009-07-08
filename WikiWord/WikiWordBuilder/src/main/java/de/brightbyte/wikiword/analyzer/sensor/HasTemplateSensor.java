/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.Map;

import de.brightbyte.wikiword.analyzer.matcher.AnyNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;

public class HasTemplateSensor<V> extends HasTemplateLikeSensor<V> {
	
	public HasTemplateSensor(V value, String name) {
		this(value, name==null ? AnyNameMatcher.instance : new ExactNameMatcher(name), null);
	}
	
	public HasTemplateSensor(V value, String name, String... params) {
		this(value, name==null ? AnyNameMatcher.instance : new ExactNameMatcher(name), HasTemplateLikeSensor.<NameMatcher>paramKeyMap(params));
	}
	
	private HasTemplateSensor(V value, NameMatcher matcher, Map<String, NameMatcher> params) {
		super(value, matcher, params);
	}
}