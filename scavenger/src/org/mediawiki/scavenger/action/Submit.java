package org.mediawiki.scavenger.action;

import java.net.URLEncoder;

import org.mediawiki.scavenger.Page;

public class Submit extends PageAction {
	String pageExecute() throws Exception {
		Page p = wiki.getPage(title);
		String comment_, newtext = ((String[]) parameters.get("text"))[0];
		String[] comment = (String[]) parameters.get("comment");
		if (comment == null)
			comment_ = "";
		else
			comment_ = comment[0];
		
		user.create();
		p.edit(user, newtext, comment_);
		wiki.commit();
		
		String url = "view.action?title=" + URLEncoder.encode(title.getText(), "UTF-8");
		resp.sendRedirect(resp.encodeRedirectURL(url));
		return null;
	}

}
