package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class ExtLinks_extlinks {
	public final List<ExtLinks_el> els;
	
	public ExtLinks_extlinks(List<ExtLinks_el> els) {
		this.els	= els;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("els",		els)
			.toString();
	}
}