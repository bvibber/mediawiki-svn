package org.wikimedia.lsearch.util;

import java.util.HashMap;
import java.util.Set;
import java.util.Map.Entry;

/** Count occurances of strings */
public class StringCounter {
	static public class Count {
		public int num=1;
	}
	HashMap<String,Count> strings = new HashMap<String,Count>();
	
	/** Increment counter for string s */
	public void count(String s){
		Count c = strings.get(s);
		if(c == null)
			strings.put(s,new Count());
		else
			c.num++;
	}
	
	/** Get count for string */
	public int get(String s){
		Count c = strings.get(s);
		if(c == null)
			return 0;
		else
			return c.num;
	}
	
	/** Return all pairs of String,Count as set */
	public Set<Entry<String,Count>> getSet(){
		return strings.entrySet();
	}
}
