// created on 8/26/2005 at 5:50 PM
using System.Text.RegularExpressions;

class TitleMatchFilter : PageFilter {
	Regex _regex;
	
	public TitleMatchFilter(string regexString) {
		_regex = new Regex(regexString);
	}
	
	public override bool Pass(string title) {
		return _regex.IsMatch(title);
	}
}
