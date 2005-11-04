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

namespace MediaWiki.Search.SearchTool {
	using System;
	using System.Collections;
	
	using MediaWiki.Search;
	
	using org.mediawiki.importer;
	
	class SearchImporter : DumpWriter {
		SearchWriter _writer;
		int _pages = 0;
		Page _page = null;
		Revision _revision = null;
		const int _interval = 100;
		
		public SearchImporter(SearchWriter writer) {
			_writer = writer;
		}
		
		public void close() {
			// nop
		}
		
		public void writeStartWiki() {
			// nop
		}
		
		public void writeEndWiki() {
			// nop
		}
		
		public void writeSiteinfo(Siteinfo info) {
			// nop
		}
		
		public void writeStartPage(Page page) {
			_page = page;
			_pages++;
			if (_pages % _interval == 0) {
				Console.Error.WriteLine("{0}: {1} pages... [[{2}]]", _writer.DatabaseName, _pages, page.Title);
			}
		}
		
		public void writeEndPage() {
			if (_revision != null) {
				// Warning! Beware that toString() and ToString() behave
				// differently on Java/IKVM objects.
				Article article = new Article(_writer.DatabaseName,
					_page.Title.Namespace.toString(),
					_page.Title.Text,
					_revision.Text,
					_revision.Timestamp.toString());
				
				// Note this is reliant on the index being clean before
				// the import starts; if not you might get duplicate entries.
				_writer.AddArticle(article);
			}
			_revision = null;
			_page = null;
		}
		
		public void writeRevision(Revision revision) {
			// We'll only use one revision per page if someone
			// sends a full dump at us.
			_revision = revision;
		}
	}

}
