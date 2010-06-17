/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.template.TemplateData;

public class TemplateNameMatcher extends AbstractAttributeMatcher<TemplateData> implements TemplateMatcher {
	protected NameMatcher matcher;
	protected boolean lineMatchSupported = true;
	
	public TemplateNameMatcher(String name) {
		this(new ExactNameMatcher(name));
	}
	
	public TemplateNameMatcher(String pattern, int flags, boolean anchored) {
		this(new PatternNameMatcher(pattern, flags, anchored));
	}
	
	public TemplateNameMatcher(Pattern pattern) {
		this(new PatternNameMatcher(pattern));
	}
	
	public TemplateNameMatcher(NameMatcher matcher) {
		if(matcher==null) throw new NullPointerException();
		this.matcher = matcher;
	}

	public boolean matches(TemplateData t) {
		return matcher.matches(t.getName());
	}

	public String getTemplateNamePattern() {
		return matcher.getRegularExpression();
	}

	public boolean lineMatchPassed(CharSequence lines) {
		try {
			return !lineMatchSupported || matcher.matchesLine(lines.toString());
		}  catch (UnsupportedOperationException ex) {
			lineMatchSupported = false;
			return true;
		}
	}
		
	@Override
	public String toString() {
		return getClass().getName() + "(" + matcher.toString() + ")";
	}
	
}