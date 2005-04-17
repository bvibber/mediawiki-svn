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

import java.io.UnsupportedEncodingException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URLDecoder;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.Set;
import java.util.StringTokenizer;

public class QueryStringMap extends HashMap implements Map {

	public QueryStringMap(URI uri) {
		super();
		grabQueryItems(uri.getRawQuery());
	}

	private void grabQueryItems(String query) {
		if (query == null)
			return;
		for (StringTokenizer tokenizer = new StringTokenizer(query, "&"); tokenizer.hasMoreElements();) {
			String token = tokenizer.nextToken();
			slurpItem(token);
		}
	}

	private void slurpItem(String token) {
		String[] pair = token.split("=", 2);
		String key = pair[0];
		if (key.length() > 0) {
			if (pair.length == 1) {
				put(pair[0], "");
			} else {
				try {
					put(pair[0], URLDecoder.decode(pair[1], "UTF-8"));
				} catch (UnsupportedEncodingException e) {
					// This should never happen.
					e.printStackTrace();
				}
			}
		}
	}

	public static void main(String[] args) {
		try {
			testURI("/x");
			testURI("/x?foo=bar");
			testURI("/x?foo=bar&biz=bax");
			
			// The %26 should _not_ split 'foo' from 'bogo'
			testURI("/x?foo=bar+%26bogo&next=extreme");
			
			// UTF-8 good encoding
			testURI("/x?serveuse=%c3%a9nid");
			
			// bad encoding; you'll see replacement char
			testURI("/x?serveuse=%e9nid");
			
			// corner cases; missing params
			testURI("/x?foo");
			testURI("/x?foo&bar=baz");
			testURI("/x?foo&&bar");
			testURI("/x?&");
			testURI("/x?=");
			testURI("/x?==&");
		} catch (URISyntaxException e) {
			e.printStackTrace();
		}
	}
	
	private static void testURI(String uri) throws URISyntaxException {
		QueryStringMap map = new QueryStringMap(new URI(uri));
		System.out.println(uri);
		Set keys = map.keySet();
		for (Iterator i = keys.iterator(); i.hasNext();) {
			 String key = (String)i.next();
			 System.out.println("  \"" + key + "\" => \"" + map.get(key) + "\"");
		}
	}
}
