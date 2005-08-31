// project created on 8/28/2005 at 11:08 PM

/*
	-> read header info
	site name, url, language, namespace keys
	
	-> read pages.....
	<page>
		-> get title, etc
		<revision>
			-> store each revision
			on next one or end of sequence, write out
			[so for 1.4 schema we can be friendly]
	
	progress report:
		if possible, a percentage through file. this might not be possible.
		rates and counts definitely
	
	input:
		stdin or file
		allow gzip -> autodetect if possible
	
	output:
		SQL on stdout
		SQL on file
		SQL directly to a server
	
	output formats:
		1.4 schema
		1.5 schema
		
*/

using System;
using System.Collections;
using System.IO;
using System.Text;
using System.Xml;

using ICSharpCode.SharpZipLib.GZip;

class MainClass {
	public static void Main(string[] args) {
		TextReader inputStream = OpenInput(args);
		XmlTextReader reader = new XmlTextReader(inputStream);
		reader.WhitespaceHandling = WhitespaceHandling.Significant;
		
		//IDumpWriter writer = new SqlWriter14(Console.Out);
		IDumpWriter writer = new XmlDumpWriter(Console.Out);
		
		writer.WriteStartWiki();
		Siteinfo siteinfo = null;
		
		while (reader.Read()) {
			if (reader.NodeType == XmlNodeType.Element) {
				string name = reader.LocalName;
				if (name.Equals("siteinfo")) {
					siteinfo = ReadSiteinfo(reader, writer);
				} else if (name.Equals("page")) {
					ReadPage(reader, writer, siteinfo);
				}
			}
		}
		reader.Close();
		writer.WriteEndWiki();
	}
	
	static TextReader OpenInput(string[] args) {
		if (args.Length == 0 || args[0] == "-") {
			return Console.In; // hope it's not borken
		}
		return File.OpenText(args[0]);
	}
	
	static Siteinfo ReadSiteinfo(XmlReader reader, IDumpWriter writer) {
		Siteinfo info = new Siteinfo();
		while (reader.Read()) {
			string name = reader.LocalName;
			if (reader.NodeType == XmlNodeType.Element) {
				string val = ReadElementContent(reader);
				if (name.Equals("sitename"))
					info.Sitename = val;
				else if (name.Equals("base"))
					info.Base = val;
				else if (name.Equals("generator"))
					info.Generator = val;
				else if (name.Equals("case"))
					info.Case = val;
				else if (name.Equals("namespaces"))
					info.Namespaces = ReadNamespaces(reader);
			} else if (reader.NodeType == XmlNodeType.EndElement && name.Equals("siteinfo")) {
				writer.WriteSiteinfo(info);
				return info;
			}
		}
		throw new ArgumentException("Ran out of XML early; incomplete <siteinfo>");
	}
	
	static IDictionary ReadNamespaces(XmlReader reader) {
		Hashtable namespaces = new Hashtable();
		while (reader.Read()) {
			string name = reader.LocalName;
			if (reader.NodeType == XmlNodeType.Element && name.Equals("namespace")) {
				int key = XmlConvert.ToInt32(reader.GetAttribute("key"));
				namespaces[key] = ReadElementContent(reader);
			} else if (reader.NodeType == XmlNodeType.EndElement && name.Equals("namespaces")) {
				return namespaces;
			}
		}
		throw new ArgumentException("Ran out of XML early; incomplete <namespaces>");
	}
	
