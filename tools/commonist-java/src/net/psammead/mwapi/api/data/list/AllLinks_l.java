package net.psammead.mwapi.api.data.list;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class AllLinks_l {
	public final Location	location;
	public final long		fromid;

	public AllLinks_l(Location location, long fromid) {
		this.location	= location;
		this.fromid		= fromid;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location", location)
				.append("fromid",	fromid)
				.toString();
	}
}
