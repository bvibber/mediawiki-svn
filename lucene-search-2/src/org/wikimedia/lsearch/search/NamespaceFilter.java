/*
 * Copyright 2005 Brion Vibber
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * $Id: NamespaceFilter.java 8398 2005-04-17 06:21:19Z vibber $
 */

package org.wikimedia.lsearch.search;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.BitSet;
import java.util.Collection;
import java.util.HashSet;

/** A bean-like class that contains information about what namespaces
 *  to filter  */
public class NamespaceFilter implements Serializable {
	private BitSet included;
	
	protected void init(){
		included = new BitSet(64);
	}
	
	/** "all" filter */
	public NamespaceFilter() {
		init();
	}
	
	/** filter namespaces */
	public NamespaceFilter(Collection<Integer> namespaces){
		init();
		for(Integer namespace : namespaces){
			included.set(namespace.intValue());
		}
	}
	/** filter on one namespace */
	public NamespaceFilter(int namespace){
		init();
		included.set(namespace);
	}
	/** filter number of namespaces separated by comma, e.g. 0,2,10 */
	public NamespaceFilter(String namespaces) {
		init();
		if (namespaces != null && !namespaces.equals("")) {
			String[] bits = namespaces.split(",");
			for (int i = 0; i < bits.length; i++) {
				included.set(Integer.parseInt(bits[i].trim()));
			}
		}
	}
	
	/** Decompose this filter into an array of single-namespace filters, do OR to construct */
	public ArrayList<NamespaceFilter> decompose(){
		ArrayList<NamespaceFilter> dec = new ArrayList<NamespaceFilter>();
		for(int i = included.nextSetBit(0);i>=0;i=included.nextSetBit(i+1)){
			dec.add(new NamespaceFilter(i));
		}
		return dec;
	}
	
	public HashSet<Integer> getNamespaces(){
		HashSet<Integer> ret = new HashSet<Integer>();
		if(included.cardinality() == 0)
			return ret;
		for(int i = included.nextSetBit(0);i>=0;i=included.nextSetBit(i+1)){
			ret.add(i);
		}
		return ret;
	}
	
	public ArrayList<Integer> getNamespacesOrdered(){
		ArrayList<Integer> ret = new ArrayList<Integer>();
		if(included.cardinality() == 0)
			return ret;
		for(int i = included.nextSetBit(0);i>=0;i=included.nextSetBit(i+1)){
			ret.add(i);
		}
		return ret;
	}
	
	public boolean filter(String namespace) {
		return filter(Integer.parseInt(namespace));
	}
	
	public boolean filter(int namespace) {
		return included.get(namespace);
	}
	
	/** Set bit for namespace to true */
	public void set(int namespace){
		included.set(namespace);
	}
	
	/** Set bit for namespace to false */
	public void unset(int namespace){
		included.set(namespace,false);
	}
	
	public boolean contains(int namespace){
		if(namespace < 0)
			return false;
		else
			return included.get(namespace);
	}
	
	public boolean contains(String namespace){
		return contains(Integer.parseInt(namespace));
	}
	
	public BitSet getIncluded() {
		return included;
	}

	public int cardinality(){
		return included.cardinality();
	}
	
	public int getNamespace(){
		return included.nextSetBit(0);		
	}
	
	/** if empty filter ("all" keyword") */
	public boolean isAll(){
		return cardinality() == 0; 
	}
	
	@Override
	public String toString() {
		return included.toString();
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((included == null) ? 0 : included.hashCode());
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
		final NamespaceFilter other = (NamespaceFilter) obj;
		if (included == null) {
			if (other.included != null)
				return false;
		} else if (!included.equals(other.included))
			return false;
		return true;
	}


	
}
