package org.mediawiki.scavenger.action;

import java.util.List;

import org.mediawiki.scavenger.Page;

public class AllPages extends PageAction {
	List<Page> pages;
	
	String pageExecute() throws Exception {
		pages = wiki.getAllPages();
		return SUCCESS;
	}

	public List<Page> getPages() {
		return pages;
	}
}
