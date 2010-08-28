package net.psammead.util;

import java.util.*;

/** A class to compare vectors of objects.  The result of comparison
  * is a list of <code>change</code> objects which form an
  * edit script.  The objects compared are traditionally lines
  * of text from two files.  Comparison options such as "ignore
  * whitespace" are implemented by modifying the <code>equals</code>
  * and <code>hashcode</code> methods for the objects compared.
  *<p>
  * The basic algorithm is described in: </br>
  * "An O(ND) Difference Algorithm and its Variations", Eugene Myers,
  * Algorithmica Vol. 1 No. 2, 1986, p 251.  
  *<p>
  * This class outputs different results from GNU diff 1.15 on some
  * inputs.  Our results are actually better (smaller change list, smaller
  * total size of changes), but it would be nice to know why.  Perhaps
  * there is a memory overwrite bug in GNU diff 1.15.
  *
  * @author Stuart D. Gathman, translated from GNU diff 1.15
  * Copyright (C) 2000  Business Management Systems, Inc.
  *<p>
  * This program is free software; you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation; either version 1, or (at your option)
  * any later version.
  *<p>
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *<p>
  * You should have received a copy of the <a href=COPYING.txt>
  * GNU General Public License</a>
  * along with this program; if not, write to the Free Software
  * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
  */
public final class Diff {
	//------------------------------------------------------------------------------
	//## fields	

	/** 1 more than the maximum equivalence value used for this or its sibling file. */
	private int equivMax = 1;

	/** When set to true, the comparison uses a heuristic to speed it up.
	  * With this heuristic, for files with a constant small density of changes, 
	  * the algorithm is linear in the file size.  
	  */
	public boolean heuristic = false;

	/** When set to true, the algorithm returns a guarranteed minimal
	  * set of changes.  This makes things slower, sometimes much slower. 
	  */
	public boolean            noDiscards = false;
	
	// Vectors being compared.
	private int[]             xVec;   
	private int[]             yVec;
	// Vector, indexed by diagonal, containing the X coordinate of the point furthest
	// along the given diagonal in the forward search of the edit matrix.
	private int[]             fDiag;    
	// Vector, indexed by diagonal, containing the X coordinate of the point furthest
	// along the given diagonal in the backward search of the edit matrix.
	private int[]             bDiag;    

	private int               fDiagOff;
	private int               bDiagOff;

	private final FileData[]  fileVec = new FileData[2];
	private int               cost;

	//------------------------------------------------------------------------------
	//## constructor

	/** Prepare to find differences between two arrays.  
	  * Each element of
	  * the arrays is translated to an "equivalence number" based on
	  * the result of <code>equals</code>.  The original Object arrays
	  * are no longer needed for computing the differences.  They will
	  * be needed again later to print the results of the comparison as
	  * an edit script, if desired.
	  */
	public Diff(Object[] a, Object[] b) {
		Map<Object, Integer>	h		= new HashMap<Object, Integer>(a.length + b.length);
		fileVec[0]	= new FileData(a, h);
		fileVec[1]	= new FileData(b, h);
	}

