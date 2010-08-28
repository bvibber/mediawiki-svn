package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class AllUsers {
	public final AllUsers_allusers	allusers;
	public final String				continueKey;

	public AllUsers(AllUsers_allusers allusers, String continueKey) {
		this.allusers		= allusers;
		this.continueKey	= continueKey;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("allusers",		allusers)
				.append("continueKey",	continueKey)
				.toString();
	}
}
