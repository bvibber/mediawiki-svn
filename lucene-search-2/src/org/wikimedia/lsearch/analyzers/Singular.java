package org.wikimedia.lsearch.analyzers;

/** Interface to fetch singular form of a word in some language */
public interface Singular {
	/** Return singular form of the word, or null if such doesn't exist */
	public String getSingular(String word);
}
