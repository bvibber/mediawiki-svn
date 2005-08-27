// created on 8/26/2005 at 5:55 PM
using System.Collections;

class FilterSet {
	ArrayList _set;
	
	public FilterSet() {
		_set = new ArrayList();
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
