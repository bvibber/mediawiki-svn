package net.psammead.util;

/**
 * parses a text to find the line and column of a position within
 * valid line separators are CR, LF and CRLF   
 */
public class LineAndColumn {
	public final int line;
	public final int column;

	public LineAndColumn(String text, int position) {
		int		line	= 0;
		int		column	= 0;
		boolean	cr		= false;
		for (int i=0; i<position; i++) {
			char	c	= text.charAt(i);
			if (c == '\r') {
				column	= 0;
				line++;
				cr	= true;
			}
			else if (c == '\n') {
				if (!cr) {
					column	= 0;
					line++;
				}
				cr	= false;
			}
			else {
				column++;
				cr	= false;
			}
		}	
		
		// humans tend to count them from one
		this.line	= line+1;
		this.column	= column+1;
	}
	
	@Override
	public String toString() {
		return line + ":" + column;
	}
}
