// created on 8/30/2005 at 11:28 PM

using System;
using System.Collections;

public class NamespaceFilter : PageFilter {
	bool _invert;
	IDictionary _matches;
	
	public NamespaceFilter(IDumpWriter sink, string configString) : base(sink) {
		_invert = configString.StartsWith("!");
		_matches = new Hashtable();
		
		string[] namespaceKeys = {
			"NS_MAIN",
			"NS_TALK",
			"NS_USER",
			"NS_USER_TALK",
			"NS_PROJECT",
			"NS_PROJECT_TALK",
			"NS_IMAGE",
			"NS_IMAGE_TALK",
			"NS_MEDIAWIKI",
			"NS_MEDIAWIKI_TALK",
			"NS_TEMPLATE",
			"NS_TEMPLATE_TALK",
			"NS_HELP",
			"NS_HELP_TALK",
			"NS_CATEGORY",
			"NS_CATEGORY_TALK" };
		
		string[] itemList = configString.Trim('!', ' ', '\t').Split(',');
		foreach (string keyString in itemList) {
			string trimmed = keyString.Trim();
			try {
				int key = int.Parse(trimmed);
				_matches[key] = key;
			} catch {
				for (int key = 0; key < namespaceKeys.Length; key++) {
					if (trimmed == namespaceKeys[key])
						_matches[key] = key;
				}
			}
		}
	}
	
	protected override bool Pass(Page page) {
		return _invert ^ _matches.Contains(page.Title.Namespace);
	}
}
