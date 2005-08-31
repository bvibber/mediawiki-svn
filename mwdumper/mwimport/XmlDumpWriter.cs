// created on 8/30/2005 at 4:53 PM
// created on 8/29/2005 at 12:41 AM

using System;
using System.Collections;
using System.IO;
using System.Text;
using System.Xml;

class XmlDumpWriter : IDumpWriter {
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
