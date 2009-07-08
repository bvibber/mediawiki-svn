/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.TemplateMatcher;
import de.brightbyte.wikiword.analyzer.matcher.TemplateNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.TemplateParameterMatcher;

public class HasTemplateSensor<V> extends HasTemplateLikeSensor<V> {
	
	public HasTemplateSensor(V value, String pattern, int flags) {
		this(value, new TemplateNameMatcher(pattern, flags, false));
	}
	
	public HasTemplateSensor(V value, Map<String, NameMatcher> params) {
		this(value, new TemplateParameterMatcher(params));
	}
	
	public HasTemplateSensor(V value, String name, String... params) {
		this(value, name==null ? null : new TemplateNameMatcher(name), params==null || params.length==0 ? null : new TemplateParameterMatcher(params));
	}
	
	public HasTemplateSensor(V value, String name, String param, NameMatcher paramMatcher) {
		this(value, name, makeParamMatcherMap(param, paramMatcher));
	}
	
	private static HashMap<String, NameMatcher> makeParamMatcherMap(String param, NameMatcher paramMatcher) {
		HashMap<String, NameMatcher> m = new HashMap<String, NameMatcher>();
		m.put(param, paramMatcher);
		return m;
	}
	
	public HasTemplateSensor(V value, String name, Map<String, NameMatcher> params) {
		this(value, name==null ? null : new TemplateNameMatcher(name), params==null || params.size()==0 ? null : new TemplateParameterMatcher(params));
	}

	public HasTemplateSensor(V value, TemplateMatcher... matchers) {
		super(value, matchers);
	}
}