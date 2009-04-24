package de.brightbyte.wikiword.analyzer.mangler;

import java.text.ParsePosition;

/**
 * A SuccessiveMangler changes text in some way, starting at a 
 * given position and stopping at some point it determines based on
 * some internal logic. May be implemented for instance to extract one 
 * paragraph after another from a text.
 */
public interface SuccessiveMangler {
	public CharSequence mangle(CharSequence text, ParsePosition pp);
}