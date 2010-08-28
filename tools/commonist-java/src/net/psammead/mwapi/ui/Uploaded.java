package net.psammead.mwapi.ui;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

/** 
 * contains information about an upload
 * location may be null if unknown (only when a  file ist overwritten with ignorewarning) 
 */
public final class Uploaded {
	public final Location	location;
	
	public Uploaded(Location location) {
		this.location	= location;
	}
	
	/** for debugging purposes only */
	@Override
	public String toString() {
		return new ToString(this)
				.append("location",	location)
				.toString();
	}
}
