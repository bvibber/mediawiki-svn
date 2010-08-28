package net.psammead.mwapi.api.data.list;

import net.psammead.util.ToString;

public final class AllUsers_u {
	public final String	name;
	public final int	editcount;

	public AllUsers_u(String name, int editcount) {
		this.name		= name;
		this.editcount	= editcount;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("name", 		name)
				.append("editcount",	editcount)
				.toString();
	}
}
