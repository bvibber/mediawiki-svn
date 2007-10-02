package org.wikimedia.lsearch.ranks;

import java.util.HashMap;
import java.util.LinkedList;
import java.util.WeakHashMap;

/**
 * Maintain a cache of objects. Cache is a simple FIFO cache of
 * constant size. Oldest entries get replaced by newer ones.
 * 
 * @author rainman
 *
 */
public class ObjectCache {
	/** used to maintain FIFO cache of valid keys */
	protected String[] fifo;
	/** storage of objects */
	protected HashMap<String,Object> objs = new HashMap<String,Object>();
	protected int size, inx;
	
	protected long hits = 0;
	protected long miss = 0;
	
	protected int report = 0;
	
	public ObjectCache(int size){
		this.size = size;
		this.fifo = new String[size];
		this.inx = 0;
	}
	
	public void put(String key, Object obj){
		// add to FIFO queue only if not already in it
		if(!objs.containsKey(key)){			
			if(inx >= size)
				inx = 0;
			String del = fifo[inx];
			if(del != null){
				//remove oldest from cache
				objs.remove(del);
			}
			fifo[inx] = key; // latest cached key
			inx++;
		}
		objs.put(key,obj);		
	}
	
	public Object get(String key){
		if(++report >= 5000){
			report = 0;
			System.out.println(getStats());
		}
		Object obj = objs.get(key);
		if(obj !=null )
			hits++;
		else
			miss++;
		return obj;
	}
	
	public String getStats(){
		long total = hits+miss;
		return "HITS: "+hits+" ("+((float)hits*100/total)+"%), MISS: "+miss+" ("+((float)miss*100/total)+"%)";
	}
	
	
}
