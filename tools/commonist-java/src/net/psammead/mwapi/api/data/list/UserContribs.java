package net.psammead.mwapi.api.data.list;

import java.util.Date;

import net.psammead.util.ToString;

public final class UserContribs {
	public final UserContribs_usercontribs	userContribs;
	public final Date						continueKey;

	public UserContribs(UserContribs_usercontribs userContribs, Date continueKey) {
		this.userContribs	= userContribs;
		this.continueKey	= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("userContribs",		userContribs)
				.append("continueKey",		continueKey)
				.toString();
	}
}
