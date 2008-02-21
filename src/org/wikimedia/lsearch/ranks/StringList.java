package org.wikimedia.lsearch.ranks;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashSet;
import java.util.Iterator;

/**
 * Maintain a list of string, with emphasis on 
 * efficient serialization and deserialization.
 * 
 * @author rainman
 *
 */
public class StringList {
	public static final int BUFFER_SIZE = 300;
	/** delimiter used during serialization/deserialization */
	public static final char DELIMITER = '\0';
		
	protected char[] serialized = null;
	protected Collection<String> collection = null;
	protected String serializedStr = null;
	
	
	/** Build a list form serialized input string */
	public StringList(String serialized){
		if(serialized == null)
			this.serialized = new char[0];
		else
			this.serialized = serialized.toCharArray();
	}
	
	/** Build from a collection of string */
	public StringList(Collection<String> inputCollection){
		this.collection = inputCollection;
	}
	
	public Iterator<String> iterator(){
		if(collection != null)
			return collection.iterator();
		else if(serialized != null)
			return new StringListIterator();
		else
			return null;		
	}
	
	public Collection<String> toCollection(){
		if(collection != null)
			return collection;
		Iterator<String> it = iterator();
		collection = new ArrayList<String>();		
		if(it != null){
			while(it.hasNext())
				collection.add(it.next());
		}
		return collection;
		
	}
	
	/** To collection, but deserialize only certain terms */
	public Collection<String> toCollection(LookupSet ls){
		Iterator<String> it = new StringListIterator(ls);
		ArrayList<String> list = new ArrayList<String>();		
		if(it != null){
			while(it.hasNext()){
				String s = it.next();
				if(s != null)
					list.add(s);
			}
		}
		return list;
		
	}
	
	public HashSet<String> toHashSet(){
		HashSet<String> set = new HashSet<String>();
		Iterator<String> it = iterator();
		if(it != null){
			while(it.hasNext())
				set.add(it.next());
		}
		return set;		
	}
	
	public HashSet<String> toHashSet(LookupSet ls){
		HashSet<String> set = new HashSet<String>();
		Iterator<String> it = new StringListIterator(ls);
		if(it != null){
			while(it.hasNext())
				set.add(it.next());
		}
		return set;
		
	}

	
	public String serialize(){
		if(serialized != null)
			return new String(serialized);
		else if(serializedStr != null)
			return serializedStr;
		else if(collection == null)
			throw new RuntimeException("String list to be serialized is null");
		StringBuilder sb = new StringBuilder();
		boolean first = true;
		for(String s : collection){
			if(!first)
				sb.append(DELIMITER);
			sb.append(s);
			first = false;
		}
		serializedStr = sb.toString();
		return serializedStr;
	}
	
	
	
	@Override
	public String toString() {
		return serialize();
	}

	public static class LookupSet {
		public HashSet<String> allow = null; 
		public static final int MASK = 2047;
		protected boolean[] lookup = null;
		
		public LookupSet(HashSet<String> allow){
			this.allow = allow;
			this.lookup = new boolean[MASK+1];
			for(String w : allow){
				lookup[stringHash(w)&MASK] = true;
			}
		}
				
		public final int bufferHash(StringListIterator it){
			if(it.length > 1)
				return it.buffer[0]*31 + it.buffer[1];
			else if(it.length == 1)
				return it.buffer[0];
			return 0;
			
		}
		
		public final int stringHash(String w){
			if(w.length() == 0)
				return 0;
			if(w.length() == 1)
				return w.charAt(0);
			
			return w.charAt(0)*31+w.charAt(1);			
		}
		
		/** If string in the buffer in contained in the set, return it */
		public final String makeContained(StringListIterator it){
			if(it.length == 0)
				return "";
			if(!lookup[bufferHash(it)&MASK])
				return null;
			
			String s = new String(it.buffer,0,it.length);
			if(allow.contains(s))
				return s;
			
			return null;
		}
		
	}

	class StringListIterator implements Iterator<String> {
		char[] buffer = new char[BUFFER_SIZE];
		int length = 0;
		int pos = 0; // position in serialized[]
		LookupSet ls = null;
		
		public StringListIterator() {}
		public StringListIterator(LookupSet ls) { this.ls = ls; }

		
		public boolean hasNext() {
			if(pos < serialized.length)
				return true;
			else
				return false;
		}

		public String next() {
			if(!hasNext())
				return null;
			length = 0;
			for(;pos<serialized.length;pos++){
				if(serialized[pos] == DELIMITER){
					pos++; // position on first char of next string
					break;
				} 
				if(length >= buffer.length){ // should never happen with wiki-titles 
					char[] newbuf = new char[buffer.length*2];
					System.arraycopy(buffer,0,newbuf,0,buffer.length);
					buffer = newbuf;
				}
				buffer[length++] = serialized[pos]; 
			}
			if(ls == null)
				return new String(buffer,0,length);
			else{
				// if there is an restricted set, don't waste time making new strings
				return ls.makeContained(this);
			}
		}

		public void remove() {
			throw new UnsupportedOperationException();
		}
		
	}
	
	
	
}
