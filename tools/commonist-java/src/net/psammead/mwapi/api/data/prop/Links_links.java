package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Links_links {
	public final List<Links_pl> pls;
	
	public Links_links(List<Links_pl> pls) {
		this.pls	= pls;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("pls",	pls)
			.toString();
	}
}