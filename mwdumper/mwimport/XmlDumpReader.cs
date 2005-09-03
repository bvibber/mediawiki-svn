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
	using System.Text;
	using System.Xml;

	delegate void ElementHandler();

	public class XmlDumpReader {
		XmlTextReader _reader;
		IDumpWriter _writer;
		
		IList _path;
		IDictionary _handlers;
		
		Siteinfo _siteinfo;
		Page _page;
		bool _pageSent;
		Contributor _contrib;
		Revision _rev;
		
		public XmlDumpReader(TextReader inputStream, IDumpWriter writer) {
			_reader = new XmlTextReader(inputStream);
			_reader.WhitespaceHandling = WhitespaceHandling.Significant;
			_writer = writer;
		}
		
		public void ReadDump() {
			_handlers = new Hashtable();
			_handlers["/mediawiki"] = new ElementHandler(ReadMediaWiki);
			_handlers["/mediawiki/siteinfo"] = new ElementHandler(ReadSiteInfo);
			_handlers["/mediawiki/siteinfo/sitename"] = new ElementHandler(ReadSitename);
			_handlers["/mediawiki/siteinfo/base"] = new ElementHandler(ReadBase);
			_handlers["/mediawiki/siteinfo/generator"] = new ElementHandler(ReadGenerator);
			_handlers["/mediawiki/siteinfo/case"] = new ElementHandler(ReadCase);
			_handlers["/mediawiki/siteinfo/namespaces"] = new ElementHandler(ReadNamespaces);
			_handlers["/mediawiki/siteinfo/namespaces/namespace"] = new ElementHandler(ReadNamespace);
			_handlers["/mediawiki/page"] = new ElementHandler(ReadPage);
			_handlers["/mediawiki/page/title"] = new ElementHandler(ReadTitle);
			_handlers["/mediawiki/page/id"] = new ElementHandler(ReadPageId);
			_handlers["/mediawiki/page/restrictions"] = new ElementHandler(ReadRestrictions);
			_handlers["/mediawiki/page/revision"] = new ElementHandler(ReadRevision);
			_handlers["/mediawiki/page/revision/id"] = new ElementHandler(ReadRevId);
			_handlers["/mediawiki/page/revision/timestamp"] = new ElementHandler(ReadTimestamp);
			_handlers["/mediawiki/page/revision/contributor"] = new ElementHandler(ReadContributor);
			_handlers["/mediawiki/page/revision/contributor/username"] = new ElementHandler(ReadUsername);
			_handlers["/mediawiki/page/revision/contributor/id"] = new ElementHandler(ReadContribId);
			_handlers["/mediawiki/page/revision/contributor/ip"] = new ElementHandler(ReadIp);
			_handlers["/mediawiki/page/revision/comment"] = new ElementHandler(ReadComment);
			_handlers["/mediawiki/page/revision/minor"] = new ElementHandler(ReadMinor);
			_handlers["/mediawiki/page/revision/text"] = new ElementHandler(ReadText);
			
			_path = new ArrayList();
			ReadThrough();
			
			_writer.Close();
			_reader.Close();
		}
		
		private void ReadThrough() {
			string current = _reader.LocalName;
			
			while (_reader.Read()) {
				string name = _reader.LocalName;
				if (_reader.NodeType == XmlNodeType.Element) {
					//Console.WriteLine("<!-- open: " + name + "-->");
					_path.Add(name);
					string pathKey = PathKey();
					//Console.WriteLine("<!-- path: " + pathKey + "-->");
					if (_handlers.Contains(pathKey)) {
						ElementHandler handler = (ElementHandler)_handlers[pathKey];
						handler();
						_path.RemoveAt(_path.Count - 1);
						continue;
					} else {
						throw new ArgumentException("Unexpected element, path " + pathKey);
					}
				} else if (_reader.NodeType == XmlNodeType.EndElement && current == name) {
					return;
				}
			}
		}
		
		private string PathKey() {
			StringBuilder str = new StringBuilder();
			foreach (string node in _path) {
				str.Append('/');
				str.Append(node);
			}
			return str.ToString();
		}
		
		private string ReadElementContent() {
			StringBuilder val = new StringBuilder();
			if (_reader.IsEmptyElement) {
				return "";
			}
			while (_reader.Read()) {
				//Console.WriteLine("XXX: " + reader.NodeType + ", " + reader.LocalName + ", " + reader.Value);
				switch (_reader.NodeType) {
				case XmlNodeType.SignificantWhitespace:
				case XmlNodeType.Text:
					_reader.MoveToContent();
					val.Append(_reader.Value);
					break;
				case XmlNodeType.EndElement:
					return val.ToString();
				default:
					// ignore
					break;
				}
			}
			return val.ToString();
		}
		
		// ----------
		
		private void ReadMediaWiki() {
			_siteinfo = null;
			_writer.WriteStartWiki();
			ReadThrough();
			_writer.WriteEndWiki();
			_siteinfo = null;
		}
		
		// ------------------
		
		private void ReadSiteInfo() {
			_siteinfo = new Siteinfo();
			ReadThrough();
			_writer.WriteSiteinfo(_siteinfo);
		}
		
		private void ReadSitename() {
			_siteinfo.Sitename = ReadElementContent();
		}
		
		private void ReadBase() {
			_siteinfo.Base = ReadElementContent();
		}
		
		private void ReadGenerator() {
			_siteinfo.Generator = ReadElementContent();
		}
		
		private void ReadCase() {
			_siteinfo.Case = ReadElementContent();
		}
		
		private void ReadNamespaces() {
			_siteinfo.Namespaces = new Hashtable();
			ReadThrough();
		}
		
		private void ReadNamespace() {
			int key = XmlConvert.ToInt32(_reader.GetAttribute("key"));
			_siteinfo.Namespaces[key] = ReadElementContent();
		}
		
		// -----------
		
		private void ReadPage() {
			_page = new Page();
			_pageSent = false;
			ReadThrough();
			
			if (_pageSent)
				_writer.WriteEndPage();
			_page = null;
		}
		
		private void ReadTitle() {
			_page.Title = new Title(ReadElementContent(), _siteinfo.Namespaces);
		}
		
		private void ReadPageId() {
			_page.Id = XmlConvert.ToInt32(ReadElementContent());
		}
		
		private void ReadRestrictions() {
			_page.Restrictions = ReadElementContent();
		}
		
		// ------
		
		private void ReadRevision() {
			if (!_pageSent) {
				_writer.WriteStartPage(_page);
				_pageSent = true;
			}
			
			_rev = new Revision();
			ReadThrough();
			_writer.WriteRevision(_rev);
			_rev = null;
		}

		private void ReadRevId() {
			_rev.Id = XmlConvert.ToInt32(ReadElementContent());
		}

		private void ReadTimestamp() {
			// This is slow, took up 10% of runtime trying 17 different formats!
			//_rev.Timestamp = XmlConvert.ToDateTime(ReadElementContent()).ToUniversalTime();
			
			// We've declared a standard format, so just check it.
			_rev.Timestamp = DateTime.ParseExact(ReadElementContent(),
				@"yyyy'-'MM'-'dd'T'HH':'mm':'ss'Z'",
				System.Globalization.CultureInfo.CurrentCulture);
		}

		private void ReadComment() {
			_rev.Comment = ReadElementContent();
		}

		private void ReadMinor() {
			_rev.Minor = true;
			_reader.Skip();
		}

		private void ReadText() {
			_rev.Text = ReadElementContent();
		}
		
		// -----------
		private void ReadContributor() {
			_contrib = null;
			
			ReadThrough();
			
			if (_contrib == null)
				throw new ArgumentException("Invalid contributor");
			
			_rev.Contributor = _contrib;
			_contrib = null;
		}


		private void ReadUsername() {
			_contrib = new Contributor(ReadElementContent());
		}

		private void ReadContribId() {
			_contrib.Id = XmlConvert.ToInt32(ReadElementContent());
		}
		
		private void ReadIp() {
			_contrib = new Contributor(ReadElementContent());
		}
	}
}
