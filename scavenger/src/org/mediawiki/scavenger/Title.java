package org.mediawiki.scavenger;

import java.sql.Connection;

/**
 * Represents a page title (not the page itself).
 */
public class Title {
	String title;
	
	/**
	 * Construct a new title from free text.
	 * @param text Page title
	 */
	public Title(String text) {
		title = text.replaceAll(" ", "_");
	}
	
	/**
	 * @return Human-readable form of this title.
	 */
	public String getText() {
		return title.replaceAll("_", " ");
	}
	
	/**
	 * @return Database key for this title.
	 */
	public String getKey() {
		return title;
	}
}
