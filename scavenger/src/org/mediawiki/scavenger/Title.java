package org.mediawiki.scavenger;

/**
 * Represents a page title (not the page itself).
 */
public class Title {
	String title, key;
	
	/**
	 * Construct a new title from free text.
	 * @param text Page title
	 */
	public Title(String text) {
		title = text;
		key = title.toLowerCase().replaceAll(" ", "_");
	}
	
	/**
	 * @return Human-readable form of the page's display title.  This preserves
	 * the case.
	 */
	public String getText() {
		return title;
	}
	
	/**
	 * @return This page's key.  This is the title in lower case, with
	 * underscores in place of spaces.
	 */
	public String getKey() {
		return key;
	}
	
	/**
	 * @return Whether this title is a valid page name
	 */
	public boolean isValidName() {
		return (title.length() > 0) && (title.length() < 256);
	}
}
