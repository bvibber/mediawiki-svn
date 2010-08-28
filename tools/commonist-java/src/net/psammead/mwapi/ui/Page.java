package net.psammead.mwapi.ui;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

/** 
 * a wikitext page with its edit metadata
 * the editToken and startTime may be null
 */
public final class Page {
	public final Location	location;
	public final String		body;
	
	public final String		editTime; 
	public final String		startTime;
	public final String		editToken;	// session ID
	public final boolean	watchThis;	// the page is watched
	public final boolean	watchKnown;	// watchThis is uncertain
	public final boolean	fresh;		// newly created article
	
	public Page(Location location, String body,
			String editTime, String startTime, String editToken, 
			boolean watchThis, boolean watchKnown, boolean fresh) {
		this.location	= location;
		this.body		= body;
		
		this.editTime	= editTime;
		this.startTime	= startTime;
		this.editToken	= editToken;
		this.watchThis	= watchThis;
		this.watchKnown	= watchKnown;
		this.fresh		= fresh;
	}
	
	/** returns a new Page with the same data but a different body */
	public Page edit(String newBody) {
		return new Page(location, newBody,
				editTime, startTime, editToken, 
				watchThis, watchKnown, fresh);
	}
	
	/** returns a new Page with the same data but a given watch state */
	public Page watched(boolean newWatchThis) {
		return new Page(location, body,
				editTime, startTime, editToken, 
				newWatchThis, true, fresh);
	}
	
	/** for debugging purposes only */
	@Override
	public String toString() {
		return new ToString(this)
				.append("location", 	location)
				.append("editTime",		editTime)
				.append("startTime",	startTime)
				.append("editToken",	editToken)
				.append("watchThis",	watchThis)
				.append("watchKnown",	watchKnown)
				.append("fresh",		fresh)
				.append("body",			body)
				.toString();
	}
}
