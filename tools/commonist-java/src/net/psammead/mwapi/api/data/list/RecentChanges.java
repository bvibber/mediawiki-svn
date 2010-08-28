package net.psammead.mwapi.api.data.list;

import java.util.Date;

import net.psammead.util.ToString;

public final class RecentChanges {
	public final RecentChanges_recentchanges recentChanges;
	public final Date						continueKey;

	public RecentChanges(RecentChanges_recentchanges recentChanges, Date continueKey) {
		this.recentChanges	= recentChanges;
		this.continueKey	= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("recentChanges",	recentChanges)
				.append("continueKey",		continueKey)
				.toString();
	}
}
