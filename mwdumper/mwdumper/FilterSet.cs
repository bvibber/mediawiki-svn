// created on 8/26/2005 at 5:55 PM
using System.Collections;

class FilterSet {
	ArrayList _set;
	
	public FilterSet() {
		_set = new ArrayList();
	}
	
	public FilterSet(string[] args) {
		_set = new ArrayList();
		for (int i = 0; i < args.Length; i++) {
			string param = args[i];
			if (param.Equals("--regex"))
				Add(new TitleMatchFilter(args[++i]));
			else if (param.Equals("--list"))
				Add(new ListFilter(args[++i]));
		}
	}
	
	public void Add(PageFilter filter) {
		_set.Add(filter);
	}
	
	public bool Pass(string title) {
		foreach (PageFilter filter in _set)
			if (filter.Pass(title))
				return true;
		return false;
	}
}