	/** Find the midpoint of the shortest edit script for a specified
	  * portion of the two files.
	  *
	  * We scan from the beginnings of the files, and simultaneously from the ends,
	  * doing a breadth-first search through the space of edit-sequence.
	  * When the two searches meet, we have found the midpoint of the shortest
	  * edit sequence.
	  *
	  * The value returned is the number of the diagonal on which the midpoint lies.
	  * The diagonal number equals the number of inserted lines minus the number
	  * of deleted lines (counting only lines before the midpoint).
	  * The edit cost is stored into COST; this is the total number of
	  * lines inserted or deleted (counting only lines before the midpoint).
	  * 
	  * This function assumes that the first lines of the specified portions
	  * of the two files do not match, and likewise that the last lines do not
	  * match.  The caller must trim matching lines from the beginning and end
	  * of the portions it is going to specify.
	  *
	  * Note that if we return the "wrong" diagonal value, or if
	  * the value of bDiag at that diagonal is "wrong",
	  * the worst this can do is cause suboptimal diff output.
	  * It cannot cause incorrect diff output.  
	  */
	private int diag(int xOff, int xLim, int yOff, int yLim) {
		final int[]	fd   = fDiag;    	// Give the compiler a chance.
		final int[]	bd   = bDiag;    	// Additional help for the compiler.
		final int[] xv   = xVec;    	// Still more help for the compiler.
		final int[] yv   = yVec;   		// And more and more . . .
		final int   dMin = xOff - yLim;	// Minimum valid diagonal.
		final int   dMax = xLim - yOff;	// Maximum valid diagonal.
		final int   fMid = xOff - yOff;	// Center diagonal of top-down search.
		final int   bMid = xLim - yLim;	// Center diagonal of bottom-up search.
		int         fMin = fMid;    	// Limits of top-down search.
		int         fMax = fMid;
		int         bMin = bMid;   		// Limits of bottom-up search.
		int         bMax = bMid;

		// True if southeast corner is on an odd diagonal with respect to the northwest.
		final boolean odd = (fMid - bMid & 1) != 0;
		fd[fDiagOff + fMid] = xOff;
		bd[bDiagOff + bMid] = xLim;
		for (int c=1;; ++c) {
			int     d;    // Active diagonal.
			boolean bigSnake = false;

			// Extend the top-down search by an edit step in each diagonal.
			if (fMin > dMin)	fd[fDiagOff + --fMin - 1] = -1;
			else				++fMin;
			if (fMax < dMax)	fd[fDiagOff + ++fMax + 1] = -1;
			else				--fMax;
			for (d = fMax; d >= fMin; d -= 2) {
				int x;
				int y;
				int oldX;
				int tLo = fd[fDiagOff + d - 1];
				int tHi = fd[fDiagOff + d + 1];
				if (tLo >= tHi)	x = tLo + 1;
				else			x = tHi;
				oldX	= x;
				y		= x - d;
				while (x < xLim && y < yLim && xv[x] == yv[y]) {
					++x;
					++y;
				}
				if (x - oldX > 20)	bigSnake = true;
				fd[fDiagOff + d] = x;
				if (odd && bMin <= d && d <= bMax && bd[bDiagOff + d] <= fd[fDiagOff + d]) {
					cost = 2 * c - 1;
					return d;
				}
			}

			// Similar extend the bottom-up search.
			if (bMin > dMin)	bd[bDiagOff + --bMin - 1] = Integer.MAX_VALUE;
			else				++bMin;
			if (bMax < dMax)	bd[bDiagOff + ++bMax + 1] = Integer.MAX_VALUE;
			else				--bMax;
			for (d = bMax; d >= bMin; d -= 2) {
				int x;
				int y;
				int oldX;
				int tLo = bd[bDiagOff + d - 1];
				int tHi = bd[bDiagOff + d + 1];
				if (tLo < tHi)	x = tLo;
				else			x = tHi - 1;
				oldX	= x;
				y		= x - d;
				while (x > xOff && y > yOff && xv[x - 1] == yv[y - 1]) {
					--x;
					--y;
				}
				if (oldX - x > 20)	bigSnake = true;
				bd[bDiagOff + d] = x;
				if (!odd && fMin <= d && d <= fMax && bd[bDiagOff + d] <= fd[fDiagOff + d]) {
					cost = 2 * c;
					return d;
				}
			}

			/* Heuristic: check occasionally for a diagonal that has made
			   lots of progress compared with the edit distance.
			   If we have any such, find the one that has made the most
			   progress and return it as if it had succeeded.
			
			   With this heuristic, for files with a constant small density
			   of changes, the algorithm is linear in the file size.  */
			if (c > 200 && bigSnake && heuristic) {
				int best    = 0;
				int bestPos = -1;
				for (d = fMax; d >= fMin; d -= 2) {
					int dd = d - fMid;
					if ((fd[fDiagOff + d] - xOff) * 2 - dd > 12 * (c + (dd > 0 ? dd : -dd))) {
						if (fd[fDiagOff + d] * 2 - dd > best && fd[fDiagOff + d] - xOff > 20 && fd[fDiagOff + d] - d - yOff > 20) {
							int k;
							int x = fd[fDiagOff + d];

							// We have a good enough best diagonal; now insist that it end with a significant snake.
							for (k = 1; k <= 20; k++) {
								if (xVec[x - k] != yVec[x - d - k])	break;
							}
							if (k == 21) {
								best    = fd[fDiagOff + d] * 2 - dd;
								bestPos = d;
							}
						}
					}
				}
				if (best > 0) {
					cost = 2 * c - 1;
					return bestPos;
				}
				best = 0;
				for (d = bMax; d >= bMin; d -= 2) {
					int dd = d - bMid;
					if ((xLim - bd[bDiagOff + d]) * 2 + dd > 12 * (c + (dd > 0 ? dd : -dd))) {
						if ((xLim - bd[bDiagOff + d]) * 2 + dd > best && xLim - bd[bDiagOff + d] > 20 && yLim - (bd[bDiagOff + d] - d) > 20) {

							// We have a good enough best diagonal; now insist that it end with a significant snake. 
							int k;
							int x = bd[bDiagOff + d];
							for (k = 0; k < 20; k++) {
								if (xVec[x + k] != yVec[x - d + k])	break;
							}
							if (k == 20) {
								best    = (xLim - bd[bDiagOff + d]) * 2 + dd;
								bestPos = d;
							}
						}
					}
				}
				if (best > 0) {
					cost = 2 * c - 1;
					return bestPos;
				}
			}
		}
	}

