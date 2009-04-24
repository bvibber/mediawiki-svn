/**
 * 
 */
package de.brightbyte.wikiword.analyzer.template;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.AnalyzerUtils;

public class DummyTemplateUser implements TemplateUser {
	protected String pattern;
	//protected boolean anchored;
	
	public DummyTemplateUser(Matcher matcher, boolean anchored) {
		this(AnalyzerUtils.getRegularExpression(matcher.pattern(), anchored));
	}
	
	public DummyTemplateUser(Pattern pattern, boolean anchored) {
		this(AnalyzerUtils.getRegularExpression(pattern, anchored));
	}
	
	public DummyTemplateUser(String s) {
		this.pattern = s;
	}
	
	public String getTemplateNamePattern() {
		return this.pattern;
	}
}