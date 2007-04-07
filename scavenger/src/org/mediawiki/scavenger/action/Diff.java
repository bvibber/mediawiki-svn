package org.mediawiki.scavenger.action;

import java.sql.SQLException;
import java.util.List;

import org.mediawiki.scavenger.Differ;
import org.mediawiki.scavenger.PageFormatter;
import org.mediawiki.scavenger.Revision;

public class Diff extends PageAction {
	List<Differ.DiffChunk> diffchunks;
	Revision r1;
	Revision r2;
	PageFormatter r1formatter;
	
	public String pageExecute() throws SQLException {
		int rb = Integer.parseInt(req.getParameter("r2"));
		r2 = wiki.getRevision(rb);
		
		String ra = req.getParameter("r1");
		if (ra.equals("prev"))
			r1 = r2.prevRevision();
		else if (ra.equals("next"))
			r1 = r2.nextRevision();
		else
			r1 = wiki.getRevision(Integer.parseInt(ra));
		
		r1formatter = new PageFormatter(wiki);
		
		Differ d = new Differ(r1, r2);
		diffchunks = d.getChunks();
		req.setAttribute("diffchunks", diffchunks);
		req.setAttribute("r1", r1);
		req.setAttribute("r2", r2);
		req.setAttribute("r1text", r1formatter.getFormattedText(r1));
		return "diff";
	}	
}