	/** Compare in detail contiguous subsequences of the two files
	  * which are known, as a whole, to match each other.
	  *
	  * The results are recorded in the vectors fileVec[N].changedFlag, by
	  * storing a 1 in the element for each line that is an insertion or deletion.
	  *
	  * The subsequence of file 0 is [XOFF, XLIM) and likewise for file 1.
	  *
	  * Note that XLIM, YLIM are exclusive bounds.
	  * All line numbers are origin-0 and discarded lines are not counted.  
	  */
	private void compareSeq(int xOff, int xLim, int yOff, int yLim) {

		// Slide down the bottom initial diagonal.
		while (xOff < xLim && yOff < yLim && xVec[xOff] == yVec[yOff]) {
			++xOff;
			++yOff;
		}

		// Slide up the top initial diagonal.
		while (xLim > xOff && yLim > yOff && xVec[xLim - 1] == yVec[yLim - 1]) {
			--xLim;
			--yLim;
		}

		// Handle simple cases.
		if (xOff == xLim) {
			while (yOff < yLim)	fileVec[1].changedFlag[1 + fileVec[1].realIndexes[yOff++]] = true;
		}
		else if (yOff == yLim) {
			while (xOff < xLim)	fileVec[0].changedFlag[1 + fileVec[0].realIndexes[xOff++]] = true;
		}
		else {

			// Find a point of correspondence in the middle of the files. 
			int d = diag(xOff, xLim, yOff, yLim);
			int c = cost;
			/*int f = fDiag[fDiagOff + d]; */
			int b = bDiag[bDiagOff + d];
			// This should be impossible, because it implies that one of the two subsequences is empty,
			// and that case was handled above without calling `diag'. Let's verify that this is true.  */
			if (c == 1)	throw new IllegalArgumentException("Empty subsequence");
			
			// Use that point to split this problem into two subproblems. 
			compareSeq(xOff, b, yOff, b - d);

			// This used to use f instead of b, but that is incorrect!
			// It is not necessarily the case that diagonal d has a snake from b to f. 
			compareSeq(b, xLim, b - d, yLim);
		}
	}

	/** Discard lines from one file that have no matches in the other file. */
	private void discardConfusingLines() {
		fileVec[0].discardConfusingLines(fileVec[1]);
		fileVec[1].discardConfusingLines(fileVec[0]);
	}

	private boolean inhibit = false;

	/** Adjust inserts/deletes of blank lines to join changes as much as possible. */
	private void shiftBoundaries() {
		if (inhibit)	return;
		fileVec[0].shiftBoundaries(fileVec[1]);
		fileVec[1].shiftBoundaries(fileVec[0]);
	}

	/** Scan the tables of which lines are inserted and deleted, producing an edit script in reverse order.  */
	private Change buildScriptReverse() {
		Change          script   = null;
		final boolean[] changed0 = fileVec[0].changedFlag;
		final boolean[] changed1 = fileVec[1].changedFlag;
		final int       len0     = fileVec[0].bufferedLines;
		final int       len1     = fileVec[1].bufferedLines;

		// Note that changedN[len0] does exist, and contains 0.  
		int i0 = 0;
		int i1 = 0;
		while (i0 < len0 || i1 < len1) {
			if (changed0[1 + i0] || changed1[1 + i1]) {
				int line0 = i0;
				int line1 = i1;

				// Find # lines changed here in each file. 
				while (changed0[1 + i0])	++i0;
				while (changed1[1 + i1])	++i1;

				// Record this change. 
				script = new Change(line0, line1, i0 - line0, i1 - line1, script);
			}

			// We have reached lines in the two files that match each other. 
			i0++;
			i1++;
		}
		return script;
	}

