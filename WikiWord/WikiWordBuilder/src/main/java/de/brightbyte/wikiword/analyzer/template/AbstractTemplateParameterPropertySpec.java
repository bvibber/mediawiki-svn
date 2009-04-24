/**
 * 
 */
package de.brightbyte.wikiword.analyzer.template;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.wikiword.analyzer.WikiPage;

public abstract class AbstractTemplateParameterPropertySpec implements TemplateParameterPropertySpec {
	protected String propertyName;
	
	public AbstractTemplateParameterPropertySpec(String name) {
		if (name==null) throw new NullPointerException();
		this.propertyName = name;
	}
	
	public String getPropertyName() {
		return propertyName;
	}

	public Set<CharSequence> getPropertyValues(WikiPage page, TemplateExtractor.TemplateData params, Set<CharSequence> values) {
		CharSequence v = getPropertyValue(page, params); 
		if (v==null) return values;
		
		if (values==null) values = new HashSet<CharSequence>();
		values.add(v);
		
		return values;
	}

	protected abstract CharSequence getPropertyValue(WikiPage page, TemplateExtractor.TemplateData params);
	
}