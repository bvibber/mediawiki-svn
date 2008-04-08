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
	protected ArrayList<Integer> splitPoints = new ArrayList<Integer>();
	protected String suffix = null;
	protected boolean extendable = true;	
	
	protected String originalText = null;
	/** if this snippet goes to the end of sentence */
	protected boolean showsEnd = false;
	/** if all of sentence text is showed */
	protected boolean showsAll = false;
	
	public Snippet(){
	}
	
	public Snippet(String text){
		this.text = text;
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
	
	public int length(){
		return text.length();
	}
	
	public void addSplitPoint(int index){
		splitPoints.add(index);
	}
	
	public String toString(){
		return getFormatted();
	}
	
	/** If consequtive words are being highlighted, merge ranges */
	public void simplifyRanges(){
		Range last = null;
		ArrayList<Range> simplified = new ArrayList<Range>();
		for(Range r : highlighted){
			if(last != null && last.end >= r.start)
				last.end = r.end;
			else{
				simplified.add(r);
				last = r;
			}
		}
		highlighted = simplified;
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
	
	/** Get ranges serialized like: 1,3,22,25] */
	public String getRangesSerialized(){
		StringBuilder sb = new StringBuilder();
		boolean first = true;
		for(Range r : highlighted){
			if(!first)
				sb.append(",");
			sb.append(r.start);
			sb.append(",");
			sb.append(r.end);
			first = false;
		}
		return sb.toString();
	}
	/** Get the points where the continuity of the snippet breaks */
	public String getSplitPointsSerialized(){
		StringBuilder sb = new StringBuilder();
		boolean first = true;
		for(Integer i : splitPoints){
			if(!first)
				sb.append(",");
			sb.append(i);
			first=false;
		}
		return sb.toString();
	}
	
	/** Get string representation of suffix, "" for null */
	public String getSuffixSerialized(){
		if(suffix == null)
			return "";
		else 
			return suffix; 
	}
	
	public void setHighlighted(ArrayList<Range> highlighted) {
		this.highlighted = highlighted;
	}
	public String getOriginalText() {
		return originalText.trim();
	}
	public void setOriginalText(String originalText) {
		this.originalText = originalText;
	}

	public String getSuffix() {
		return suffix;
	}

	public void setSuffix(String suffix) {
		this.suffix = suffix;
	}

	public boolean isExtendable() {
		return extendable;
	}

	public void setExtendable(boolean extendable) {
		this.extendable = extendable;
	}

	public boolean isShowsEnd() {
		return showsEnd;
	}

	public void setShowsEnd(boolean showsEnd) {
		this.showsEnd = showsEnd;
	}

	public boolean isShowsAll() {
		return showsAll;
	}

	public void setShowsAll(boolean showsAll) {
		this.showsAll = showsAll;
	}
	
	
	
	
	
}
