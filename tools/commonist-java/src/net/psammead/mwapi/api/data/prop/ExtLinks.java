package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class ExtLinks {
	public final List<ExtLinks_page>	pages;

	public ExtLinks(List<ExtLinks_page> pages) {
		this.pages = pages;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("pages",	pages)
				.toString();
	}
}
