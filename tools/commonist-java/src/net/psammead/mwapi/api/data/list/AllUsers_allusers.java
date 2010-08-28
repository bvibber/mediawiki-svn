package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class AllUsers_allusers {
	public final List<AllUsers_u>	us;

	public AllUsers_allusers(List<AllUsers_u> us) {
		this.us	= us;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("us",	us)
				.toString();
	}
}
