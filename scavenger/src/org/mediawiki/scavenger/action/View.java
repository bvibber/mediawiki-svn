package org.mediawiki.scavenger.action;

import java.sql.SQLException;
import java.util.Map;

import org.apache.struts2.interceptor.ParameterAware;
import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.PageFormatter;
import org.mediawiki.scavenger.Revision;

import com.opensymphony.xwork2.ActionSupport;

public class View extends PageAction {
	Page page;
	Revision viewing;
	PageFormatter formatter;
	
	public String pageExecute() throws Exception {
		if (title == null)
			return "mainpage";
	
		page = wiki.getPage(title);

		String[] rev_ = (String[]) parameters.get("rev");
		if (rev_ != null)
			viewing = wiki.getRevision(Integer.parseInt(rev_[0]));
		else
			viewing = page.getLatestRevision();
	
		formatter = new PageFormatter(viewing);
		
		return SUCCESS;
	}
	
	public Page getPage() {
		return page;
	}
	
	public Revision getViewing() {
		return viewing;
	}
	
	public PageFormatter getFormatter() {
		return formatter;
	}
}
