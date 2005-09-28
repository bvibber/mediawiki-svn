/*
 * MediaWiki import/export processing tools
 * Copyright 2005 by Brion Vibber
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
 * $Id$
 */

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
