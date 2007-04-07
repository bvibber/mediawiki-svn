package org.mediawiki.scavenger.action;

import java.net.URLEncoder;

import org.mediawiki.scavenger.Page;

public class Submit extends PageAction {
	public String pageExecute() throws Exception {
		Page p = wiki.getPage(title);
		String newtext = req.getParameter("text");
		String comment = req.getParameter("comment");
		if (comment == null)
			comment = "";
		
		user.create();
		p.edit(user, newtext, comment);
		wiki.commit();
		
		String url = "view/" + URLEncoder.encode(title.getText(), "UTF-8");
		resp.sendRedirect(resp.encodeRedirectURL(url));
		return null;
	}

}
