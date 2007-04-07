package org.mediawiki.scavenger.action;

import java.util.List;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Revision;

/**
 * Shows history of edits to a page.
 */
public class History extends PageAction {
	List<Revision> revisions;
	
	public String pageExecute() throws Exception {
		Page p = wiki.getPage(title);
		revisions = p.getHistory(50);
		
		/*
		 * If the user requested a page with a non-canonical name
		 * (wrong case), redirect them.
		 */
		if (!p.getTitle().getText().equals(title.getText())) {
			req.setAttribute("pagename", p.getTitle().getText());
			return "viewpage";
		}
		
		req.setAttribute("revisions", revisions);
		return "history";
	}
}
