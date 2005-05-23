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

namespace MediaWiki.Search.Daemon {

	using System;
	using System.Collections;
	using System.Text;
	using System.Web;

	public class QueryStringMap : Hashtable {

		public QueryStringMap(Uri uri) : base() {
			GrabQueryItems(uri.Query);
		}

		private void GrabQueryItems(string query) {
			if (query == null)
				return;
			if (query == "")
				return;
			foreach (string token in query.Substring(1, query.Length - 1).Split('&')) {
				SlurpItem(token);
			}
		}

		private void SlurpItem(string token) {
			string[] pair = token.Split(new char[] { '=' }, 2);
			String key = pair[0];
			if (key.Length > 0) {
				if (pair.Length == 1) {
					this[pair[0]] = "";
				} else {
					this[pair[0]] = HttpUtility.UrlDecode(pair[1], Encoding.UTF8);
				}
			}
		}

	}
}
