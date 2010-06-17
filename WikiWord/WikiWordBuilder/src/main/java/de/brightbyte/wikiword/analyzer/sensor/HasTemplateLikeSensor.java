/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.List;
import java.util.Map;

import de.brightbyte.data.MultiMap;
import de.brightbyte.util.CollectionUtils;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.TemplateMatcher;
import de.brightbyte.wikiword.analyzer.matcher.TemplateMultiMatcher;
import de.brightbyte.wikiword.analyzer.matcher.TemplateNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.TemplateParameterMatcher;
import de.brightbyte.wikiword.analyzer.template.TemplateData;
import de.brightbyte.wikiword.analyzer.template.TemplateUser;

public class HasTemplateLikeSensor<V> extends AbstractSensor<V> implements TemplateUser {
	protected TemplateMatcher matcher;
	
	public HasTemplateLikeSensor(V value, String pattern, int flags) {
		this(value, pattern, flags, false);
	}
	
	public HasTemplateLikeSensor(V value, String pattern, int flags, boolean anchored) {
		this(value, new TemplateNameMatcher(pattern, flags, anchored));
	}
	
	public HasTemplateLikeSensor(V value, Map<String, NameMatcher> params) {
		this(value, new TemplateParameterMatcher(params));
	}
	
	public HasTemplateLikeSensor(V value, String pattern, int flags, String... params) {
		this(value, pattern==null ? null : new TemplateNameMatcher(pattern, flags, false), params==null || params.length==0 ? null : new TemplateParameterMatcher(params));
	}
	
	public HasTemplateLikeSensor(V value, String pattern, int flags, Map<String, NameMatcher> params) {
		this(value, pattern==null ? null : new TemplateNameMatcher(pattern, flags, false), params==null || params.size()==0 ? null : new TemplateParameterMatcher(params));
	}
	
	public HasTemplateLikeSensor(V value, TemplateMatcher... matchers) {
		super(value);
		if (matchers==null) throw new NullPointerException();
		
		matchers = CollectionUtils.toCleanArray(matchers, TemplateMatcher.class, false, false);
		
		if (matchers.length==0) throw new IllegalArgumentException("at least one TemplateMatcher must be provided");
		if (matchers.length==1) this.matcher = matchers[0];
		else this.matcher = new TemplateMultiMatcher(matchers);
	}

	@Override
	public boolean sense(WikiPage page) {
		CharSequence t = page.getTemplatesString();
		if (!matcher.lineMatchPassed(t)) return false;
		
		MultiMap<String, TemplateData, List<TemplateData>> templates = page.getTemplates();
		if (templates.size()==0) return false;
		
		for (List<TemplateData> ll: templates.values()) {
				for (TemplateData td: ll) {
					if (matcher.matches(td)) return true;
				}
		}
		
		return false;
	}

	public String getTemplateNamePattern() {
		return matcher.getTemplateNamePattern();
	}
	
	public String toString() {
		return getClass().getName() + "(" + matcher.toString() + ")";
	}
}