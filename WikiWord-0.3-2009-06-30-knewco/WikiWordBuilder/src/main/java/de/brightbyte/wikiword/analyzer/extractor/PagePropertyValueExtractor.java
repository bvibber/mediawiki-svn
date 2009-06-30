/**
 * 
 */
package de.brightbyte.wikiword.analyzer.extractor;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.wikiword.analyzer.WikiPage;

public class PagePropertyValueExtractor implements ValueExtractor {
	protected String name;

	public PagePropertyValueExtractor(String name) {
		this.name = name;
	}

	public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into) {
		Set<CharSequence> vv = page.getProperties().get(name);
		if (vv!=null && vv.size()>0) {
			if (into==null) into = new HashSet<CharSequence>();
			into.addAll(vv);
		}
		
		return into;
	}
	
}