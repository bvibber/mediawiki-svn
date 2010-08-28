package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class RecentChanges_recentchanges {
	public final List<RecentChanges_rc>	rcs;

	public RecentChanges_recentchanges(List<RecentChanges_rc> rcs) {
		this.rcs	= rcs;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("rcs",	rcs)
				.toString();
	}
}
