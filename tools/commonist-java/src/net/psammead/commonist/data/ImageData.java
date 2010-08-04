package net.psammead.commonist.data;

import java.io.File;

/** Data edited in an ImageUI */
public final class ImageData {
	public final File		file;
	public final String		name;
	public final String		description;
	public final String		categories;
	public final boolean	upload;
	
	public ImageData(
			File	file,
			String	name,
			String	description,
			String	categories,
			boolean	upload) {
		this.file			= file;
		this.name			= name;
		this.description	= description;
		this.categories		= categories;
		this.upload			= upload;
	}
}
