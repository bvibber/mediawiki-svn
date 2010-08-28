package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class ImageUsage_imageusage {
	public final List<ImageUsage_iu>	ius;

	public ImageUsage_imageusage(List<ImageUsage_iu> ius) {
		this.ius	= ius;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("ius",	ius)
				.toString();
	}
}
