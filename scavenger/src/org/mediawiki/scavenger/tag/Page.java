package org.mediawiki.scavenger.tag;

import java.io.IOException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.jsp.JspException;
import javax.servlet.jsp.JspWriter;
import javax.servlet.jsp.tagext.BodyTagSupport;

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
			String context = ((HttpServletRequest) pageContext.getRequest()).getContextPath();
			String query = null;
			
			/*
			 * Build the query string.
			 */
			if (!params.isEmpty()) {
				StringBuilder qb = new StringBuilder();
				boolean first = true;
				
				for (Map.Entry<String, String> e : params.entrySet()) {
					if (!first)
						qb.append("&");
					else
						first = false;

					qb.append(URLEncoder.encode(e.getKey(), "UTF-8"));
					qb.append("=");
					qb.append(URLEncoder.encode(e.getValue(), "UTF-8"));
				}
				query = qb.toString();
			}

			URI uri = new URI(null, null, 
						String.format("%s/%s/%s", context, action, title.getURLText()),
						query, null);
			
			String url = ((HttpServletResponse) pageContext.getResponse())
							.encodeURL(uri.toASCIIString());

			if (var == null) {
				JspWriter out = pageContext.getOut();
				out.print(url);
			} else {
				pageContext.setAttribute(var, url);
			}
		} catch (IOException e) {
			throw new JspException("Can't write");
		} catch (URISyntaxException e) {
		}
		
		return SKIP_BODY;
	}
	
	public void addParameter(String name, String value) {
		params.put(name, value);
	}
}
