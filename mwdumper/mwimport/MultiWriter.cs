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
	
	public class MultiWriter : IDumpWriter {
		IList _sinks;
		
		public MultiWriter() {
			_sinks = new ArrayList();
		}
		
		public void Close() {
			foreach (IDumpWriter sink in _sinks)
				sink.Close();
		}
		
		public void WriteStartWiki() {
			foreach (IDumpWriter sink in _sinks)
				sink.WriteStartWiki();
		}
		
		public void WriteEndWiki() {
			foreach (IDumpWriter sink in _sinks)
				sink.WriteEndWiki();
		}
		
		public void WriteSiteinfo(Siteinfo info) {
			foreach (IDumpWriter sink in _sinks)
				sink.WriteSiteinfo(info);
		}
		
		public void WriteStartPage(Page page) {
			foreach (IDumpWriter sink in _sinks)
				sink.WriteStartPage(page);
		}
		
		public void WriteEndPage() {
			foreach (IDumpWriter sink in _sinks)
				sink.WriteEndPage();
		}
		
		public void WriteRevision(Revision revision) {
			foreach (IDumpWriter sink in _sinks)
				sink.WriteRevision(revision);
		}
		
		public void Add(IDumpWriter sink) {
			_sinks.Add(sink);
		}
	}
}
