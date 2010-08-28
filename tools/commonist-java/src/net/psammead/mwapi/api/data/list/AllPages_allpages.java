package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class AllPages_allpages {
	public final List<AllPages_p>	ps;

	public AllPages_allpages(List<AllPages_p> ps) {
		this.ps	= ps;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("ps",	ps)
				.toString();
	}
}
