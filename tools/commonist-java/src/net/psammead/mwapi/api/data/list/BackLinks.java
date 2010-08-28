package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class BackLinks {
	public final BackLinks_backlinks	backlinks;
	public final String					continueKey;

	public BackLinks(BackLinks_backlinks backlinks, String continueKey) {
		this.backlinks		= backlinks;
		this.continueKey	= continueKey;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("backlinks",	backlinks)
				.append("continueKey",	continueKey)
				.toString();
	}
}
