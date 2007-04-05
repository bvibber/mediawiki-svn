package org.mediawiki.scavenger;

import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;

import org.incava.util.diff.Diff;
import org.incava.util.diff.Difference;
import org.mediawiki.scavenger.Revision;

/**
 * Produce the differences between two revisions of a page;
 */
public class Differ {
	Revision a, b;
	Diff diff;
	List diffs;
	String text_a, text_b;
	String[] lines_a, lines_b;
	
	public Differ(Revision a, Revision b) throws SQLException {
		this.a = a;
		this.b = b;
		text_a = a.getText();
		text_b = b.getText();
		lines_a = text_a.split("\n");
		lines_b = text_b.split("\n");
		diff = new Diff(lines_a, lines_b);
		diffs = diff.diff();
	}
	
	public List<Difference> diff() throws SQLException {	
		return diffs;
	}
	
	public static class DiffLine {
		int line;
		boolean addition, deletion;
		String text;
		
		public DiffLine(int no, boolean add, boolean del, String text) {
			this.line = no;
			this.addition = add;
			this.deletion = del;
			this.text = text;
		}
		
		public int getLine() {
			return line;
		}
		
		public boolean getAddition() {
			return addition;
		}
		
		public boolean getDeletion() {
			return deletion;
		}
		
		public String getText() {
			return text;
		}
	}
	
	public List<DiffLine> format() {
		List<DiffLine> result = new LinkedList<DiffLine>();

		Iterator it = diffs.iterator();
		int last = 0;
		while (it.hasNext()) {
			Difference d = (Difference) it.next();
			int dstart = d.getDeletedStart(), dend = d.getDeletedEnd();
			int astart = d.getAddedStart(), aend = d.getAddedEnd();
		
			int stop = last;
			if (dend != -1)
				stop = dend;
			if (aend != -1 && aend > stop)
				stop = aend;
			
			for (; last < stop; ++last) {
				result.add(new DiffLine(last + 1, false, false, lines_b[last]));
			}
			
			if (dend != -1) {
				for (last = dstart; last <= dend; ++last) {
					result.add(new DiffLine(last + 1, false, true, lines_a[last]));
				}
			}
			
			if (aend != -1) {
				for (last = astart; last <= aend; ++last) {
					result.add(new DiffLine(last + 1, true, false, lines_b[last]));
				}
			}
		}
		
		for (; last < lines_b.length; ++last)
			result.add(new DiffLine(last + 1, false, false, lines_b[last]));
		
		return result;
	}
}
