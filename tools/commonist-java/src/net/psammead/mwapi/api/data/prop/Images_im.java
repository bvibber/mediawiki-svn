package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Images_im {
	public final Location	location;

	public Images_im(Location location) {
		this.location	= location;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location",	location)
				.toString();
	}
}
