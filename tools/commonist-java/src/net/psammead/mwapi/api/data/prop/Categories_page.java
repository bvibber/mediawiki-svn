package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Categories_page {
	public final Location				location;
	public final long					pageid;
	public final Categories_categories	categories;
	
	public Categories_page(Location location, long pageid, Categories_categories categories) {
		this.location		= location;
		this.pageid			= pageid;
		this.categories		= categories;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",		location)
			.append("pageid",		pageid)
			.append("categories",	categories)
			.toString();
	}
}