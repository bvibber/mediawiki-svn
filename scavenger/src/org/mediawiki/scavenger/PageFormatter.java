package org.mediawiki.scavenger;

import java.sql.SQLException;

import com.petebevin.markdown.MarkdownProcessor;

public class PageFormatter {
	Revision rev;
	String formatted = null;
	
	public PageFormatter(Revision r) {
		this.rev = r;
	}
	
	/**
	 * Return formatted HTML of this revision;
	 */
	public String getFormattedText() throws SQLException {
		if (formatted != null)
			return formatted;
		
		String t = rev.getText();
		MarkdownProcessor proc = new MarkdownProcessor();
		return formatted = proc.markdown(t);
	}
}	
