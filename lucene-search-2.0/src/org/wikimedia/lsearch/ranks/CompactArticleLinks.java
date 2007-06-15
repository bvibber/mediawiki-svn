package org.wikimedia.lsearch.ranks;

import java.io.UnsupportedEncodingException;
import java.util.ArrayList;


/**
 * Page links object that has been optimized to use for low
 * memory consumption. String is being stored in a utf-8
 * encoded byte[] array, and the same object is to be used
 * as a key and value in a hashmap. 
 * 
 * Two objects equals iff they have the same string (other fields
 * are ignored in equals())
 * 
 * @author rainman
 *
 */
public class CompactArticleLinks{
	/** format: <ns>:<title> */
	protected byte[] str;
	public int links;	
	protected int hash = 0;
	/** if this page is a redirect */
	public CompactArticleLinks redirectsTo;
	/** list of pages that redirect here */
	public ArrayList<CompactArticleLinks> redirected;
	
	public CompactArticleLinks(String s){
		try {
			str = s.getBytes("utf-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		}
	}
	
	public CompactArticleLinks(String s, int count){
		this(s);
		this.links = count;
	}		
	
	@Override
	public String toString() {
		try {
			return new String(str,0,str.length,"utf-8");
		} catch (UnsupportedEncodingException e) {
			return "";
		}
	}
		
	public String getKey(){
		try {
			return new String(str,0,str.length,"utf-8");
		} catch (UnsupportedEncodingException e) {
			return "";
		}
	}
	
	public void addRedirect(CompactArticleLinks from){
		if(redirected == null)
			redirected = new ArrayList<CompactArticleLinks>();
		redirected.add(from);
	}
	@Override
	public int hashCode() {
		int h = hash;
		if(h == 0){
			int off = 0;
			
			for (int i = 0; i < str.length; i++) {
				h = 31*h + str[off++];
			}
			hash = h;
		}
		
		return h;			
	}

	@Override
	public boolean equals(Object obj) {			
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final CompactArticleLinks other = (CompactArticleLinks) obj;
		if(other.str.length != str.length)
			return false;
		for(int i=0;i<str.length;i++)
			if(str[i] != other.str[i])
				return false;
		return true;
	}
	
	
}