package org.wikimedia.lsearch.highlight;

import java.io.Serializable;

/**
 * Result of higlighting, contains 
 * snippets for title, redirect, sections, and text 
 * @author rainman
 *
 */
public class HighlightResult implements Serializable {
	protected Snippet title = null;
	protected Snippet redirect = null;
	protected Snippet section = null;
	protected Snippet text = null;
	
	public HighlightResult(){		
	}

	public Snippet getRedirect() {
		return redirect;
	}

	public void setRedirect(Snippet redirect) {
		this.redirect = redirect;
	}

	public Snippet getSection() {
		return section;
	}

	public void setSection(Snippet section) {
		this.section = section;
	}

	public Snippet getText() {
		return text;
	}

	public void setText(Snippet text) {
		this.text = text;
	}

	public Snippet getTitle() {
		return title;
	}

	public void setTitle(Snippet title) {
		this.title = title;
	}
	
	
}
