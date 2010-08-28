package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Templates_tl {
	public final Location	location;

	public Templates_tl(Location location) {
		this.location	= location;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location",	location)
				.toString();
	}
}
