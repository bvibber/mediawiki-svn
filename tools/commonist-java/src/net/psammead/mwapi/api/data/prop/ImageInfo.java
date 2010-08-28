package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class ImageInfo {
	public final List<ImageInfo_page>	pages;

	public ImageInfo(List<ImageInfo_page> pages) {
		this.pages = pages;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("pages",	pages)
				.toString();
	}
}
