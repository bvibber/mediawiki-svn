package net.psammead.mwapi.api.data.list;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class BackLinks_bl {
	public final Location	location;
	public final long		pageid;

	public BackLinks_bl(Location location, long pageid) {
		this.location	= location;
		this.pageid		= pageid;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location", location)
				.append("pageid",	pageid)
				.toString();
	}
}
