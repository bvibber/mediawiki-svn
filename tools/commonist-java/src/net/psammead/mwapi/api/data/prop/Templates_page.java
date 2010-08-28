package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Templates_page {
	public final Location	location;
	public final long		pageid;
	public final Templates_templates	templates;
	
	public Templates_page(Location location, long pageid, Templates_templates templates) {
		this.location	= location;
		this.pageid		= pageid;
		this.templates	= templates;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",	location)
			.append("pageid",	pageid)
			.append("tls",		templates)
			.toString();
	}
}