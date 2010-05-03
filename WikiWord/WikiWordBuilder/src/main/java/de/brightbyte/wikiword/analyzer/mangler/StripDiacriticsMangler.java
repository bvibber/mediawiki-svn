/**
 * 
 */
package de.brightbyte.wikiword.analyzer.mangler;

import de.brightbyte.util.StringUtils;

public class StripDiacriticsMangler implements Mangler {
	public StripDiacriticsMangler() {
	}
	
	public CharSequence mangle(CharSequence text) {
		return StringUtils.stripDiacritics(text);
	}
}