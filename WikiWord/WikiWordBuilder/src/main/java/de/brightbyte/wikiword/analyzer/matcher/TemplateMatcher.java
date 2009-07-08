/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import de.brightbyte.wikiword.analyzer.template.TemplateData;
import de.brightbyte.wikiword.analyzer.template.TemplateUser;

public interface TemplateMatcher  extends AttributeMatcher<TemplateData>, TemplateUser {
	public boolean lineMatchPassed(CharSequence t);
}