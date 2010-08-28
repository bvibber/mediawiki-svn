package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class ExtLinks_page {
	public final Location			location;
	public final long				pageid;
	public final ExtLinks_extlinks	extlinks;
	
	public ExtLinks_page(Location location, long pageid, ExtLinks_extlinks extlinks) {
		this.location	= location;
		this.pageid		= pageid;
		this.extlinks	= extlinks;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",	location)
			.append("pageid",	pageid)
			.append("extLinks",	extlinks)
			.toString();
	}
}