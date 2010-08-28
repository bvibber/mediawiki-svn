package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Images_images {
	public final List<Images_im> ims;
	
	public Images_images(List<Images_im> ims) {
		this.ims	= ims;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("ims",		ims)
			.toString();
	}
}