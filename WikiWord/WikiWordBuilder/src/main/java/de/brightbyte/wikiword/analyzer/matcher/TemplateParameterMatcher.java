/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.wikiword.analyzer.template.TemplateData;

public class TemplateParameterMatcher extends AbstractAttributeMatcher<TemplateData> implements TemplateMatcher {
	protected Map<String, NameMatcher> params;

	protected static Map<String, NameMatcher> paramKeyMap(String[] params) {
		if (params==null) return null;
		if (params.length==0) return null;
		
		HashMap<String, NameMatcher> m = new HashMap<String, NameMatcher>();
		for (String p: params) {
			m.put(p, null);
		}
		
		return m;
	}
	
	public TemplateParameterMatcher(String... params) {
		this(paramKeyMap(params));
	}

	public TemplateParameterMatcher(Map<String, NameMatcher> params) {
		if(params==null) throw new NullPointerException();
		this.params = params;
	}

	public boolean matches(TemplateData t) {
		for (Map.Entry<String, NameMatcher> f: params.entrySet()) {
			String key = f.getKey();
			NameMatcher valueMatcher = f.getValue();
			
			CharSequence value = t.getParameter(key);
			if (value!=null) {
				if (valueMatcher==null) ;
				else {
					if (valueMatcher.matches(value)) ;
					else return false;
				}
			}
			else return false;
		}
		
		return true;
	}

	public String getTemplateNamePattern() {
		return ".*";
	}

	public boolean lineMatchPassed(CharSequence lines) {
		return true;
	}
	
	@Override
	public String toString() {
		return getClass().getName() + "(" + params.toString() + ")";
	}

}