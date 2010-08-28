package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Revisions_page {
	public final Location	location;
	public final long		pageid;
	public final Revisions_revisions	revisions;
	
	public Revisions_page(Location location, long pageid, Revisions_revisions revisions) {	// TODO redirect?
		this.location	= location;
		this.pageid		= pageid;
		this.revisions	= revisions;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",	location)
			.append("pageid",	pageid)
			.append("revs",		revisions)
			.toString();
	}
}