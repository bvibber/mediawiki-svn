package org.wikimedia.lsearch.beans;

public class Rank {
	/** Number of linking articles */
	public int links;
	/** if this is redirect, point to the target title */
	public String redirect;
	
	public Rank(int links) {
		this.links = links;
		redirect = null;
	}

	public Rank(int links, String redirect) {
		this.links = links;
		this.redirect = redirect;
	}
	
}
