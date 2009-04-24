/**
 * 
 */
package de.brightbyte.wikiword.analyzer.extractor;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.wikiword.analyzer.WikiPage;

public class PropertyValueExtractor implements ValueExtractor {
	protected String property;
	private String prefix;
	private String suffix;
	private boolean normalize = true;
	
	public PropertyValueExtractor(String property) {
		super();
		this.property = property;
	}

	public PropertyValueExtractor setPrefix(String prefix) {
		this.prefix = prefix;
		return this;
	}

	public PropertyValueExtractor setSuffix(String suffix) {
		this.suffix = suffix;
		return this;
	}
	
	public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
		Set<CharSequence> vv = page.getProperties().get(property);
		
		if (vv!=null && !vv.isEmpty()) {
			if (into==null) into = new HashSet<CharSequence>();
			
			for(CharSequence v: vv) {
				//TODO: use builder?!
				if (prefix!=null) v = prefix + v;
				if (suffix!=null) v = v + suffix;
				
				if (normalize) v = page.getAnalyzer().normalizeTitle(v);
				into.add(v);
			}
		}
		
		return into;
	}
	
}