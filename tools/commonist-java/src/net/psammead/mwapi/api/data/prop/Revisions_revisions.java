package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Revisions_revisions {
	public final List<Revisions_rev> revs;
	
	public Revisions_revisions(List<Revisions_rev> revs) {
		this.revs	= revs;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("revs",		revs)
			.toString();
	}
}