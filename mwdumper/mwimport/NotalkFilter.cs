// created on 8/30/2005 at 11:51 PM

using System;

public class NotalkFilter : PageFilter {
	public NotalkFilter(IDumpWriter sink) : base(sink) {
	}
	
	protected override bool Pass(Page page) {
		return !page.Title.IsTalk;
	}
}
