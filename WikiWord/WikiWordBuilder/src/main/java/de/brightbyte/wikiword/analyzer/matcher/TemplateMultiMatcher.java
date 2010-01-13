/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.Arrays;

import de.brightbyte.wikiword.analyzer.template.TemplateData;

public class TemplateMultiMatcher extends AbstractAttributeMatcher<TemplateData> implements TemplateMatcher {
	protected Iterable<TemplateMatcher> matchers;
	
	public TemplateMultiMatcher(TemplateMatcher... matchers) {
		this(Arrays.asList(matchers));
	}
	
	public TemplateMultiMatcher(Iterable<TemplateMatcher> matchers) {
		if(matchers==null) throw new NullPointerException();
		this.matchers = matchers;
	}

	public boolean matches(TemplateData t) {
		for (TemplateMatcher m: matchers) {
			if (!m.matches(t)) return false;
		}
		
		return true;
	}

	public String getTemplateNamePattern() {
		StringBuilder s = new StringBuilder();
		
		for (TemplateMatcher m: matchers) {
			String p = m.getTemplateNamePattern();
			if (p==null || p.length()==0 || p.equals(".*")) continue;
			if (s.length()>0) s.append("|");
			s.append("(");
			s.append(p);
			s.append(")");
		}
		
		if (s.length()==0) s.append(".*");
		return s.toString();
	}

	public boolean lineMatchPassed(CharSequence lines) {
		for (TemplateMatcher m: matchers) {
			if (!m.lineMatchPassed(lines)) return false;
		}
		
		return true;
	}
	
	@Override
	public String toString() {
		return getClass().getName() + "(" + matchers + ")";
	}
}