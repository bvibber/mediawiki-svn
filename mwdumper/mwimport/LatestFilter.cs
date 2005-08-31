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
	public class LatestFilter : IDumpWriter {
		IDumpWriter _sink;
		Revision _lastRevision;
		
		public LatestFilter(IDumpWriter sink) {
			_sink = sink;
		}
		
		public void Close() {
			_sink.Close();
		}
		
		public void WriteStartWiki() {
			_sink.WriteStartWiki();
		}
		
		public void WriteEndWiki() {
			_sink.WriteEndWiki();
		}
		
		public void WriteSiteinfo(Siteinfo info) {
			_sink.WriteSiteinfo(info);
		}
		
		public void WriteStartPage(Page page) {
			_sink.WriteStartPage(page);
		}
		
		public void WriteEndPage() {
			if (_lastRevision != null) {
				_sink.WriteRevision(_lastRevision);
				_lastRevision = null;
			}
			_sink.WriteEndPage();
		}
		
		public void WriteRevision(Revision revision) {
			_lastRevision = revision;
		}
	}
}
