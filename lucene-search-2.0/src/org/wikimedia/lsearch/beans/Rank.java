package org.wikimedia.lsearch.beans;

import java.util.ArrayList;

public class Rank {
	/** Number of linking articles */
	public int links;
	/** if this is redirect, point to the target title */
	public Rank redirectsTo;
	/** all the pages that get redirected here */
	public ArrayList<String> redirected;
	
	public Rank(int links) {
		this.links = links;
		redirectsTo = null;
	}

	public Rank(int links, Rank redirect) {
		this.links = links;
		this.redirectsTo = redirect;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + links;
		result = PRIME * result + 0;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final Rank other = (Rank) obj;
		if (links != other.links)
			return false;
		if (redirectsTo == null) {
			if (other.redirectsTo != null)
				return false;
		} else if (redirectsTo != other.redirectsTo)
			return false;
		return true;
	}
	
	
	
}
