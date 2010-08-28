package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Categories_cl {
	public final Location	location;
	public final String		sortkey;

	public Categories_cl(Location location, String sortkey) {
		this.location	= location;
		this.sortkey	= sortkey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location",	location)
				.append("sortkey",	sortkey)
				.toString();
	}
}
