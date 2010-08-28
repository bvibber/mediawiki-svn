package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class AllPages {
	public final AllPages_allpages	allpages;
	public final String				continueKey;

	public AllPages(AllPages_allpages allpages, String continueKey) {
		this.allpages		= allpages;
		this.continueKey	= continueKey;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("allpages",		allpages)
				.append("continueKey",	continueKey)
				.toString();
	}
}
