package org.mediawiki.scavenger.action;

import java.util.List;

import org.mediawiki.scavenger.Title;

public class LiveSearch extends PageAction {
	protected String pageExecute() throws Exception {
		String query = req.getParameter("q");
		List<String> matches = wiki.getPrefixMatches(new Title(query).getKey(), 10);
		req.setAttribute("matches", matches);
		return "livesearch";
	}
}
