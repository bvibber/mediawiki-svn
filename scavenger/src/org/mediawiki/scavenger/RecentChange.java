package org.mediawiki.scavenger;

import java.sql.SQLException;

public class RecentChange {
	Revision r;
	
	public RecentChange(Revision r) {
		this.r = r;
	}
	
	public Page getPage() throws SQLException {
		return r.getPage();
	}
	
	public Title getTitle() throws SQLException {
		return r.getPage().getTitle();
	}
	
	public Revision getRevision() {
		return r;
	}
}
