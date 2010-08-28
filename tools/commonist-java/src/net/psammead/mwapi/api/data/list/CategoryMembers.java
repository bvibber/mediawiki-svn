package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class CategoryMembers {
	public final CategoryMembers_categorymembers	categorymembers;
	public final String								continueKey;

	public CategoryMembers(CategoryMembers_categorymembers categoryMembers, String continueKey) {
		this.categorymembers	= categoryMembers;
		this.continueKey		= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("categoryMembers",	categorymembers)
				.append("continueKey",		continueKey)
				.toString();
	}
}
