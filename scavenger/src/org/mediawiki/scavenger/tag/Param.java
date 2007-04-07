package org.mediawiki.scavenger.tag;

import javax.servlet.jsp.JspException;
import javax.servlet.jsp.tagext.TagSupport;

public class Param extends TagSupport {
	String name = null;
	String value = null;

	public void setName(String n) {
		this.name = n;
	}
	
	public void setValue(String v) {
		this.value = v;
	}

	public int doStartTag() throws JspException {
		((Page) getParent()).addParameter(name, value);
		return SKIP_BODY;
	}
}