	/** Scan the tables of which lines are inserted and deleted, producing an edit script in forward order.  */
	private Change buildScript() {
		Change          script   = null;
		final boolean[] changed0 = fileVec[0].changedFlag;
		final boolean[] changed1 = fileVec[1].changedFlag;
		final int       len0     = fileVec[0].bufferedLines;
		final int       len1     = fileVec[1].bufferedLines;
		int             i0       = len0;
		int             i1       = len1;

		// Note that changedN[-1] does exist, and contains 0. 
		while (i0 >= 0 || i1 >= 0) {
			if (changed0[i0] || changed1[i1]) {
				int line0 = i0;
				int line1 = i1;

				// Find # lines changed here in each file. 
				while (changed0[i0])	--i0;
				while (changed1[i1])	--i1;

				// Record this change. 
				script = new Change(i0, i1, line0 - i0, line1 - i1, script);
			}

			// We have reached lines in the two files that match each other. 
			i0--;
			i1--;
		}
		return script;
	}

	// Report the differences of two files.  DEPTH is the current directory depth.
	public Change diff2(final boolean reverse) {

		// Some lines are obviously insertions or deletions because they don't match anything.  
		// Detect them now, and avoid even thinking about them in the main comparison algorithm. 
		discardConfusingLines();

		//  Now do the main comparison algorithm, considering just the unDiscarded lines. 
		xVec      = fileVec[0].unDiscarded;
		yVec      = fileVec[1].unDiscarded;
		int diags = fileVec[0].nonDiscardedLines + fileVec[1].nonDiscardedLines + 3;
		fDiag     = new int[diags];
		fDiagOff  = fileVec[1].nonDiscardedLines + 1;
		bDiag     = new int[diags];
		bDiagOff  = fileVec[1].nonDiscardedLines + 1;
		compareSeq(0, fileVec[0].nonDiscardedLines, 0, fileVec[1].nonDiscardedLines);
		fDiag = null;
		bDiag = null;

		// Modify the results slightly to make them prettier in cases where that can validly be done. 
		shiftBoundaries();

		// Get the results of comparison in the form of a chain of `struct change's -- an edit script. 
		if (reverse)	return buildScriptReverse();
		else			return buildScript();
	}

	/** The result of comparison is an "edit script": a chain of change objects.
	  * Each change represents one place where some lines are deleted
	  * and some are inserted.
	  *
	  * LINE0 and LINE1 are the first affected lines in the two files (origin 0).
	  * DELETED is the number of lines deleted here from file 0.
	  * INSERTED is the number of lines inserted here in file 1.
      *
	  * If DELETED is 0 then LINE0 is the number of the line before
	  * which the insertion was done; vice versa for INSERTED and LINE1.  
	  */
	public static class Change {

		/** Previous or next edit command. */
		public Change link;

		/** # lines of file 1 changed here.  */
		public final int inserted;

		/** # lines of file 0 changed here.  */
		public final int deleted;

		/** Line number of 1st deleted line.  */
		public final int line0;

		/** Line number of 1st inserted line.  */
		public final int line1;

		/** Cons an additional entry onto the front of an edit script OLD.
	      * LINE0 and LINE1 are the first affected lines in the two files (origin 0).
	      * DELETED is the number of lines deleted here from file 0.
	      * INSERTED is the number of lines inserted here in file 1.
		  * 
	      * If DELETED is 0 then LINE0 is the number of the line before
	      * which the insertion was done; vice versa for INSERTED and LINE1.  
		  */
		Change(int line0, int line1, int deleted, int inserted, Change old) {
			this.line0    = line0;
			this.line1    = line1;
			this.inserted = inserted;
			this.deleted  = deleted;
			this.link     = old;

			//System.err.println(line0+","+line1+","+inserted+","+deleted);
		}
	}

	/** Data on one input file being compared.  */
	class FileData {

		/** Number of elements (lines) in this file. */
		final int bufferedLines;

		/** Vector, indexed by line number, containing an equivalence code for
	      * each line.  It is this vector that is actually compared with that
	      * of another file to generate differences. 
		  */
		private final int[] equivs;

		/** Vector, like the previous one except that
	      * the elements for discarded lines have been squeezed out.  
		  */
		final int[] unDiscarded;

		/** Vector mapping virtual line numbers (not counting discarded lines)
	      * to real ones (counting those lines).  Both are origin-0.  
		  */
		final int[] realIndexes;

		/** Total number of nondiscarded lines. */
		int nonDiscardedLines;

