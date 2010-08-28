package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class AllLinks {
	public final AllLinks_alllinks	alllinks;
	public final String				continueKey;

	public AllLinks(AllLinks_alllinks alllinks, String continueKey) {
		this.alllinks		= alllinks;
		this.continueKey	= continueKey;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("alllinks",		alllinks)
				.append("continueKey",	continueKey)
				.toString();
	}
}
