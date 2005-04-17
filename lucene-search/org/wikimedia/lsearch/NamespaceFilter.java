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
 * $Id$
 */

package org.wikimedia.lsearch;

public class NamespaceFilter {
	private boolean[] included;
	private boolean empty;
	
	public NamespaceFilter() {
		empty = true;
		included = new boolean[256];
	}
	
	public NamespaceFilter(String namespaces) {
		empty = true;
		included = new boolean[256];
		
		if (namespaces != null) {
			String[] bits = namespaces.split(",");
			for (int i = 0; i < bits.length; i++) {
				try {
					empty = false;
					included[Integer.parseInt(bits[i])] = true;
				} catch (ArrayIndexOutOfBoundsException e) {
					// owie
				}
			}
		}
	}
	
	public boolean filter(String namespace) {
		return filter(Integer.parseInt(namespace));
	}
	
	public boolean filter(int namespace) {
		if (empty)
			return true;
		try {
			return included[namespace];
		} catch (ArrayIndexOutOfBoundsException e) {
			return false;
		}
	}

}
