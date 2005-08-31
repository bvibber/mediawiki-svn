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
		IDumpWriter writer = new XmlDumpWriter(Console.Out);
		
		TextReader inputStream = OpenInput(args);
		XmlDumpReader reader = new XmlDumpReader(inputStream, writer);
		
		reader.ReadDump();
	}

	static TextReader OpenInput(string[] args) {
		if (args.Length == 0 || args[0] == "-") {
			return Console.In; // hope it's not borken
		}
		return File.OpenText(args[0]);
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
