/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import de.brightbyte.wikiword.analyzer.template.TemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor.TemplateData;

public class TemplateNameMatcher extends AbstractAttributeMatcher<TemplateExtractor.TemplateData> implements TemplateMatcher {
	protected NameMatcher matcher;
	
	public TemplateNameMatcher(NameMatcher matcher) {
		if(matcher==null) throw new NullPointerException();
		this.matcher = matcher;
	}

	public boolean matches(TemplateData t) {
		return false;
	}
}