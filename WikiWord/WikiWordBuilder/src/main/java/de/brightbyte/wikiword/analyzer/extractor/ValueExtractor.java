package de.brightbyte.wikiword.analyzer.extractor;

import java.util.Set;

import de.brightbyte.wikiword.analyzer.WikiPage;

/**
 * Extractor to extract some value from a WikiPage. 
 */
public interface ValueExtractor {
	
	public Set<CharSequence> extract(WikiPage page, Set<CharSequence> into);
}