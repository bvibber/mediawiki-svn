package org.mediawiki.scavenger.action;

import java.util.List;

import org.mediawiki.scavenger.Page;

public class AllPages extends PageAction {
	List<Page> pages;
	
	public String pageExecute() throws Exception {
		pages = wiki.getAllPages();
		req.setAttribute("pages", pages);
		return "allpages";
	}
}
