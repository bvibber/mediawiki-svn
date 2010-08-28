package net.psammead.mwapi.api.data.prop;

import net.psammead.util.ToString;

public final class LangLinks_li {
	public final String	lang;

	public LangLinks_li(String lang) {
		this.lang	= lang;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("lang",	lang)
				.toString();
	}
}
