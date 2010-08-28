package net.psammead.mwapi.api.data.prop;

import java.util.Date;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class Info_page {
	public final Location	location;
	public final long		pageid;
	public final long		lastrevid;
	public final Date		touched;
	public final int		counter;
	public final int		length;
	public final boolean	redirect;
	
	public Info_page(Location location, long pageid, long lastrevid, Date touched, int counter, int length, boolean redirect) {
		this.location	= location;
		this.pageid		= pageid;
		this.lastrevid	= lastrevid;
		this.touched	= touched;
		this.counter	= counter;
		this.length		= length;
		this.redirect	= redirect;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",		location)
			.append("pageid",		pageid)
			.append("lastrevid",	lastrevid)
			.append("touched",		touched)
			.append("counter",		counter)
			.append("length",		length)
			.append("redirect",		redirect)
			.toString();
	}
}