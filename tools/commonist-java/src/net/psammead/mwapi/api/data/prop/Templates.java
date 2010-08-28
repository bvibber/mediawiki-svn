package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Templates {
	public final List<Templates_page>	pages;

	public Templates(List<Templates_page> pages) {
		this.pages = pages;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("pages",	pages)
				.toString();
	}
}
