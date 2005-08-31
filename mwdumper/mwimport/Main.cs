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
using System.Text.RegularExpressions;
using System.Xml;

using ICSharpCode.SharpZipLib.GZip;

class MainClass {
	public static void Main(string[] args) {
		TextReader input = null;
		TextWriter output = null;
		IDumpWriter sink = null;
		
		foreach (string arg in args) {
			string opt = null, val = null, param = null;
			if (SplitArg(arg, out opt, out val, out param)) {
				if (opt == "output") {
					if (output != null)
						throw new ArgumentException("Currently only one output is supported.");
					output = OpenOutputFile(val, param);
				} else if (opt == "format") {
					if (output == null)
						output = Console.Out;
					if (sink != null)
						throw new ArgumentException("Only one format per output allowed.");
					sink = OpenOutputSink(output, val, param);
				} else if (opt == "filter") {
					if (sink == null) {
						if (output == null)
							output = Console.Out;
						sink = new XmlDumpWriter(output);
					}
					sink = AddFilter(sink, val, param);
				} else {
					throw new ArgumentException("Unrecognized option " + opt);
				}
			} else if (arg == "-") {
				if (input != null)
					throw new ArgumentException("Input already set; can't set to stdin");
				input = Console.In;
			} else {
				if (input != null)
					throw new ArgumentException("Input already set; can't set to " + arg);
				input = File.OpenText(arg);
			}
		}
		
		if (input == null)
			input = Console.In;
		if (output == null)
			throw new ArgumentException("You managed not to specify any output.");
		if (sink == null)
			throw new ArgumentException("You managed not to specify any output format.");
		
		XmlDumpReader reader = new XmlDumpReader(input, sink);
		reader.ReadDump();
	}
	
	static bool SplitArg(string arg, out string opt, out string val, out string param) {
		opt = "";
		val = "";
		param = "";
		
		if (!arg.StartsWith("--"))
			return false;
		
		string[] bits = arg.Substring(2).Split(new char[] {'='}, 2);
		opt = bits[0];
		
		if (bits.Length > 1) {
			string[] bits2 = bits[1].Split(new char[] {':'}, 2);
			val = bits2[0];
			if (bits2.Length > 1)
				param = bits2[1];
		}
		
		//Console.WriteLine("'{0}' '{1}' '{2}'", opt, val, param);
		return true;
	}
	
	static TextWriter OpenOutputFile(string dest, string param) {
		if (dest == "stdout")
			return Console.Out;
		else if (dest == "file")
			return File.CreateText(param);
		else if (dest == "gzip")
			return new StreamWriter(new GZipOutputStream(File.Create(param)));
		else
			throw new ArgumentException("Destination sink not implemented: " + dest);
	}
	
	static IDumpWriter OpenOutputSink(TextWriter output, string format, string param) {
		if (format == "xml")
			return new XmlDumpWriter(output);
		else if (format == "sql") {
			if (param == "1.4")
				return new SqlWriter14(output);
			else if (param == "sql1.5")
				return new SqlWriter15(output);
			else
				throw new ArgumentException("SQL version not known: " + param);
		} else
			throw new ArgumentException("Output format not known: " + format);
	}
	
	static IDumpWriter AddFilter(IDumpWriter sink, string filter, string param) {
		if (filter == "latest")
			return new LatestFilter(sink);
		else if (filter == "namespace")
			return new NamespaceFilter(sink, param);
		else if (filter == "notalk")
			return new NotalkFilter(sink);
		else if (filter == "titlematch")
			return new TitleMatchFilter(sink, param);
		else if (filter == "list")
			return new ListFilter(sink, param);
		else
			throw new ArgumentException("Filter unknown: " + filter);
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
