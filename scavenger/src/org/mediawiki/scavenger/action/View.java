package org.mediawiki.scavenger.action;

import java.net.URLEncoder;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.PageFormatter;
import org.mediawiki.scavenger.Revision;

public class View extends PageAction {
	Page page;
	Revision viewing;
	String formattedText;
	
	protected String pageExecute() throws Exception {
		if (title == null)
			return "mainpage";
	
		page = wiki.getPage(title);

		formattedText = null;
		if (page.exists()) {
			/*
			 * If the user requested a page with a non-canonical name
			 * (wrong case), redirect them.
			 */
			String pageURL = page.getTitle().getURLText();
			if (!pageURL.equals(title.getURLText())) {
				String url = req.getContextPath() + "/view/" + 
								URLEncoder.encode(pageURL, "UTF-8");
				resp.sendRedirect(resp.encodeRedirectURL(url));
				return null;
			}

			String rev = req.getParameter("rev");
			if (rev != null)
				viewing = wiki.getRevision(Integer.parseInt(rev));
			else
				viewing = page.getLatestRevision();
		}
		
		req.setAttribute("viewing", viewing);
		return "view";
	}
}
