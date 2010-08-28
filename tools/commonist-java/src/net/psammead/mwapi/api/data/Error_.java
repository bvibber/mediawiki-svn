package net.psammead.mwapi.api.data;

import net.psammead.util.ToString;

/** an error api.php returned */
public final class Error_ {
	public final String	code;
	public final String	info;
	public final String	text;
	
	public Error_(String code, String info, String text) {
		this.code	= code;
		this.info	= info;
		this.text	= text;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("code",	code)
				.append("info",	info)
				.append("text",	text)
				.toString();
	}
}
