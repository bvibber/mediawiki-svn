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

	public class NamespaceFilter : PageFilter {
		bool _invert;
		IDictionary _matches;
		
		public NamespaceFilter(IDumpWriter sink, string configString) : base(sink) {
			_invert = configString.StartsWith("!");
			_matches = new Hashtable();
			
			string[] namespaceKeys = {
				"NS_MAIN",
				"NS_TALK",
				"NS_USER",
				"NS_USER_TALK",
				"NS_PROJECT",
				"NS_PROJECT_TALK",
				"NS_IMAGE",
				"NS_IMAGE_TALK",
				"NS_MEDIAWIKI",
				"NS_MEDIAWIKI_TALK",
				"NS_TEMPLATE",
				"NS_TEMPLATE_TALK",
				"NS_HELP",
				"NS_HELP_TALK",
				"NS_CATEGORY",
				"NS_CATEGORY_TALK" };
			
			string[] itemList = configString.Trim('!', ' ', '\t').Split(',');
			foreach (string keyString in itemList) {
				string trimmed = keyString.Trim();
				try {
					int key = int.Parse(trimmed);
					_matches[key] = key;
				} catch {
					for (int key = 0; key < namespaceKeys.Length; key++) {
						if (trimmed == namespaceKeys[key])
							_matches[key] = key;
					}
				}
			}
		}
		
		protected override bool Pass(Page page) {
			return _invert ^ _matches.Contains(page.Title.Namespace);
		}
	}
}