	static string ReadElementContent(XmlReader reader) {
		StringBuilder val = new StringBuilder();
		while (reader.Read()) {
			//Console.WriteLine("XXX: " + reader.NodeType + ", " + reader.LocalName + ", " + reader.Value);
			switch (reader.NodeType) {
			case XmlNodeType.SignificantWhitespace:
			case XmlNodeType.Text:
				reader.MoveToContent();
				val.Append(reader.Value);
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
	
	static void ReadPage(XmlReader reader, IDumpWriter writer, Siteinfo siteinfo) {
		Page page = new Page();
		
		if (siteinfo == null)
			throw new ArgumentException("siteinfo must exist");
		if (siteinfo.Namespaces == null)
			throw new ArgumentException("Namespaces must exist");
		
		while (reader.Read()) {
			string name = reader.LocalName;
			if (reader.NodeType == XmlNodeType.Element) {
				if (name.Equals("revision"))
					break; // Move on to Stage Two
				
				string val = ReadElementContent(reader);
				
				if (name.Equals("title"))
					page.Title = new Title(val, siteinfo.Namespaces);
				else if (name.Equals("id"))
					page.Id = XmlConvert.ToInt32(val);
				else if (name.Equals("restrictions"))
					page.Restrictions = val;
			}
		}
		
		writer.WriteStartPage(page);
		do {
			string name = reader.LocalName;
			if (reader.NodeType == XmlNodeType.Element && name.Equals("revision"))
				ReadRevision(reader, writer, siteinfo);
			else if (reader.NodeType == XmlNodeType.EndElement && name.Equals("page"))
				break;
		} while(reader.Read());
		writer.WriteEndPage();
	}
	
	static void ReadRevision(XmlReader reader, IDumpWriter writer, Siteinfo siteinfo) {
		Revision rev = new Revision();
		while (reader.Read()) {
			string name = reader.LocalName;
			if (reader.NodeType == XmlNodeType.Element) {
				if (name.Equals("contributor")) {
					rev.Contributor = ReadContributor(reader);
					continue;
				}
				string val = ReadElementContent(reader);
				if (name.Equals("id"))
					rev.Id = XmlConvert.ToInt32(val);
				else if (name.Equals("timestamp"))
					rev.Timestamp = XmlConvert.ToDateTime(val);
				else if (name.Equals("minor"))
					rev.Minor = true;
				else if (name.Equals("comment"))
					rev.Comment = val;
				else if (name.Equals("text"))
					rev.Text = val;
			} else if (reader.NodeType == XmlNodeType.EndElement && name.Equals("revision")) {
				writer.WriteRevision(rev);
				return;
			}
		}
	}
	
	static Contributor ReadContributor(XmlReader reader) {
		Contributor contrib = null;
		while (reader.Read()) {
			string name = reader.LocalName;
			//Console.WriteLine("??? " + name);
			if (reader.NodeType == XmlNodeType.Element) {
				string val = ReadElementContent(reader);
				//Console.WriteLine("-- " + name + ": " + val);
				if (name.Equals("ip"))
					contrib = new Contributor(val);
				else if (name.Equals("username"))
					contrib = new Contributor(val);
				else if (name.Equals("id"))
					contrib.Id = XmlConvert.ToInt32(val);
			} else if (reader.NodeType == XmlNodeType.EndElement && name.Equals("contributor")) {
				if (contrib == null)
					throw new ArgumentException("Didn't find valid contents for a <contributor>");
				return contrib;
			}
		}
		throw new ArgumentException("Ran out of XML early; couldn't complete <contributor>");
	}
	
	public static void Test(string[] args) {
		Siteinfo info = new Siteinfo();
		info.Sitename = "OneFive";
		info.Base = "http://localhost/head/index.php/Main_Page";
		info.Generator = "MediaWiki 1.6alpha";
		info.Case = "first-letter";
		info.Namespaces = new Hashtable();
		info.Namespaces[-2] = "Media";
		info.Namespaces[-1] = "Special";
		info.Namespaces[0] = "";
		info.Namespaces[1] = "Talk";
		
		Page page = new Page();
		page.Id = 1;
		page.Title = new Title("Talk:Main Page", info.Namespaces);
		page.Restrictions = "";
		
		Revision revision = new Revision();
		revision.Id = 1;
		revision.Text = "This is a bunch of stuff\nyo momma!";
		revision.Minor = true;
		revision.Timestamp = DateTime.UtcNow;
		revision.Contributor = new Contributor("WikiSysop", 1);
		revision.Comment = "wacky edit o doom (it's all good)";
		
		Revision revision2 = new Revision();
		revision2.Id = 2;
		revision2.Text = "''''''''\"\"\"\"\"\"\"\"\"\"\"\"\"\"\"\"\"\" VANDALE!!!!!";
		revision2.Minor = false;
		revision2.Timestamp = DateTime.UtcNow;
		revision2.Contributor = new Contributor("127.0.0.1");
		revision2.Comment = "/* fuk uuuu */";
		
		SqlWriter14 writer = new SqlWriter14(Console.Out);
		writer.WriteStartWiki();
		writer.WriteSiteinfo(info);
		writer.WriteStartPage(page);
		writer.WriteRevision(revision);
		writer.WriteRevision(revision2);
		writer.WriteEndPage();
		writer.WriteEndWiki();
	}
}
