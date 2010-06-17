package de.brightbyte.wikiword.analyzer.extractor;

import java.util.List;
import java.util.Set;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.wikiword.analyzer.AnalyzerUtils;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.mangler.Mangler;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;
import de.brightbyte.wikiword.analyzer.template.TemplateData;
import de.brightbyte.wikiword.analyzer.template.TemplateUser;

public class TemplateParameterValueExtractor implements ValueExtractor, TemplateUser {
	//TODO: allow for matching templates by the parameters they contain, etc. use TemplateMatcher
	protected NameMatcher template;
	protected String parameter;
	protected String prefix = null;
	protected Mangler mangler = null;
	
	public TemplateParameterValueExtractor(String template, int flags, String parameter) {
		this(new PatternNameMatcher(template, flags, true), parameter);
	}
	
	public TemplateParameterValueExtractor(NameMatcher template, String parameter) {
/*			this(new TemplateNameMatcher(template), properties);
	}
	
	public TemplateParameterExtractor(TemplateMatcher template, TemplateParameterPropertySpec... properties) {
*/
		if (template==null) throw new NullPointerException();
		if (parameter==null) throw new NullPointerException();
		
		this.template = template;
		this.parameter = parameter;
	}

	public TemplateParameterValueExtractor setManger(Mangler m) {
		mangler = m;
		return this;
	}
	
	public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
		MultiMap<String, TemplateData, List<TemplateData>> tpl = page.getTemplates();
		
		for (Iterable<TemplateData> list : template.matches(tpl)) {
			for (TemplateData m: list) {
				CharSequence v = m.getParameter(parameter);
				if (prefix!=null) v = prefix+v;
				if (v!=null) {
					if (mangler!=null) v = mangler.mangle(v);
					into = AnalyzerUtils.addToSet(into, v);
				}
			}
		}
		
		return into;
	}

	public String getTemplateNamePattern() {
		return template.getRegularExpression();
	}

	public ValueExtractor setPrefix(String prefix) {
		this.prefix = prefix;
		return this;
	}

}