		/** Array, indexed by real origin-1 line number, containing true for a line that is an insertion or a deletion.
	      * The results of comparison are stored here.  
		  */
		boolean[] changedFlag;

		FileData(Object[] data, Map<Object, Integer> h) {
			bufferedLines = data.length;
			equivs         = new int[bufferedLines];
			unDiscarded    = new int[bufferedLines];
			realIndexes    = new int[bufferedLines];
			for (int i = 0; i < data.length; ++i) {
				Integer ir = h.get(data[i]);
				if (ir == null)	h.put(data[i], new Integer(equivs[i] = equivMax++));
				else			equivs[i] = ir.intValue();
			}
		}


		/** Allocate changed array for the results of comparison.  */
		void clear() {

			// Allocate a flag for each line of each file, saying whether that line is an insertion or deletion.
			// Allocate an extra element, always zero, at each end of each vector.
			changedFlag = new boolean[bufferedLines + 2];
		}

		/** Return equivCount[I] as the number of lines in this file
		  * that fall in equivalence class I.
		  * @return the array of equivalence class counts.
		  */
		int[] equivCount() {
			int[] equivCount = new int[equivMax];
			for (int i = 0; i < bufferedLines; ++i)	++equivCount[equivs[i]];
			return equivCount;
		}

		/** Discard lines that have no matches in another file.
		  *
		  * A line which is discarded will not be considered by the actual
		  * comparison algorithm; it will be as if that line were not in the file.
		  * The file's `realIndexes' table maps virtual line numbers
		  * (which don't count the discarded lines) into real line numbers;
		  * this is how the actual comparison algorithm produces results
		  * that are comprehensible when the discarded lines are counted.
		  * <p>
		  * When we discard a line, we also mark it as a deletion or insertion
		  * so that it will be printed in the output.  
		  * @param f the other file   
		  */
		void discardConfusingLines(FileData f) {
			clear();

			// Set up table of which lines are going to be discarded.
			final byte[] discarded = discardable(f.equivCount());

			// Don't really discard the provisional lines except when they occur in a run of discardables, 
			// with nonprovisionals at the beginning and end. 
			filterDiscards(discarded);

			// Actually discard the lines.
			discard(discarded);
		}

		/** Mark to be discarded each line that matches no line of another file.
		  * If a line matches many lines, mark it as provisionally discardable.  
		  * @see equivCount()
		  * @param counts The count of each equivalence number for the other file.
		  * @return 0=nondiscardable, 1=discardable or 2=provisionally discardable for each line
		  */
		private byte[] discardable(final int[] counts) {
			final int    end      = bufferedLines;
			final byte[] discards = new byte[end];
			//final int[]  equivs   = this.equivs;
			int          many     = 5;
			int          tem      = end / 64;

			// Multiply MANY by approximate square root of number of lines.
			// That is the threshold for provisionally discardable lines. 
			while ((tem = tem >> 2) > 0)	many *= 2;
			for (int i = 0; i < end; i++) {
				int nMatch;
				if (equivs[i] == 0)	continue;
				nMatch = counts[equivs[i]];
				if (nMatch == 0)		discards[i] = 1;
				else if (nMatch > many)	discards[i] = 2;
			}
			return discards;
		}

