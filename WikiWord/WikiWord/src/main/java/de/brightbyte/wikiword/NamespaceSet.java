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
 * $Id: NamespaceSet.java 11268 2005-10-10 06:57:30Z vibber $
 */

package de.brightbyte.wikiword;

import java.util.List;
import java.util.Map;
import java.util.HashMap;
import java.util.Iterator;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class NamespaceSet implements Iterable<Namespace> {
	protected Map<Integer, Namespace> byCode = new HashMap<Integer, Namespace>();
	protected Map<String, Namespace> byName = new HashMap<String, Namespace>();
	
	protected NamespaceSet() {
		//noop
	}
	
	protected String normalizeName(String name, boolean lower) {
		name = name.trim().replace(' ', '_');
		if (lower) name = name.toLowerCase();
		//XXX: else ucfirst?!
		return name;
	}
	
	public void addNamespace(int code, String name) {
		Namespace ns = byCode.get(code);
		if (ns==null) {
			addNamespace( new Namespace(code, normalizeName(name, false)) );
		}
		else {
			ns.addName(normalizeName(name, false));
			byName.put(normalizeName(name, true), ns);
		}
	}
	
	public void addNamespace(int code, List<String> names) {
		for (String name: names) {
			addNamespace(code, name);
		}
	}
	
	public void addNamespace(Namespace ns) {
		byCode.put(ns.getNumber(), ns);
		
		for (String name: ns.getNames()) {
			byName.put(normalizeName(name, true), ns);
		}
	}

	public Namespace getNamespace(int number) {
		return byCode.get(number);
	}

	private static final Pattern numericNamespacePattern = Pattern.compile("^(ns:)?(\\d+)$", Pattern.CASE_INSENSITIVE);
	
	public Namespace getNamespace(String name) {
		if (name.equals("") || name.equals("*")) return getNamespace(Namespace.MAIN);
		
		Matcher m = numericNamespacePattern.matcher(name);
		if (m.matches()) {
			int n = Integer.parseInt(m.group(2));
			return getNamespace(n);
		} else {
			return byName.get(normalizeName(name, true));
		}
	}
	
	public String getCanonicalName(int number) {
		Namespace n = getNamespace(number);
		return n == null ? null : n.getCanonicalName();
	}

	public String getLocalName(int number) {
		Namespace n = getNamespace(number);
		return n == null ? null : n.getLocalName();
	}
	
	public int getNumber(String name) {
		Namespace n = getNamespace(name);
		return n == null ? Namespace.NONE : n.getNumber();
	}
	
	public Iterator<Namespace> iterator() {
		return byCode.values().iterator();
	}
	
	public void addAll(NamespaceSet ct) {
		for(Namespace ns: ct) {
			addNamespace(ns.getNumber(), ns.getNames());
		}
	}
}
