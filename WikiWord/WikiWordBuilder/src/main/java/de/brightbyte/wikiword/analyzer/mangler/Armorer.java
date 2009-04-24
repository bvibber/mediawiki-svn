package de.brightbyte.wikiword.analyzer.mangler;


/**
 * An Armorer replaces parts of a text by a placeholder, and stores the 
 * placeholder along with the text that was removed in a TextArmor object,
 * so it can be put back later. This is used to protect some parts of a
 * text against processing.
 */
public interface Armorer {
	public static final char ARMOR_MARKER_CHAR = '\u007F';

	public CharSequence armor(CharSequence text, TextArmor armor);
}