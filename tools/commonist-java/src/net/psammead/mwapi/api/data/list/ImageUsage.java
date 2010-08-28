package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class ImageUsage {
	public final ImageUsage_imageusage	imageUsage;
	public final String					continueKey;

	public ImageUsage(ImageUsage_imageusage imageUsage, String continueKey) {
		this.imageUsage		= imageUsage;
		this.continueKey	= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("imageUsage",	imageUsage)
				.append("continueKey",	continueKey)
				.toString();
	}
}
