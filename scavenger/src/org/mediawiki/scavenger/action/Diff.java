package org.mediawiki.scavenger.action;

import java.sql.SQLException;
import java.util.List;

import org.mediawiki.scavenger.Differ;
import org.mediawiki.scavenger.Revision;

public class Diff extends PageAction {
	List<Differ.DiffLine> difflines;
	Revision r1;
	Revision r2;
	
	public String pageExecute() throws SQLException {
		int ra = Integer.parseInt(((String[]) parameters.get("r1"))[0]);
		r1 = wiki.getRevision(ra);
		
		String[] rb = (String[]) parameters.get("r2");
		if (rb[0].equals("prev"))
			r2 = r1.prevRevision();
		else if (rb[0].equals("next"))
			r2 = r1.nextRevision();
		else
			r2 = wiki.getRevision(Integer.parseInt(rb[0]));
		
		Differ d = new Differ(r2, r1);
		difflines = d.format();
		return SUCCESS;
	}
	
	public List<Differ.DiffLine> getDifflines() {
		return difflines;
	}
	
	public Revision getR1() {
		return r1;
	}
	
	public Revision getR2() {
		return r2;
	}
}
