package net.psammead.mwapi.api.data.list;

import java.util.List;

import net.psammead.util.ToString;

public final class CategoryMembers_categorymembers {
	public final List<CategoryMembers_cm>	cms;

	public CategoryMembers_categorymembers(List<CategoryMembers_cm> cms) {
		this.cms	= cms;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("cms",	cms)
				.toString();
	}
}
