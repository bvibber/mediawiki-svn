// created on 8/26/2005 at 5:50 PM
using System.Text.RegularExpressions;

public class TitleMatchFilter : PageFilter {
	Regex _regex;
	
	public TitleMatchFilter(IDumpWriter sink, string regexString) : base(sink) {
		_regex = new Regex(regexString);
	}
	
	protected override bool Pass(Page page) {
		return _regex.IsMatch(page.Title.ToString());
	}
}
