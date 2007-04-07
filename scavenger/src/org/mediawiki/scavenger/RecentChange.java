package org.mediawiki.scavenger;

import java.sql.SQLException;

public class RecentChange {
	Revision revision;
	
	public RecentChange(Revision r) {
		revision = r;
	}
	
	public Page getPage() throws SQLException {
		return revision.getPage();
	}
	
	public Title getTitle() throws SQLException {
		return revision.getPage().getTitle();
	}
	
	public Revision getRevision() {
		return revision;
	}
}
