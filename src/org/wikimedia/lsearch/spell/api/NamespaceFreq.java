package org.wikimedia.lsearch.spell.api;

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
	
	/** Construct from serialized field value */
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
	
	/** Get frequency of term for one namespace */
	public int getFrequency(int namespace){
		if(nsmap.containsKey(-10))
			return nsmap.get(-10).val;
		else if(nsmap.containsKey(namespace))
			return nsmap.get(namespace).val;
		else 
			return 0;
	}
	
	/** Get frequency of term over some set of namespaces */
	public int getFrequency(NamespaceFilter nsf){
		if(nsmap.containsKey(-10))
			return nsmap.get(-10).val;
		int sum = 0;
		BitSet ns = nsf.getIncluded();
		for(int i=ns.nextSetBit(0); i>=0; i=ns.nextSetBit(i+1)){
			sum += getFrequency(i);
		}
		return sum;
	}
	
	/** Get total frequency of term over all namespaces */
	public int getFrequency(){
		if(nsmap.containsKey(-10))
			return nsmap.get(-10).val;
		int sum = 0;
		for(IntWrap i : nsmap.values()){
			sum += i.val;
		}
		return sum;
	}
	
	/** Serialize only if total frequency is at least minFreq */
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
	
	/** Serialize into a field format: ns:freq ns2:freq2 ... */
	public String serialize(){
		return serialize(0);
	}
	
	/** Modify frequency value for some namespace */
	public void setFrequency(int namespace, int frequency){
		nsmap.put(namespace,new IntWrap(frequency));
	}
	
	/** Incremental term frequency in namespace */
	public void incFrequency(int namespace){
		incFrequency(namespace,1);
	}
	
	/** Incremental term frequency in namespace */
	public void incFrequency(int namespace, int inc){
		if(nsmap.containsKey(namespace)){
			nsmap.get(namespace).val+=inc;
		} else
			nsmap.put(namespace,new IntWrap(inc));
	}
	
	/** Get all namespaces where term has nonzero frequency */
	public Set<Integer> getNamespaces(){
		return nsmap.keySet();
	}

}
