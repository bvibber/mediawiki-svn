package org.mediawiki.scavenger.action;

import java.util.List;

import org.mediawiki.scavenger.RecentChange;

public class RecentChanges extends PageAction {
	List<RecentChange> changes;
	
	String pageExecute() throws Exception {
		changes = wiki.getRecentChanges(50);
		return SUCCESS;
	}
	
	public List<RecentChange> getChanges() {
		return changes;
	}
}
