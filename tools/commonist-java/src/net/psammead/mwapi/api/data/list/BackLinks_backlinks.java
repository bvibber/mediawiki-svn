package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class BackLinks_backlinks {
	public final List<BackLinks_bl>	bls;

	public BackLinks_backlinks(List<BackLinks_bl> bls) {
		this.bls	= bls;
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("bls",	bls)
				.toString();
	}
}
