package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class AllLinks_alllinks {
	public final List<AllLinks_l>	ls;

	public AllLinks_alllinks(List<AllLinks_l> ls) {
		this.ls	= ls;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("ls",	ls)
				.toString();
	}
}
