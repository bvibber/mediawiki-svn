package org.mediawiki.scavenger.action;

import java.sql.SQLException;
import java.util.List;

import org.mediawiki.scavenger.Differ;
import org.mediawiki.scavenger.PageFormatter;
import org.mediawiki.scavenger.Revision;

public class Diff extends PageAction {
	List<Differ.DiffLine> difflines;
	Revision r1;
	Revision r2;
	PageFormatter r1formatter;
	
	public String pageExecute() throws SQLException {
		int ra = Integer.parseInt(req.getParameter("r1"));
		r1 = wiki.getRevision(ra);
		
		String rb = req.getParameter("r2");
		if (rb.equals("prev"))
			r2 = r1.prevRevision();
		else if (rb.equals("next"))
			r2 = r1.nextRevision();
		else
			r2 = wiki.getRevision(Integer.parseInt(rb));
		r1formatter = new PageFormatter(wiki);
		
		Differ d = new Differ(r2, r1);
		difflines = d.format();
		req.setAttribute("difflines", difflines);
		req.setAttribute("r1", r1);
		req.setAttribute("r2", r2);
		req.setAttribute("r1text", r1formatter.getFormattedText(r1));
		return "diff";
	}	
}
