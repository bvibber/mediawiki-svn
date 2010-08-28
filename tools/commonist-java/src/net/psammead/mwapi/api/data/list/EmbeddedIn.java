package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class EmbeddedIn {
	public final EmbeddedIn_embeddedin	embeddedin;
	public final String					continueKey;

	public EmbeddedIn(EmbeddedIn_embeddedin embeddedIn, String continueKey) {
		this.embeddedin		= embeddedIn;
		this.continueKey	= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("embeddedin",	embeddedin)
				.append("continueKey",	continueKey)
				.toString();
	}
}
