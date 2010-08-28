package net.psammead.mwapi.api.data.list;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class CategoryMembers_cm {
	public final Location	location;
	public final long		pageid;
	public final String		sortkey;

	public CategoryMembers_cm(Location location, long pageid, String sortkey) {
		this.location	= location;
		this.pageid		= pageid;
		this.sortkey	= sortkey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location", location)
				.append("pageid",	pageid)
				.append("sortkey",	sortkey)
				.toString();
	}
}
