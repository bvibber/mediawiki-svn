package org.wikimedia.lsearch.highlight;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Set;

import org.wikimedia.lsearch.analyzers.Alttitles;
import org.wikimedia.lsearch.analyzers.ExtToken;

/**
 * Snippet of highlighted text.
 * 
 * @author rainman
 *
 */
public class Snippet implements Serializable {
	public static class Range implements Serializable {
		public int start;
		public int end;
		
		public Range(int start, int end){
			this.start = start; 
			this.end = end;
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + end;
			result = PRIME * result + start;
			return result;
		}

		@Override
		public boolean equals(Object obj) {
			if (this == obj)
				return true;
			if (obj == null)
				return false;
			if (getClass() != obj.getClass())
				return false;
			final Range other = (Range) obj;
			if (end != other.end)
				return false;
			if (start != other.start)
				return false;
			return true;
		}
		
		
	}
	protected String text = null;
	protected ArrayList<Range> highlighted = new ArrayList<Range>();
	
	protected String originalText = null;

	public Snippet(){	
	}
	
	public void addRange(Range r){
		if(highlighted.size() != 0 && r.equals(highlighted.get(highlighted.size()-1))){
			return; // don't allow duplicates!
		}
		highlighted.add(r);
	}

	public ArrayList<Range> getHighlighted() {
		return highlighted;
	}

	public String getText() {
		return text;
	}
	
	public void setText(String text){
		this.text = text;
	}
	
	public String toString(){
		return getFormatted();
	}
	
	/** Get default formatting with <b> and </b> tags */
	public String getFormatted(){
		return getFormatted("<b>","</b>");
	}
	
	/** Get formating with custom html begin and end tags */
	public String getFormatted(String beginTag, String endTag){	
		StringBuilder sb = new StringBuilder();
		int last = 0;
		for(Range r : highlighted){
			sb.append(text.substring(last,r.start));
			sb.append(beginTag);
			sb.append(text.substring(r.start,r.end));
			sb.append(endTag);
			last = r.end;
		}
		if(last != text.length())
			sb.append(text.substring(last));
		return sb.toString();
	}
	
	public void setHighlighted(ArrayList<Range> highlighted) {
		this.highlighted = highlighted;
	}
	public String getOriginalText() {
		return originalText;
	}
	public void setOriginalText(String originalText) {
		this.originalText = originalText;
	}
	
	
	
}
