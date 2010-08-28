package net.psammead.mwapi.api.data.list;

import java.util.Date;

import net.psammead.util.ToString;

public final class WatchList {
	public final WatchList_watchlist	watchList;
	public final Date				continueKey;

	public WatchList(WatchList_watchlist watchList, Date continueKey) {
		this.watchList		= watchList;
		this.continueKey	= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("watchList",	watchList)
				.append("continueKey",	continueKey)
				.toString();
	}
}
