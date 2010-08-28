package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class ImageInfo_imageinfo {
	public final List<ImageInfo_ii> iis;
	
	public ImageInfo_imageinfo(List<ImageInfo_ii> iis) {
		this.iis	= iis;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("iis",		iis)
			.toString();
	}
}