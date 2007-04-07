package org.mediawiki.scavenger.tag;

import java.io.IOException;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.jsp.JspException;
import javax.servlet.jsp.JspWriter;
import javax.servlet.jsp.tagext.BodyTagSupport;
import javax.servlet.jsp.tagext.TagSupport;

import org.mediawiki.scavenger.Title;

public class Page extends BodyTagSupport {
	Title title;
	String var;
	String action;
	Map<String, String> params;

	public void init() {
		title = null;
		var = null;
		action = null;
		params = null;
	}
	
	public void release() {
		init();
	}
	
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
		params = new HashMap<String, String>();
		return EVAL_BODY_BUFFERED;
	}
	
	public int doEndTag() throws JspException {
		try {
			StringBuilder result = new StringBuilder();
			String context = ((HttpServletRequest) pageContext.getRequest()).getContextPath();
			
			result.append(String.format("%1$s/%2$s/%3$s",
					context,
					action, 
					URLEncoder.encode(title.getURLText(), "UTF-8")));

			/*
			 * Build the query string.
			 */
			if (!params.isEmpty()) {
				boolean first = true;
				result.append("?");
				
				for (Map.Entry<String, String> e : params.entrySet()) {
					if (!first)
						result.append("&amp;");
					else
						first = false;

					result.append(URLEncoder.encode(e.getKey(), "UTF-8"));
					result.append("=");
					result.append(URLEncoder.encode(e.getValue(), "UTF-8"));
				}
			}

			String url = ((HttpServletResponse) pageContext.getResponse()).encodeURL(result.toString());

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
	
	public void addParameter(String name, String value) {
		params.put(name, value);
	}
}
