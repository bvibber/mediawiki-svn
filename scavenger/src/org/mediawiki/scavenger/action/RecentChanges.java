package org.mediawiki.scavenger.action;

import java.util.List;

import org.mediawiki.scavenger.RecentChange;

public class RecentChanges extends PageAction {
	List<RecentChange> changes;
	
	public String pageExecute() throws Exception {
		changes = wiki.getRecentChanges(50);
		req.setAttribute("changes", changes);
		return "recentchanges";
	}
}
