package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class LangLinks_page {
	public final Location	location;
	public final long		pageid;
	public final LangLinks_langlinks	langlinks;
	
	public LangLinks_page(Location location, long pageid, LangLinks_langlinks langlinks) {	// TODO redirect?
		this.location	= location;
		this.pageid		= pageid;
		this.langlinks	= langlinks;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",		location)
			.append("pageid",		pageid)
			.append("langlinks",	langlinks)
			.toString();
	}
}