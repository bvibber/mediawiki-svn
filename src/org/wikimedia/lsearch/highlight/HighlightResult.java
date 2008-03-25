package org.wikimedia.lsearch.highlight;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.HashSet;

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
	protected String date = null;
	protected int wordCount = 0;
	protected long size = 0;
	
	public static final String SEPARATOR=" <b>...</b> ";
	
	public HighlightResult(){
	}
	
	public String getFormattedTitle(){
		return getFormatted(title);
	}
	
	public String getFormattedText(){
		StringBuilder sb = new StringBuilder();
		if(text==null || text.size()==0)
			return null;
		for(Snippet t : text){
			sb.append(getFormatted(t).trim());
			if(t.getSuffix() != null)
				sb.append(t.getSuffix());
			else
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
	
	public void insertTextSnippet(Snippet t, int index){
		text.add(index,t);
	}
	
	public void replaceTextSnippet(Snippet t, int index){
		text.remove(index);
		text.add(index,t);
	}

	public ArrayList<Snippet> getText() {
		return text;
	}
	
	public int textLength(){
		int len = 0;
		for(Snippet t : text)
			len += t.length();
		return len;
	}

	public String getDate() {
		return date;
	}

	public void setDate(String date) {
		this.date = date;
	}

	public int getWordCount() {
		return wordCount;
	}

	public void setWordCount(int wordCount) {
		this.wordCount = wordCount;
	}

	public long getSize() {
		return size;
	}

	public void setSize(long size) {
		this.size = size;
	}
	
	
	
}
