package org.mediawiki.scavenger.action;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Revision;

public class Edit extends PageAction {
	String pageText;

	protected String pageExecute() throws Exception {
		Page p = wiki.getPage(title);
		pageText = null;
		
		Revision r = p.getLatestRevision();
		if (r != null)
			pageText = r.getText();

		req.setAttribute("pageText", pageText);
		return "edit";
	}
}
