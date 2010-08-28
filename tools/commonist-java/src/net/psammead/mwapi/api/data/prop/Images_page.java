package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Images_page {
	public final Location		location;
	public final long			pageid;
	public final Images_images	images;
	
	public Images_page(Location location, long pageid, Images_images images) {
		this.location	= location;
		this.pageid		= pageid;
		this.images		= images;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",	location)
			.append("pageid",	pageid)
			.append("images",	images)
			.toString();
	}
}