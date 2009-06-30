/**
 * 
 */
package de.brightbyte.wikiword.analyzer.sensor;

import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor;
import de.brightbyte.wikiword.analyzer.template.TemplateUser;
import de.brightbyte.wikiword.analyzer.template.TemplateExtractor.TemplateData;

public class HasTemplateLikeSensor<V> extends AbstractSensor<V> implements TemplateUser {
	protected NameMatcher matcher;
	protected Map<String, NameMatcher> params;
	
	//TODO: provide an OR mode, so this triggers if *any* param matches
	
	public HasTemplateLikeSensor(V value, String pattern, int flags) {
		this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false), null);
	}
	
	public HasTemplateLikeSensor(V value, String pattern, int flags, String[] params) {
		this(value, new PatternNameMatcher(pattern, flags | Pattern.MULTILINE, false), HasTemplateLikeSensor.<NameMatcher>paramKeyMap(params));
	}
	
	public HasTemplateLikeSensor(V value, NameMatcher matcher, Map<String, NameMatcher> params) {
		super(value);
		this.matcher = matcher;
	}

	@Override
	public boolean sense(WikiPage page) {
		if (params==null) {
			CharSequence t = page.getTemplatesString();
			return matcher.matchesLine(t.toString());
		}
		
		MultiMap<String, TemplateData, List<TemplateData>> templates = page.getTemplates();
		if (templates.size()==0) return false;
		
		templateLoop : for (List<TemplateExtractor.TemplateData> ll: matcher.matches(templates)) {
				if (params==null) return true;
			
				for (TemplateExtractor.TemplateData tpl: ll) {
					for (Map.Entry<String, NameMatcher> f: params.entrySet()) {
						String key = f.getKey();
						NameMatcher valueMatcher = f.getValue();
						
						CharSequence value = tpl.getParameter(key);
						if (value!=null) {
							if (valueMatcher==null) ;
							else {
								if (valueMatcher.matches(value)) ;
								else continue templateLoop;
							}
						}
						else continue templateLoop;
					}
					
					return true;
				}
		}
		
		return false;
	}

	public String getTemplateNamePattern() {
		return matcher.getRegularExpression();
	}
	
	protected static <V> Map<String, V> paramKeyMap(String[] params) {
		if (params==null) return null;
		
		HashMap<String, V> m = new HashMap<String, V>();
		for (String p: params) {
			m.put(p, null);
		}
		
		return m;
	}
	
	
}