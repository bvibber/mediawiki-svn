package org.wikimedia.lsearch.suggest.api;

import java.util.BitSet;
import java.util.HashMap;
import java.util.Set;
import java.util.Map.Entry;

import org.wikimedia.lsearch.search.NamespaceFilter;

/** Mapping from namespaces to frequencies */
public class NamespaceFreq {
	class IntWrap{
		int val = 0;
		IntWrap() {}
		IntWrap(int value){ val = value; }
		IntWrap(String value){ val = Integer.parseInt(value); }
		public String toString(){ return ""+val; }
	}
	/** namespace -> frequency */
	protected HashMap<Integer,IntWrap> nsmap = new HashMap<Integer,IntWrap>();
	
	public NamespaceFreq(String field){
		String[] pairs = field.split(" ");
		for(String pair : pairs){
			if(pair.length() == 0)
				continue;
			String[] nsf = pair.split(":");			
			if(nsf.length == 2)
				nsmap.put(Integer.parseInt(nsf[0]),new IntWrap(nsf[1]));
			else {
				throw new RuntimeException("Bad syntax for namespace-frequency pairs : "+field);
			}
		}
	}
	
	public NamespaceFreq() {		
	}
	
	public int getFrequency(int namespace){
		if(nsmap.containsKey(namespace))
			return nsmap.get(namespace).val;
		else 
			return 0;
	}
	
	public int getFrequency(NamespaceFilter nsf){
		int sum = 0;
		BitSet ns = nsf.getIncluded();
		for(int i=ns.nextSetBit(0); i>=0; i=ns.nextSetBit(i+1)){
			sum += getFrequency(i);
		}
		return sum;
	}
	
	public String serialize(int minFreq){
		StringBuilder sb = new StringBuilder();
		int sum = 0;
		for(Entry<Integer,IntWrap> e : nsmap.entrySet()){
			sum += e.getValue().val;
			sb.append(e.getKey());
			sb.append(":");
			sb.append(e.getValue());
			sb.append(" ");
		}
		if(sum < minFreq)
			return "";
		return sb.toString();
	}
	
	public String serialize(){
		return serialize(0);
	}
	
	public void setFrequency(int namespace, int frequency){
		nsmap.put(namespace,new IntWrap(frequency));
	}
	
	public void incFrequency(int namespace){
		if(nsmap.containsKey(namespace)){
			nsmap.get(namespace).val++;
		} else
			nsmap.put(namespace,new IntWrap(1));
	}
	
	public Set<Integer> getNamespaces(){
		return nsmap.keySet();
	}
}
