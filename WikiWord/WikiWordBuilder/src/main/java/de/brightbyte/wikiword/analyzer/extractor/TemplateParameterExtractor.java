package de.brightbyte.wikiword.analyzer.extractor;

import java.util.List;
import java.util.Set;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;
import de.brightbyte.wikiword.analyzer.template.TemplateData;
import de.brightbyte.wikiword.analyzer.template.TemplateParameterPropertySpec;
import de.brightbyte.wikiword.analyzer.template.TemplateUser;

public class TemplateParameterExtractor implements PropertyExtractor, TemplateUser {
	//TODO: allow for matching templates by the parameters they contain, etc. use TemplateMatcher
	protected NameMatcher template;
	protected TemplateParameterPropertySpec[] properties;
	
	public TemplateParameterExtractor(String template, int flags, TemplateParameterPropertySpec... properties) {
		this(new ExactNameMatcher(template), properties);
	}
	
	public TemplateParameterExtractor(NameMatcher template, TemplateParameterPropertySpec... properties) {
/*			this(new TemplateNameMatcher(template), properties);
	}
	
	public TemplateParameterExtractor(TemplateMatcher template, TemplateParameterPropertySpec... properties) {
*/
		if (template==null) throw new NullPointerException();
		if (properties==null) throw new NullPointerException();
		
		this.template = template;
		this.properties = properties;
	}

	public MultiMap<String, CharSequence, Set<CharSequence>> extract(WikiPage page, MultiMap<String, CharSequence, Set<CharSequence>> into) {
		MultiMap<String, TemplateData, List<TemplateData>> tpl = page.getTemplates();
		
		for (Iterable<TemplateData> list : template.matches(tpl)) {
			for (TemplateData m: list) {
				for (TemplateParameterPropertySpec prop: properties) {

					Set<CharSequence> set = null;
					set = prop.getPropertyValues(page, m, set);

					if (set!=null) {
						if (into==null) into = new ValueSetMultiMap<String, CharSequence>();
						into.putAll(prop.getPropertyName(), set);
					}
				}
			}
		}
		
		return into;
	}

	public String getTemplateNamePattern() {
		return template.getRegularExpression();
	}
	
}
