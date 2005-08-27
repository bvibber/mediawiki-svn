// created on 8/26/2005 at 6:20 PM

using System;
using System.Collections;
using System.IO;

class ListFilter : PageFilter {
	IDictionary _list;
	
	public ListFilter(string sourceFileName) {
		_list = new Hashtable();
		using (TextReader input = File.OpenText(sourceFileName)) {
			string line = input.ReadLine();
			while (line != null) {
				if (!line.StartsWith("#")) {
					string cleaned = line.TrimStart(' ', '\n', '\r', '\t', ':');
					string title = cleaned.TrimEnd(' ', '\n', '\r', '\t');
					
					if (title.Length > 0)
						_list[title] = title;
				}
				line = input.ReadLine();
			}
		}
	}
	
	public override bool Pass(string title) {
		return _list.Contains(title);
	}
}
