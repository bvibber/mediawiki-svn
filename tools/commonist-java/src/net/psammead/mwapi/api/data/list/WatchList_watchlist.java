package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class WatchList_watchlist {
	public final List<WatchList_item>	items;

	public WatchList_watchlist(List<WatchList_item> items) {
		this.items	= items;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("items",	items)
				.toString();
	}
}
