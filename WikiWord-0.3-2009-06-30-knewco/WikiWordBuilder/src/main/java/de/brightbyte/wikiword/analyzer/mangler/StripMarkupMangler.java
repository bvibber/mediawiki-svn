/**
 * 
 */
package de.brightbyte.wikiword.analyzer.mangler;

import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class StripMarkupMangler implements Mangler {
	private WikiTextAnalyzer analyzer;

	public StripMarkupMangler(WikiTextAnalyzer analyzer) {
		if (analyzer==null) throw new NullPointerException();
		this.analyzer = analyzer;
	}
	
	public CharSequence mangle(CharSequence text) {
		return this.analyzer.stripMarkup(text);
	}
}