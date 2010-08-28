package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Images {
	public final List<Images_page>	pages;

	public Images(List<Images_page> pages) {
		this.pages = pages;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("pages",	pages)
			.toString();
	}
}
