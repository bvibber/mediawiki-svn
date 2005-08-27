// project created on 8/23/2005 at 6:20 PM
using System;
using System.Collections;
using System.Xml;

class MainClass
{
	public static void Main(string[] args)
	{
		XmlTextReader reader = new XmlTextReader(Console.In);
		XmlTextWriter writer = new XmlTextWriter(Console.Out);
		
		FilterSet filters = new FilterSet(args);
		
		// <mediawiki>
		//   <siteinfo>
		//     ....
		//   <page>
		//     <title>
		//     ....
		
		// 1. Copy <mediawiki> open tag
		// 2. Copy whole <siteinfo> set
		// 3. Iterate through <page> siblings
		// 3a. For each <page>, check its <title> contents
		// 3b. For a match, copy the beginning and remainder of the <page> section
		// 3c. For a failure, skip until the next <page>
		// 4. Copy </mediawiki> close tag
		
		reader.ReadStartElement();
		// FIXME: write out the full root element start with all its attributes
		writer.WriteStartDocument();
		writer.WriteWhitespace("\n");
		writer.WriteStartElement("mediawiki");
		writer.WriteWhitespace("\n");
		
		reader.Read();
		while (!reader.EOF) {
			if (reader.NodeType == XmlNodeType.Element &&
				reader.LocalName.Equals("page")) {
				
				// This seems an awfully verbose way to get the <title> contents
				while (reader.NodeType != XmlNodeType.Element ||
						!reader.LocalName.Equals("title"))
					reader.Read();
				while (reader.NodeType != XmlNodeType.Text)
					reader.Read();
				string thisTitle = reader.Value;
				while (reader.NodeType != XmlNodeType.EndElement ||
						!reader.LocalName.Equals("title"))
					reader.Read();
				reader.Read();
				
				bool isMatch = filters.Pass(thisTitle);
				if (isMatch) {
					writer.WriteStartElement("page");
					writer.WriteWhitespace("\n  ");
					writer.WriteElementString("title", thisTitle);
				}
				
				// Either skip our output the rest of the stuff in <page>...</page>
				while (reader.NodeType != XmlNodeType.EndElement ||
						!reader.LocalName.Equals("page")) {
					if (isMatch) {
						writer.WriteNode(reader, false);
					} else {
						reader.Read();
					}
				}
				if (isMatch) {
					// </page>
					writer.WriteEndElement();
				}
				
				// The next iteration *should* come back with whitespace,
				// the next <page> open, or the </mediawiki> closer
				reader.Read();
				
				// Skip over whitespace so we don't bloat output
				while (reader.NodeType == XmlNodeType.Whitespace)
					reader.Read();
			} else {
				// Some non-page element; we won't mess with them.
				writer.WriteNode(reader, false);
			}
		}
		
		reader.Close();
		writer.Close();
	}
}