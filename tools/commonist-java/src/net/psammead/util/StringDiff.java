package net.psammead.util;

import java.io.File;
import java.io.IOException;


/** static helper class: diff Strings in classic and side-by-side format - see man diff */
public final class StringDiff {
	public static void main(String... args) throws IOException {
		if (args.length != 3) {
			System.err.println("usage: StringDiff charset file1 file2");
			System.exit(1);
		}
		String	left	= IOUtil.readStringFromFile(new File(args[1]), args[0]);
		String	right	= IOUtil.readStringFromFile(new File(args[2]), args[0]);
		System.out.print(classic(left, right));
		System.out.flush();
	}
	
	private static final String	LEFT_PREFIX			= "< ";
	private static final String	RIGHT_PREFIX		= "> ";
	private static final String	CHANGE_SEPARATOR	= "---";
	private static final String	NUMBER_SEPARATOR	= ",";
	private static final String	DELETE_OP			= "d";
	private static final String	APPEND_OP			= "a";
	private static final String	CHANGE_OP			= "c";
	private	static final String	LINE_SEPARATOR		= "\n";
	
	/** instances of this class cannot be built */
	private StringDiff() {}

	/** classic diff */
	public static String classic(String left, String right) {
		final String[]		leftLines	= left.split(LINE_SEPARATOR);
		final String[]		rightLines	= right.split(LINE_SEPARATOR);
		
		final Diff			diff		= new Diff(leftLines, rightLines);
		final Diff.Change		script		= diff.diff2(false);
		
		final StringBuilder	out			= new StringBuilder();
		for (Diff.Change hunk=script; hunk!=null; hunk=hunk.link) {
			// determine left and right line ranges
			final int deletes		= hunk.deleted;
			final int inserts		= hunk.inserted;
			if (deletes == 0 && inserts == 0)	continue;
			final int leftFirst	= hunk.line0;
			final int rightFirst	= hunk.line1;
			final int leftLast	= hunk.line0 + deletes - 1;
			final int rightLast	= hunk.line1 + inserts - 1;
			
			// write out the line number header for this hunk:
			
			// left range
			if (deletes > 1) {
				out.append(Integer.toString(leftFirst + 1)).append(NUMBER_SEPARATOR);
			}
			out.append(Integer.toString(leftLast + 1));
			// change letter
				 if (inserts == 0)	out.append(DELETE_OP);
			else if (deletes == 0)	out.append(APPEND_OP);
			else					out.append(CHANGE_OP);
			// right range
			if (inserts > 1) {
				out.append(Integer.toString(rightFirst + 1)).append(NUMBER_SEPARATOR);
			}
			out.append(Integer.toString(rightLast + 1)).append(LINE_SEPARATOR);
	
			// write the lines that the left file has.
			if (deletes != 0) {
				for (int i=leftFirst; i<=leftLast; i++) {
					out.append(LEFT_PREFIX).append(leftLines[i]).append(LINE_SEPARATOR);
				}
			}
			// write a Separator between lines
			if (inserts != 0 && deletes != 0) {
				out.append(CHANGE_SEPARATOR).append(LINE_SEPARATOR);
			}
			// write the lines that the right file has.
			if (inserts != 0) {
				for (int i=rightFirst; i<=rightLast; i++) {
					out.append(RIGHT_PREFIX).append(rightLines[i]).append(LINE_SEPARATOR);
				}
			}
		}
		return out.toString();
	}
	
