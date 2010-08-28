package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Templates_templates {
	public final List<Templates_tl>	tls;
	
	public Templates_templates(List<Templates_tl> tls) {
		this.tls	= tls;
	}

	@Override
	public String toString() {
		return new ToString(this)
			.append("tls",	tls)
			.toString();
	}
}
