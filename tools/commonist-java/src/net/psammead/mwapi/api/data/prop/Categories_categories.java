package net.psammead.mwapi.api.data.prop;

import java.util.List;

import net.psammead.util.ToString;

public final class Categories_categories {
	public final List<Categories_cl> cls;
	
	public Categories_categories(List<Categories_cl> cls) {
		this.cls	= cls;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
			.append("cls",		cls)
			.toString();
	}
}