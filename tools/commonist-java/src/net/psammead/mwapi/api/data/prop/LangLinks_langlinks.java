package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class LangLinks_langlinks {
	public final List<LangLinks_li> lls;
	
	public LangLinks_langlinks(List<LangLinks_li> lls) {
		this.lls	= lls;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("lls",	lls)
			.toString();
	}
}