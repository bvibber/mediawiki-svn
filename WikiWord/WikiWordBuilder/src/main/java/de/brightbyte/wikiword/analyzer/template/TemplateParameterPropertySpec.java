package de.brightbyte.wikiword.analyzer.template;

import java.util.Set;

import de.brightbyte.wikiword.analyzer.WikiPage;

/**
 * Property specification for properties derived from template parameters 
 */
public interface TemplateParameterPropertySpec {
	
	/** determins a property value from a map of template parameters.
	 **/
	public Set<CharSequence> getPropertyValues(WikiPage page, TemplateData params, Set<CharSequence> intoValues);
	
	public String getPropertyName();
}