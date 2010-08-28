package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class ExtUrlUsage_exturlusage {
	public final List<ExtUrlUsage_eu>	eus;

	public ExtUrlUsage_exturlusage(List<ExtUrlUsage_eu> eus) {
		this.eus	= eus;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("eus",	eus)
				.toString();
	}
}
