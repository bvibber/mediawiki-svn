/**
 * 
 */
package de.brightbyte.wikiword.analyzer.template;

import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.AnalyzerUtils;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.mangler.Mangler;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.matcher.NameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;

public class DefaultTemplateParameterPropertySpec implements TemplateParameterPropertySpec {
	protected Mangler[] norm = null;
	protected Mangler[] clean = null;
	protected Matcher find = null;
	protected Matcher split = null;
	
	protected NameMatcher cond = null;
	
	protected boolean upper = false;
	protected boolean strip = false;
	
	protected String property;
	protected String parameter;
	
	protected String prefix = null;
	protected String suffix = null;
	
	public DefaultTemplateParameterPropertySpec(String param, String prop) {
		if (param==null) throw new NullPointerException();
		if (prop==null) throw new NullPointerException();
		
		this.parameter = param;
		this.property = prop;
	}
	
	public DefaultTemplateParameterPropertySpec addCleanup(Pattern pattern, String rep) {
		return addCleanup(new RegularExpressionMangler(pattern, rep));
	}
	
	public DefaultTemplateParameterPropertySpec addCleanup(Mangler clean) {
		if (clean==null) return this;
		
		this.clean = AnalyzerUtils.append(this.clean, clean, Mangler.class);
		return this;
	}
	
	public DefaultTemplateParameterPropertySpec setCondition(NameMatcher cond) {
		this.cond = cond;
		return this;
	}
	
	public DefaultTemplateParameterPropertySpec setCondition(String p, int flags, boolean anchored) {
		return setCondition( new PatternNameMatcher(p, flags, anchored));
	}
	
	public DefaultTemplateParameterPropertySpec addNormalizer(Pattern pattern, String rep) {
		return addNormalizer(new RegularExpressionMangler(pattern, rep));
	}
	
	public DefaultTemplateParameterPropertySpec addNormalizer(Mangler norm) {
		if (norm==null) return this;
		
		this.norm = AnalyzerUtils.append(this.norm, norm, Mangler.class);
		return this;
	}

	public DefaultTemplateParameterPropertySpec setSplitPattern(Pattern split) {
		this.split = split==null ? null : split.matcher("");
		return this;
	}

	public DefaultTemplateParameterPropertySpec setFindPattern(Pattern find) {
		this.find = find==null ? null : find.matcher("");
		return this;
	}

	public DefaultTemplateParameterPropertySpec setToUpperCase(boolean upper) {
		this.upper = upper;
		return this;
	}

	public DefaultTemplateParameterPropertySpec setStripMarkup(boolean strip) {
		this.strip = strip;
		return this;
	}

	public DefaultTemplateParameterPropertySpec setPrefix(String prefix) {
		this.prefix = prefix;
		return this;
	}

	public DefaultTemplateParameterPropertySpec setSuffix(String suffix) {
		this.suffix = suffix;
		return this;
	}

	public Set<CharSequence> getPropertyValues(WikiPage page, TemplateData params, Set<CharSequence> intoValues) {
		CharSequence v = params.getParameter(parameter);
		if (v==null) return intoValues;
		if (v.length()==0) return intoValues;
		
		if (clean!=null) {
			for (Mangler m: clean) v = m.mangle(v);
		}
		
		if (cond!=null) {
			if (!cond.matches(v)) return intoValues;
		}
		
		if (split!=null) {
			int i = 0;
			int j = 0;
			
			split.reset(v);
			boolean done = false;
			
			while (i<v.length()) {
				if (split.find(i)) {
					j = split.start();
				}
				else {
					j = v.length();
					done = true;
				}
				
				CharSequence w = v.subSequence(i, j);
				
				if (done) i = j;
				else i = split.end();
				
				intoValues = addValue(w, page, intoValues);
			}
		}
		else if (find!=null) {
			find.reset(v);
			while (find.find()) {
				CharSequence w = find.groupCount() > 0 ? find.group(1) : find.group();
				
				intoValues = addValue(w, page, intoValues);
			}
		}
		else if (split==null) {
			intoValues = addValue(v, page, intoValues);
		}
		
		return intoValues;
	}
	
	protected Set<CharSequence> addValue(CharSequence w, WikiPage page, Set<CharSequence> values) {
		if (w==null || w.length()==0) return values;
		
		w = AnalyzerUtils.trim(w);
		if (w.length()==0) return values;
		
		w = mangle(page, w);
		if (w.length()==0) return values;
		
		values = AnalyzerUtils.addToSet(values, w);
		return values;
	}
	
	protected CharSequence mangle(WikiPage page, CharSequence v) {
		if (strip) {
			WikiTextAnalyzer analyzer = page.getAnalyzer(); 
			/*if (analyzer.smellsLikeWikiText(v))*/ v = analyzer.stripMarkup(v);
		}
		
		if (norm!=null) {
			for (Mangler m: norm) v = m.mangle(v);
		}
		
		if (upper) v = v.toString().toUpperCase();
		
		//TODO: use builder!
		if (prefix!=null) v = prefix + v;
		if (suffix!=null) v = v + suffix;
		
		return v;
	}

	public String getPropertyName() {
		return property;
	}
	
}