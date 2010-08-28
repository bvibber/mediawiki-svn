package net.psammead.mwapi.api.data.list;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class ExtUrlUsage_eu {
	public final Location	location;
	public final long		pageid;
	public final String		url;

	public ExtUrlUsage_eu(Location location, long pageid, String url) {
		this.location	= location;
		this.pageid		= pageid;
		this.url		= url;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location", location)
				.append("pageid",	pageid)
				.append("url",		url)
				.toString();
	}
}
