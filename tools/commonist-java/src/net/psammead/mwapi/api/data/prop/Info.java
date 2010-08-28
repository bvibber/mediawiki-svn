package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Info {
	public final List<Info_page>	pages;

	public Info(List<Info_page> pages) {
		this.pages = pages;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("pages",	pages)
				.toString();
	}
}
