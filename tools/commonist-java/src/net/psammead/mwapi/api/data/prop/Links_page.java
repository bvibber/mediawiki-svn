package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Links_page {
	public final Location	location;
	public final long		pageid;
	public final Links_links links;
	
	public Links_page(Location location, long pageid, Links_links links) {	// TODO redirect?
		this.location	= location;
		this.pageid		= pageid;
		this.links		= links;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",	location)
			.append("pageid",	pageid)
			.append("links",	links)
			.toString();
	}
}