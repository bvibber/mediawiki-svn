package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Links_pl {
	public final Location	location;

	public Links_pl(Location location) {
		this.location	= location;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location",	location)
				.toString();
	}
}
