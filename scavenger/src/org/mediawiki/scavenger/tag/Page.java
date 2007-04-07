package org.mediawiki.scavenger.tag;

import java.io.IOException;
import java.net.URLEncoder;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.jsp.JspException;
import javax.servlet.jsp.JspWriter;
import javax.servlet.jsp.tagext.BodyTagSupport;
import javax.servlet.jsp.tagext.TagSupport;

import org.mediawiki.scavenger.Title;

public class Page extends BodyTagSupport {
	Title title = null;
	String var = null;
	String action = null;

	public void setName(String name) {
		title = new Title(name);
	}
	
	public void setVar(String v) {
		var = v;
	}
	
	public void setAction(String a) {
		action = a;
	}
	
	public int doStartTag() throws JspException {
		try {
			String context = ((HttpServletRequest) pageContext.getRequest()).getContextPath();
			String url = String.format("%1$s/%2$s/%3$s",
					context,
					action, 
					URLEncoder.encode(title.getURLText(), "UTF-8"));
			url = ((HttpServletResponse) pageContext.getResponse()).encodeURL(url);
			
			if (var == null) {
				JspWriter out = pageContext.getOut();
				out.print(url);
			} else {
				pageContext.setAttribute(var, url);
			}
		} catch (IOException e) {
			throw new JspException("Can't write");
		}
		
		return SKIP_BODY;
	}
}
