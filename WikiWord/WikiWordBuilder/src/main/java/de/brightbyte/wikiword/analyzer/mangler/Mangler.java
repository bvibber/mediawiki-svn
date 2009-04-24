package de.brightbyte.wikiword.analyzer.mangler;

/**
 * A Mangler changes text in some way.
 */
public interface Mangler {
	public CharSequence mangle(CharSequence text);
}