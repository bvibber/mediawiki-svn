package net.psammead.mwapi.api.data.list;

import java.util.Date;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class WatchList_item {
	public final Location	location;
	public final long		pageid;
	public final long		revid;
	public final Date		timestamp; 
	public final boolean	fresh;
	public final boolean	minor;
	public final boolean	anon;
	public final String		type;
	public final String		user; 
	public final int		oldlen;
	public final int		newlen; 
	public final String		comment;

	public WatchList_item(Location location, long pageid, long revid, Date timestamp, boolean fresh, boolean minor, boolean anon, String type, String user, int oldlen, int newlen, String comment) {
		this.location	= location;
		this.pageid		= pageid;
		this.revid		= revid;        
		this.timestamp	= timestamp;
		this.fresh		= fresh;
		this.minor		= minor;
		this.anon		= anon;
		this.type		= type;
		this.user		= user;
		this.oldlen		= oldlen;
		this.newlen		= newlen;
		this.comment	= comment;
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
				.append("type",			type)
				.append("user",			user)
				.append("oldlen",		oldlen)
				.append("newlen",		newlen)
				.append("comment",		comment)
				.toString();
	}
}
