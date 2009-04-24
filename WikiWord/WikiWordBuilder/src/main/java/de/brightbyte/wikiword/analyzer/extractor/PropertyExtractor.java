package de.brightbyte.wikiword.analyzer.extractor;

import java.util.Set;

import de.brightbyte.data.MultiMap;
import de.brightbyte.wikiword.analyzer.WikiPage;

/**
 * Extractor to extract some value from a WikiPage. 
 */
public interface PropertyExtractor {
	
	/** extracts properties and returns them as a map. If a map instance is provided,
	 * that instance will be used to store the properties. Otherwise, a new instance
	 * will be created if any properties have been found. If no map was provided and no
	 * properties have been found, this method returns null. 
	 **/
	public MultiMap<String, CharSequence, Set<CharSequence>> extract(WikiPage page, MultiMap<String, CharSequence, Set<CharSequence>> into);
}