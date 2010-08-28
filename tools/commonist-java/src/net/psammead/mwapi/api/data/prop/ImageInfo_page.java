package net.psammead.mwapi.api.data.prop;

import net.psammead.mwapi.Location;
import net.psammead.util.ToString;

public final class ImageInfo_page {
	public final Location	location;
	public final long		pageid;
	public final String		imagerepository;
	public final ImageInfo_imageinfo imageinfo;
	
	public ImageInfo_page(Location location, long pageid, String imagerepository, ImageInfo_imageinfo imageinfo) {
		this.location			= location;
		this.pageid				= pageid;
		this.imagerepository	= imagerepository;
		this.imageinfo			= imageinfo;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("location",		location)
			.append("pageid",		pageid)
			.append("imageInfo",	imageinfo)
			.toString();
	}
}