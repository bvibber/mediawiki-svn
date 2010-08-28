package net.psammead.mwapi.api.data.prop;

import java.util.Date;

import net.psammead.util.ToString;

public final class Revisions_rev {
	public final long		revid; 
	public final String		user;
	public final Date		timestamp;
	public final String		comment;
	public final boolean	minor;
	public final boolean	anon;
	public final String		content;

	public Revisions_rev(long revid, String user, Date timestamp, String comment, boolean minor, boolean anon, String content) {
		this.revid		= revid;
		this.user		= user;
		this.timestamp	= timestamp;
		this.comment	= comment;
		this.minor		= minor;
		this.anon		= anon;
		this.content	= content;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("revid",		revid)
				.append("user",			user)
				.append("timestamp",	timestamp)
				.append("comment",		comment)
				.append("minor",		minor)
				.append("anon",			anon)
				.append("content",		content)
				.toString();
	}
}
