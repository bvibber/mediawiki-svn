package net.psammead.mwapi.api.data.list;

import java.util.Date;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class LogEvents_item {
	public final Location	location;
	public final long		pageid;
	public final int		logid;
	public final Date		timestamp;
	public final String		type;
	public final String		action;
	public final String		user;
	public final String		comment;

	public LogEvents_item(Location location, long pageid, int logid, Date timestamp, String type, String action, String user, String comment) {
		this.location	= location;
		this.pageid		= pageid;
		this.logid		= logid;
		this.timestamp	= timestamp;
		this.type		= type;
		this.action		= action;
		this.user		= user;
		this.comment	= comment;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("location", 	location)
				.append("pageid",		pageid)
				.append("logid",		logid)
				.append("timestamp",	timestamp)
				.append("type",			type)
				.append("action",		action)
				.append("user",			user)
				.append("comment",		comment)
				.toString();
	}
}
