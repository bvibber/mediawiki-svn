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

namespace MediaWiki.Import {
	using System;
	using System.Collections;

	public struct Title {
		public int Namespace;
		public string Text;
		
		private string _prefix;
		
		public Title(string prefixedTitle, IDictionary namespaces) {
			foreach (int key in namespaces.Keys) {
				string prefix = (string)namespaces[key];
				int len = prefix.Length;
				if (len > 0
					&& (prefixedTitle.Length - len) > 1
					&& prefixedTitle.StartsWith(prefix)
					&& prefixedTitle[len] == ':') {
					Namespace = key;
					_prefix = prefix + ":";
					Text = Title.ValidateTitleChars(prefixedTitle.Substring(len + 1));
					return;
				}
			}
			Namespace = 0;
			_prefix = "";
			Text = prefixedTitle;
		}
		
		public static string ValidateTitleChars(string text) {
			// FIXME
			return text;
		}
		
		public override string ToString() {
			return _prefix + Text;
		}
		
		public bool IsTalk {
			get {
				return (Namespace & 0x0001) == 1;
			}
		}
	}
}
