package org.mediawiki.scavenger.action;

import java.util.List;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Revision;

import com.opensymphony.xwork2.ActionSupport;

/**
 * Shows history of edits to a page.
 */
public class History extends PageAction {
	List<Revision> revisions;
	
	public String pageExecute() throws Exception {
		Page p = wiki.getPage(title);
		revisions = p.getHistory(50);
		
		return SUCCESS;
	}
	
	public List<Revision> getRevisions() {
		return revisions;
	}
}
