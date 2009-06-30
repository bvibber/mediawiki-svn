/**
 * 
 */
package de.brightbyte.wikiword.analyzer.mangler;

import de.brightbyte.xml.HtmlEntities;

public class EntityDecodeMangler implements Mangler {
	public CharSequence mangle(CharSequence text) {
		return HtmlEntities.decodeEntities(text);
	}
}