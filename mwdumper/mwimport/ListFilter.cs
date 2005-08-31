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
	using System.IO;

	public class ListFilter : PageFilter {
		IDictionary _list;
		
		public ListFilter(IDumpWriter sink, string sourceFileName) : base(sink) {
			_list = new Hashtable();
			using (TextReader input = File.OpenText(sourceFileName)) {
				string line = input.ReadLine();
				while (line != null) {
					if (!line.StartsWith("#")) {
						string cleaned = line.TrimStart(' ', '\n', '\r', '\t', ':');
						string title = cleaned.TrimEnd(' ', '\n', '\r', '\t');
						
						if (title.Length > 0)
							_list[title] = title;
					}
					line = input.ReadLine();
				}
			}
		}
		
		protected override bool Pass(Page page) {
			return _list.Contains(page.Title.ToString());
		}
	}
}
