package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class UserContribs_usercontribs {
	public final List<UserContribs_item>	items;

	public UserContribs_usercontribs(List<UserContribs_item> items) {
		this.items	= items;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("items",	items)
				.toString();
	}
}
