package org.mediawiki.scavenger;

import java.sql.SQLException;

import org.mediawiki.scavenger.markdown.MarkdownProcessor;

public class PageFormatter {
	Wiki wiki;
	
	public PageFormatter(Wiki w) {
		this.wiki = w;
	}
	
	/**
	 * Return formatted HTML of this revision;
	 */
	public String getFormattedText(Revision r) throws SQLException {
		return getFormattedText(r.getText());
	}

	public String getFormattedText(String t) throws SQLException {
		MarkdownProcessor proc = new MarkdownProcessor();
		return replaceLinks(proc.markdown(t));
	}

	/**
	 * Replace wiki links with HTML.
	 */
	public String replaceLinks(String text) throws SQLException {
		String[] linkparts = text.split("\\[\\[");
		StringBuilder result = new StringBuilder();
		for (String part : linkparts) {
			int end = part.indexOf("]]");

			if (end == -1) {
				result.append(part);
				continue;
			}
			
			String rest = part.substring(end + 2);
			String link = part.substring(0, end);
			String HTML = wiki.linkTo(link);
			result.append(HTML);
			result.append(rest);
		}
		
		return result.toString();
	}
}	
