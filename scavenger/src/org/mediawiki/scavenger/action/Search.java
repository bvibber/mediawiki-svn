package org.mediawiki.scavenger.action;

import java.net.URLEncoder;
import java.util.List;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.search.SearchIndex;
import org.mediawiki.scavenger.search.SearchResult;

public class Search extends PageAction {
	protected String pageExecute() throws Exception {
		String name = req.getParameter("q");
		Title t = wiki.getTitle(name);
		Page p = wiki.getPage(t);
		
		if (p.exists()) {
			String url = req.getContextPath() + "/view/" + 
				URLEncoder.encode(t.getURLText(), "UTF-8");
			resp.sendRedirect(resp.encodeRedirectURL(url));
			return null;
		}
		
		req.setAttribute("title", t);
		SearchIndex idx = wiki.getSearchIndex();

		if (idx == null)
			return "search";
	
		List<SearchResult> hits = idx.search(name, 20);
		req.setAttribute("hits", hits);
		return "search";
	}
}
