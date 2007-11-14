package org.wikimedia.lsearch.highlight;

import java.io.Serializable;
import java.util.ArrayList;

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
	protected ArrayList<Snippet> text = new ArrayList<Snippet>();
	
	public static final String SEPARATOR=" ... ";
	
	public HighlightResult(){		
	}
	
	public String getFormattedTitle(){
		return getFormatted(title);
	}
	
	public String getFormattedText(){
		StringBuilder sb = new StringBuilder();
		if(text==null || text.size()==0)
			return null;
		sb.append(SEPARATOR);
		for(Snippet t : text){
			sb.append(getFormatted(t));
			sb.append(SEPARATOR);
		}
		return sb.toString();
	}
	
	public String getFormattedRedirect(){
		return getFormatted(redirect);
	}
	
	public String getFormattedSection(){
		return getFormatted(section);
	}
	
	protected String getFormatted(Snippet snippet){
		if(snippet == null)
			return null;
		else
			return snippet.getFormatted();
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

	public Snippet getTitle() {
		return title;
	}

	public void setTitle(Snippet title) {
		this.title = title;
	}
	
	public void addTextSnippet(Snippet t){
		text.add(t);
	}

	public ArrayList<Snippet> getText() {
		return text;
	}
	

	
	
}
