package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Categories {
	public final List<Categories_page>	pages;

	public Categories(List<Categories_page> pages) {
		this.pages = pages;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("pages",	pages)
				.toString();
	}
}
