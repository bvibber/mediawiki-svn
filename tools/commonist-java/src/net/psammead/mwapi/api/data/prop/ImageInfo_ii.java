package net.psammead.mwapi.api.data.prop;

import java.util.Date;

import net.psammead.util.ToString;

public final class ImageInfo_ii {
	public final Date	timestamp;
	public final String	user;
	public final long	size;
	public final int	width;
	public final int	height;
	public final String	url;
	public final String	comment;
	public final String	content;

	public ImageInfo_ii(Date timestamp, String user, long size, int width, int height, String url, String comment, String content) {
		this.timestamp	= timestamp;
		this.user		= user;
		this.size		= size;
		this.width		= width;
		this.height		= height;
		this.url		= url;
		this.comment	= comment;
		this.content	= content;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("timestamp",	timestamp)
				.append("user",			user)
				.append("size",			size)
				.append("width",		width)
				.append("height",		height)
				.append("url",			url)
				.append("comment",		comment)
				.append("content",		content)
				.toString();
	}

}
