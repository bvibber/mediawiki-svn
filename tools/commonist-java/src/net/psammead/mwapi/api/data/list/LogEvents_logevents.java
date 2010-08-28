package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class LogEvents_logevents {
	public final List<LogEvents_item>	items;

	public LogEvents_logevents(List<LogEvents_item> items) {
		this.items	= items;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("items",		items)
				.toString();
	}
}
