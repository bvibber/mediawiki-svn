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

	public class XmlDumpWriter : IDumpWriter {
		protected TextWriter _stream;
		protected XmlTextWriter _writer;
		
		public const string Version = "0.3";
		protected const string _ns = "http://www.mediawiki.org/xml/export-" + Version;
		protected const string _schema = "http://www.mediawiki.org/xml/export-" + Version + ".xsd";
		
		public XmlDumpWriter(TextWriter output) {
			_stream = output;
			_writer = new XmlTextWriter(_stream);
			_writer.Formatting = Formatting.Indented;
		}
		
		public void Close() {
			_writer.Close();
		}
		
		public void WriteStartWiki() {
			_writer.WriteStartDocument();
			_writer.WriteStartElement("mediawiki", _ns);
			
			_writer.WriteAttributeString("xsi", "schemaLocation", "http://www.w3.org/2001/XMLSchema-instance", _schema);
			_writer.WriteAttributeString("version", Version);
			
			// TODO: store and keep the xml:lang
			_writer.WriteAttributeString("xml", "lang", "http://www.w3.org/XML/1998/namespace", "en");
		}
		
		public void WriteEndWiki() {
			_writer.WriteEndDocument();
		}
		
		public void WriteSiteinfo(Siteinfo info) {
			_writer.WriteStartElement("siteinfo");
			_writer.WriteElementString("sitename", info.Sitename);
			_writer.WriteElementString("base", info.Base);
			_writer.WriteElementString("generator", info.Generator);
			_writer.WriteElementString("case", info.Case);
			
			_writer.WriteStartElement("namespaces");
			foreach (int key in info.Namespaces.Keys) {
				_writer.WriteStartElement("namespace");
				_writer.WriteAttributeString("key", key.ToString());
				_writer.WriteString((string)info.Namespaces[key]);
				_writer.WriteEndElement();
			}
			_writer.WriteEndElement();
			
			_writer.WriteEndElement();
		}
		
		public void WriteStartPage(Page page) {
			_writer.WriteStartElement("page");
			_writer.WriteElementString("title", page.Title.ToString());
			if (page.Id != 0)
				_writer.WriteElementString("id", page.Id.ToString());
			if (page.Restrictions != "")
				_writer.WriteElementString("restrictions", page.Restrictions);
		}
		
		public void WriteEndPage() {
			_writer.WriteEndElement();
		}
		
		public void WriteRevision(Revision rev) {
			_writer.WriteStartElement("revision");
			if (rev.Id != 0)
				_writer.WriteElementString("id", rev.Id.ToString());
			
			_writer.WriteElementString("timestamp", FormatTimestamp(rev.Timestamp));
			
			WriteContributor(rev.Contributor);
			
			if (rev.Minor) {
				_writer.WriteStartElement("minor");
				_writer.WriteEndElement();
			}
			
			if (rev.Comment != "")
				_writer.WriteElementString("comment", rev.Comment);
			
			_writer.WriteStartElement("text");
			_writer.WriteAttributeString("xml", "space", "http://www.w3.org/XML/1998/namespace", "preserve");
			_writer.WriteString(rev.Text);
			_writer.WriteEndElement();
			
			_writer.WriteEndElement();
		}
		
		string FormatTimestamp(DateTime ts) {
			return ts.ToString("yyyy-MM-ddTHH:mm:ss") + "Z";
		}
		
		void WriteContributor(Contributor contrib) {
			_writer.WriteStartElement("contributor");
			if (contrib.IsAnon) {
				_writer.WriteElementString("ip", contrib.Address);
			} else {
				_writer.WriteElementString("username", contrib.Username);
				_writer.WriteElementString("id", contrib.Id.ToString());
			}
			_writer.WriteEndElement();
		}
	}
}
