package org.mediawiki.scavenger.tag;

import java.sql.SQLException;

import javax.servlet.jsp.JspException;
import javax.servlet.jsp.JspWriter;
import javax.servlet.jsp.tagext.TagSupport;

import org.mediawiki.scavenger.PageFormatter;
import org.mediawiki.scavenger.Wiki;

public class Parse extends TagSupport {
	String text = null;
	String var = null;
	
	public void init() {
		text = null;
		var = null;
	}
	
	public void setText(String t) {
		text = t;
	}
	
	public void setVar(String v) {
		var = v;
	}
	
	public int doStartTag() throws JspException {
		Wiki w = (Wiki) pageContext.getRequest().getAttribute("wiki");
		PageFormatter f = new PageFormatter(w);
		
		String formatted;
		try {
			formatted = f.getFormattedText(text);
			if (var == null) {
				JspWriter out = pageContext.getOut();
				out.print(formatted);
			} else {
				pageContext.setAttribute(var, formatted);
			}
		} catch (Exception e) {
			throw new JspException("Could not parse text", e);
		}
		
		return SKIP_BODY;
	}
}
