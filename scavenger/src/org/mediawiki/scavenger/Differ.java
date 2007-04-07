package org.mediawiki.scavenger;

import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;

import org.mediawiki.scavenger.Revision;
import org.suigeneris.jrcs.diff.Diff;
import org.suigeneris.jrcs.diff.DifferentiationFailedException;
import org.suigeneris.jrcs.diff.delta.Chunk;
import org.suigeneris.jrcs.diff.delta.Delta;
import org.suigeneris.jrcs.diff.myers.MyersDiff;

/**
 * Produce the differences between two revisions of a page;
 */
public class Differ {
	Revision a, b;
	org.suigeneris.jrcs.diff.Revision revision;
	String text_a, text_b;
	String[] lines_a, lines_b;
	
	public class DiffLine {
		String text;
		boolean context;
		
		public DiffLine(String t, boolean c) {
			text = t;
			context = c;
		}
		
		public String getText() {
			return text;
		}
		
		public boolean getContext() {
			return context;
		}
	}
	
	public class DiffBlock {
		int start, end;
		String[] text;
		Chunk chunk;
		List<DiffLine> lines;
		
		public DiffBlock(Chunk c, String[] text) {
			lines = new ArrayList<DiffLine>();
			chunk = c;
			start = c.first();
			end = c.last();
			
			/*
			 * Insert 5 lines of context on either side.
			 */
			for (int i = Math.max(0, start - 5); i < start; ++i) {
				lines.add(new DiffLine(text[i], true));
			}
			for (int i = start; i <= end; ++i) {
				lines.add(new DiffLine(text[i], false));
			}
			for (int i = end + 1, e = Math.min(text.length, end + 5); i < e; ++i) {
				lines.add(new DiffLine(text[i], true));
			}
		}
		
		public List<DiffLine> getLines() {
			return lines;
		}
	}
	
	public class DiffChunk {
		Chunk left, right;
		
		public DiffChunk(Chunk l, Chunk r) {
			left = l;
			right = r;
		}
		
		public DiffBlock getLeft() {
			return new DiffBlock(left, lines_a);
		}
		public DiffBlock getRight() {
			return new DiffBlock(right, lines_b);
		}
	}

	List<DiffChunk> chunks;
	
	public Differ(Revision a_, Revision b_) throws SQLException {
		a = a_;
		b = b_;
		text_a = a.getText();
		text_b = b.getText();
		lines_a = text_a.split("\n");
		lines_b = text_b.split("\n");
		try {
			revision = Diff.diff(lines_a, lines_b, new MyersDiff());
		} catch (DifferentiationFailedException e) {
			revision = null;
		}
		
		chunks = new ArrayList<DiffChunk>();
		for (int i = 0, end = revision.size(); i < end; ++i) {
			Delta d = revision.getDelta(i);
			Chunk c1 = d.getOriginal();
			Chunk c2 = d.getRevised();
			chunks.add(new DiffChunk(c1, c2));
		}
	}
	
	public List<DiffChunk> getChunks() {
		return chunks;
	}
}