		/** Don't really discard the provisional lines except when they occur
		  * in a run of discardables, with nonprovisionals at the beginning
		  * and end.  
		  */
		private void filterDiscards(final byte[] discards) {
			final int end = bufferedLines;
			for (int i = 0; i < end; i++) {

				// Cancel provisional discards not in middle of run of discards. 
				if (discards[i] == 2)	discards[i] = 0;
				else if (discards[i] != 0) {

					// We have found a nonprovisional discard. 
					int j;
					int length;
					int provisional = 0;

					// Find end of this run of discardable lines. Count how many are provisionally discardable. 
					for (j = i; j < end; j++) {
						if (discards[j] == 0)	break;
						if (discards[j] == 2)	++provisional;
					}

					// Cancel provisional discards at end, and shrink the run. 
					while (j > i && discards[j - 1] == 2) {
						discards[--j] = 0;
						--provisional;
					}

					// Now we have the length of a run of discardable lines whose first and last are not provisional. 
					length = j - i;

					// If 1/4 of the lines in the run are provisional, cancel discarding of all provisional lines in the run.
					if (provisional * 4 > length) {
						while (j > i) {
							if (discards[--j] == 2) discards[j] = 0;
						}
					}
					else {
						int consec;
						int minimum = 1;
						int tem     = length / 4;

						//  MINIMUM is approximate square root of LENGTH/4.
						//  A subrun of two or more provisionals can stand when LENGTH is at least 16.
						//  A subrun of 4 or more can stand when LENGTH >= 64. 
						while ((tem = tem >> 2) > 0)	minimum *= 2;
						minimum++;

						// Cancel any subrun of MINIMUM or more provisionals within the larger run.
						for (j = 0, consec = 0; j < length; j++) {
							if (discards[i + j] != 2)		consec = 0;
							//  Back up to start of subrun, to cancel it all. 
							else if (minimum == ++consec)	 j -= consec;
							else if (minimum < consec)		discards[i + j] = 0;
						}
						
						// Scan from beginning of run until we find 3 or more nonprovisionals in a row
						// or until the first nonprovisional at least 8 lines in. Until that point, cancel any provisionals. 
						for (j = 0, consec = 0; j < length; j++) {
							if (j >= 8 && discards[i + j] == 1)	break;
							if (discards[i + j] == 2) {
								consec = 0;
								discards[i + j] = 0;
							}
							else if (discards[i + j] == 0) {
								consec = 0;
							}
							else {
								consec++;
							}
							if (consec == 3)	break;
						}

						// I advances to the last line of the run. 
						i += length - 1;

						// Same thing, from end. 
						for (j = 0, consec = 0; j < length; j++) {
							if (j >= 8 && discards[i - j] == 1)	break;
							if (discards[i - j] == 2) {
								consec = 0;
								discards[i - j] = 0;
							}
							else if (discards[i - j] == 0) {
								consec = 0;
							}
							else {
								consec++;
							}
							if (consec == 3)	break;
						}
					}
				}
			}
		}

		/** Actually discard the lines.
	      * @param discards flags lines to be discarded
	      */
		private void discard(final byte[] discards) {
			final int end = bufferedLines;
			int       j = 0;
			for (int i = 0; i < end; ++i) {
				if (noDiscards || discards[i] == 0) {
					unDiscarded[j]   = equivs[i];
					realIndexes[j++] = i;
				}
				else {
					changedFlag[1 + i] = true;
				}
			}
			nonDiscardedLines = j;
		}

		/** Adjust inserts/deletes of blank lines to join changes
	      * as much as possible.
		  *
		  * We do something when a run of changed lines include a blank
		  * line at one end and have an excluded blank line at the other.
		  * We are free to choose which blank line is included.
		  * `compareSeq' always chooses the one at the beginning,
		  * but usually it is cleaner to consider the following blank line
		  * to be the "change".  The only exception is if the preceding blank line
		  * would join this change to other changes.  
		  * @param f the file being compared against
		  */
		void shiftBoundaries(FileData f) {
			final boolean[] changed         = changedFlag;
			final boolean[] otherChanged   = f.changedFlag;
			int             i               = 0;
			int             j               = 0;
			int             iEnd           = bufferedLines;
			int             preceding       = -1;
			int             otherPreceding = -1;
			for (;;) {
				int start;
				int end;
				int otherStart;

				// Scan forwards to find beginning of another run of changes.
				// Also keep track of the corresponding point in the other file. 
				while (i < iEnd && !changed[1 + i]) {
					// Non-corresponding lines in the other file will count as the preceding batch of changes.
					while (otherChanged[1 + j++])	otherPreceding = j;
					i++;
				}
				if (i == iEnd)	break;
				start           = i;
				otherStart     = j;
				for (;;) {
					// Now find the end of this run of changes. 
					while (i < iEnd && changed[1 + i])	i++;
					end = i;

					// If the first changed line matches the following unchanged one,
					// and this run does not follow right after a previous run,
					// and there are no lines deleted from the other file here,
					// then classify the first changed line as unchanged
					// and the following line as changed in its place.  
					// You might ask, how could this run follow right after another?
					// Only because the previous run was shifted here.  
					if (end != iEnd && equivs[start] == equivs[end] && !otherChanged[1 + j] && end != iEnd && !((preceding >= 0 && start == preceding) || (otherPreceding >= 0 && otherStart == otherPreceding))) {
						changed[1 + end++]   = true;
						changed[1 + start++] = false;
						++i;

						// Since one line-that-matches is now before this run instead of after, 
						// we must advance in the other file to keep in synch.
						++j;
					}
					else {
						break;
					}
				}
				preceding       = i;
				otherPreceding = j;
			}
		}
	}
}