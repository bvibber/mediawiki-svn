package org.mediawiki.importer;

import java.util.ArrayList;
import java.util.Hashtable;
import java.util.Iterator;

public class NamespaceSet {
	ArrayList order;
	Hashtable byname;
	Hashtable bynumber;
	
	public NamespaceSet() {
		order = new ArrayList();
		byname = new Hashtable();
		bynumber = new Hashtable();
	}
	
	public void add(int index, String prefix) {
		order.add(new Integer(index));
		byname.put(prefix, new Integer(index));
		bynumber.put(new Integer(index), prefix);
	}
	
	public boolean hasPrefix(String prefix) {
		return byname.containsKey(prefix);
	}
	
	public boolean hasIndex(int index) {
		return bynumber.containsKey(new Integer(index));
	}
	
	public String getPrefix(int index) {
		return (String)bynumber.get(new Integer(index));
	}
	
	public int getIndex(String prefix) {
		return ((Integer)byname.get(prefix)).intValue();
	}
	
	public String getColonPrefix(int index) {
		String prefix = getPrefix(index);
		if (index != 0)
			return prefix + ":";
		else
			return prefix;
	}
	
	public Iterator keys() {
		return order.listIterator();
	}
}
