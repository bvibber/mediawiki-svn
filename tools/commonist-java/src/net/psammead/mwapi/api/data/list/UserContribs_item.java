package net.psammead.mwapi.api.data.list;

import java.util.Date;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class UserContribs_item {
	public final Location	location;
	public final long		pageid;
	public final long		revid;
	public final Date		timestamp;
	public final boolean	fresh;
	public final boolean	minor;
	public final String		comment;

	public UserContribs_item(Location location, long pageid, long revid, Date timestamp, boolean fresh, boolean minor, String comment) {
		this.location	= location;
		this.pageid		= pageid;
		this.revid		= revid;
		this.fresh		= fresh;
		this.minor		= minor;
		this.comment	= comment;
		this.timestamp	= timestamp;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location", 	location)
				.append("pageid",		pageid)
				.append("revid",		revid)
				.append("timestamp",	timestamp)
				.append("fresh",		fresh)
				.append("minor",		minor)
				.append("comment",		comment)
				.toString();
	}
}