	/** side-by-side diff */
	public static String sideBySide(String left, String right, String leftPrefix, String rightPrefix, String commonPrefix, String lineSeparator) {
		final String[]		leftLines	= left.split(lineSeparator);
		final String[]		rightLines	= right.split(lineSeparator);
		final Diff			diff		= new Diff(leftLines, rightLines);
		final Diff.Change	script		= diff.diff2(false);
		final StringBuilder	out			= new StringBuilder();
		
		int	cursor	= 0;
		for (Diff.Change hunk=script; hunk!=null; hunk=hunk.link) {
			final int	leftPos		= hunk.line0;
			final int deletes		= hunk.deleted;
			final int rightPos	= hunk.line1;
			final int inserts		= hunk.inserted;
			
			// output common lines
			for (int line=cursor; line<leftPos; line++) {
				out.append(commonPrefix).append(leftLines[line]).append(lineSeparator);
			}
			
			// output left lines
			for (int line=leftPos; line<leftPos+deletes; line++) {
				out.append(leftPrefix).append(leftLines[line]).append(lineSeparator);
			}
			
			// output right lines
			for (int line=rightPos; line<rightPos+inserts; line++) {
				out.append(rightPrefix).append(rightLines[line]).append(lineSeparator);
			}

			cursor	= leftPos	+ deletes;
		}
		
		// append rest of common lines
		for (int line=cursor; line<leftLines.length; line++) {
			out.append(commonPrefix).append(leftLines[line]).append(lineSeparator);
		}
		return out.toString();
	}
	
	public static String editScript(String left, String right) {
		final String[]	file0	= left.split(LINE_SEPARATOR);
		final String[]	file1	= right.split(LINE_SEPARATOR);
		
		final String lf = "\n";
		final StringBuilder	out	= new StringBuilder();
		
		Diff.Change	change	= new Diff(file0, file1).diff2(true);
		while (change != null) {
			// Print this hunk.
			if (change.deleted != 0 || change.inserted != 0) {
				final int	last0	= change.line0 + change.deleted  - 1;
				final int	last1	= change.line1 + change.inserted - 1;
				
				// Print out the line number header for this hunk
				final String numberRange = (last0 > change.line0 ? "" + (change.line0+1) + ',' + (last0+1) : ""+ (last0+1));
				final char changeLetter = (change.inserted == 0 ? 'd' : change.deleted == 0 ? 'a' : 'c');
				out.append(numberRange).append(changeLetter).append(lf);
				
				// Print new/changed lines from second file, if needed
				if (change.inserted != 0) {
					boolean inserting = true;
					for (int i=change.line1; i<=last1; i++) {
						// Resume the insert, if we stopped.
						if (!inserting) {
							out.append(i - change.line1 + change.line0 + "a").append(lf);
						}
						inserting = true;
				
						// If the file's line is just a dot, it would confuse `ed'. So
						// output it with a double dot, and set the flag LEADING_DOT so
						// that we will output another ed-command later to change the
						// double dot into a single dot.
				
						if (".".equals(file1[i])) {
							out.append("..").append(lf);
							out.append(".").append(lf);
							// Now change that double dot to the desired single dot.
							out.append(i - change.line1 + change.line0 + 1 + "s/^\\.\\././").append(lf);
							inserting = false;
						} 
						else {
							// Line is not `.', so output it unmodified.
							out.append(file1[i].toString()).append(lf);
						}
					}
				
					// End insert mode, if we are still in it.
					if (inserting) {
						out.append(".").append(lf);
					}
				}
			}
			change	= change.link;
		}
		return out.toString();
	}
	
//	public static String editScript2(String left, String right) {
//		String[]	file0	= left.split(LINE_SEPARATOR);
//		String[]	file1	= right.split(LINE_SEPARATOR);
//		Diff.Change	change	= new Diff(file0, file1).diff2(true);
//		
//		final StringBuilder	out	= new StringBuilder();
//		
//		while (change != null) {
//			// Print this hunk.
//			if (change.deleted != 0 || change.inserted != 0) {
//				final int	last0	= change.line0 + change.deleted  - 1;
//				final int	last1	= change.line1 + change.inserted - 1;
//				if (change.inserted == 0) {
//					out.append("delete " + change.line0 + ".." + last0 + LINE_SEPARATOR);
//				}
//				else if (change.deleted == 0) {
//					out.append("insert " + change.line1 + ".." + last1 + " at " + change.line0 + LINE_SEPARATOR);
//				}
//				else {
//					out.append("replace " + change.line0 + ".." + last0 + " with " + change.line1 + ".." + last1 + LINE_SEPARATOR);
//				}
//			}
//			change	= change.link;
//		}
//		return out.toString();
//	}
}
