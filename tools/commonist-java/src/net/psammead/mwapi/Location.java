package net.psammead.mwapi;

import net.psammead.mwapi.connection.TitleUtil;

/** 
 * a page's Location metadata. 
 * the title must contain underscores instead of spaces!
 */
public final class Location {
	public final String	wiki;
	public final String	title;
	
	// TODO: should this be public??
	
	public Location(String wiki, String title) {
		this.wiki	= wiki;
		// normalize the title
		this.title	= TitleUtil.underscores(title);
	}
	
	/** 
	 * canonical representation:
	 * prefix title with : if it has none and fix underscore/space
	 */
	@Override
	public String toString() {
		return wiki + ":" + TitleUtil.spaces(title);
	}
	
	@Override
	public boolean equals(Object o) {
		if (o == null)					return false;
		if (o == this)					return true;
		if (o.getClass() != getClass())	return false;
		Location	oo = (Location)o;
		return wiki.equals(oo.wiki) 
			&& title.equals(oo.title);
	}
}
