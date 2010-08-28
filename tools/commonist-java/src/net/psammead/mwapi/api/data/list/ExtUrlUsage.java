package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class ExtUrlUsage {
	public final ExtUrlUsage_exturlusage	extUrlUsage;
	public final int						continueKey;

	public ExtUrlUsage(ExtUrlUsage_exturlusage extUrlUsage, int continueKey) {
		this.extUrlUsage	= extUrlUsage;
		this.continueKey	= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("extUrlUsage",	extUrlUsage)
				.append("continueKey",	continueKey)
				.toString();
	}
}
