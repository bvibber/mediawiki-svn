package org.wikimedia.lsearch.ranks;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;

/**
 * Maintain a list of string, with emphasis on 
 * efficient serialization and deserialization.
 * 
 * Note: string length is limited to BUFFER_SIZE chars 
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



	class StringListIterator implements Iterator<String> {
		char[] buffer = new char[BUFFER_SIZE];
		int length = 0;
		int pos = 0; // position in serialized[]		
		
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
			return new String(buffer,0,length);
		}

		public void remove() {
			throw new UnsupportedOperationException();
		}
		
	}
	
	
	
}
