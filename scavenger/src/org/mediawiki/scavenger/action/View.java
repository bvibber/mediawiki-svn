package org.mediawiki.scavenger.action;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.PageFormatter;
import org.mediawiki.scavenger.Revision;

public class View extends PageAction {
	Page page;
	Revision viewing;
	String formattedText;
	
	public String pageExecute() throws Exception {
		if (title == null)
			return "mainpage";
	
		page = wiki.getPage(title);

		formattedText = null;
		if (page.exists()) {
			/*
			 * If the user requested a page with a non-canonical name
			 * (wrong case), redirect them.
			 */
			if (!page.getTitle().getText().equals(title.getText())) {
				req.setAttribute("pagename", page.getTitle().getText());
				return "viewpage";
			}

			String[] rev_ = (String[]) parameters.get("rev");
			if (rev_ != null)
				viewing = wiki.getRevision(Integer.parseInt(rev_[0]));
			else
				viewing = page.getLatestRevision();

			PageFormatter formatter = new PageFormatter(wiki);
			formattedText = formatter.getFormattedText(viewing);
		}
		
		return SUCCESS;
	}
	
	public Page getPage() {
		return page;
	}
	
	public Revision getViewing() {
		return viewing;
	}
	
	public String getFormattedText() {
		return formattedText;
	}
}
