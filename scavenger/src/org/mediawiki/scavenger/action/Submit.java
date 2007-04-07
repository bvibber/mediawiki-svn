package org.mediawiki.scavenger.action;

import java.net.URLEncoder;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Revision;
import org.mediawiki.scavenger.search.SearchIndex;

public class Submit extends PageAction {
	public String pageExecute() throws Exception {
		Page p = wiki.getPage(title);
		String newtext = req.getParameter("text");
		String comment = req.getParameter("comment");
		if (comment == null)
			comment = "";

		/*
		 * Strip trailing space, and ensure text ends with \n.
		 */
		newtext = newtext.replaceAll("[ \t]*$", "");
		if (newtext.charAt(newtext.length() - 1) != '\n') {
			newtext += '\n';
		}
		
		user.create();
		Revision r = p.edit(user, newtext, comment);
		wiki.commit();

		SearchIndex idx = wiki.getSearchIndex();
		if (idx != null)
			idx.indexRevision(r);

		String url = req.getContextPath() + "/view/" + 
			URLEncoder.encode(title.getURLText(), "UTF-8");
		resp.sendRedirect(resp.encodeRedirectURL(url));
		return null;
	}

}
