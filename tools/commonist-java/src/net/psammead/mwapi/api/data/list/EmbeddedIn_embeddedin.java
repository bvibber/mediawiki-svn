package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class EmbeddedIn_embeddedin {
	public final List<EmbeddedIn_ei>	eis;

	public EmbeddedIn_embeddedin(List<EmbeddedIn_ei> eis) {
		this.eis	= eis;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("eis",	eis)
				.toString();
	}
}
