/**
 * 
 */
package de.brightbyte.wikiword.analyzer.mangler;

import java.util.Properties;

import de.brightbyte.util.StringUtils;

public class CharacterTranslationMangler implements Mangler {
	protected Properties translation;
	
	public CharacterTranslationMangler(Properties translation) {
		this.translation = translation;
	}
	
	public CharSequence mangle(CharSequence text) {
		return StringUtils.replace(text, translation);
	}
}