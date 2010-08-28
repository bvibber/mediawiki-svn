package net.psammead.mwapi.api.data.prop;

import net.psammead.util.ToString;

public final class ExtLinks_el {
	public final String	url;

	public ExtLinks_el(String url) {
		this.url	= url;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("url",	url)
				.toString();
	}
}
