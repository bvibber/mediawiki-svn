package org.wikimedia.lsearch.util;

import java.io.UnsupportedEncodingException;
import java.util.Arrays;
import java.util.HashSet;
import java.util.Set;

/** 
 * A set of utf-8 byte strings, useful for lazy
 * initialization of ExtToken strings 
 * 
 * @author rainman
 *
 */
public class Utf8Set {
	/** Utf8 string, a piece of byte array */ 
	protected static class Utf8String {
		byte[] data;
		int start, end;
		
		Utf8String(){}
		Utf8String(String w){
			try {
				data = w.getBytes("utf-8");
				start = 0;
				end = data.length;
			} catch (UnsupportedEncodingException e) {
				e.printStackTrace();
			}
		}
		
		@Override
		public int hashCode() {
			int result = 1;
			for(int i=start;i<end;i++)
				result = 31 * result + data[i];

			return result;
		}
		
		@Override
		public boolean equals(Object obj) {
			if (getClass() != obj.getClass())
				return false;
			final Utf8String other = (Utf8String) obj;
			int len = end-start;
			int olen = other.end-other.start;
			if(len != olen)
				return false;
			for(int i=start,j=other.start;i<end;i++,j++){
				if(data[i]!=other.data[j])
					return false;
			}
			return true;
		}		
	}
	
	public static final int MASK = 0xff;
	protected boolean[] lookup = new boolean[MASK+1];
	protected HashSet<Utf8String> set = new HashSet<Utf8String>();
	
	protected byte[] data;
	protected Utf8String str = new Utf8String();
	
	public Utf8Set(Set<String> words){		
		for(String w : words){
			lookup[w.charAt(0)&MASK] = true;
			set.add(new Utf8String(w));
		}
	}
	
	public void setData(byte[] data){
		this.data = data;
		str.data = data;
	}

	public boolean contains(int start, int end){
		str.start = start;
		str.end = end;
		if(!lookup[data[start]&0xff]) // quick lookup of first letter
			return false;
		return set.contains(str);
	}
	
}
