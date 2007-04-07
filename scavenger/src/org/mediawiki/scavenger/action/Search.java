package org.mediawiki.scavenger.action;

import java.net.URLEncoder;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Title;

public class Search extends PageAction {
	protected String pageExecute() throws Exception {
		String name = req.getParameter("q");
		Title t = wiki.getTitle(name);
		Page p = wiki.getPage(t);
		
		if (p.exists()) {
			String url = req.getContextPath() + "/view/" + 
				URLEncoder.encode(t.getText(), "UTF-8");
			resp.sendRedirect(resp.encodeRedirectURL(url));
			return null;
		}
		
		req.setAttribute("title", t);
		req.setAttribute("page", p);
		return "search";
	}
}
