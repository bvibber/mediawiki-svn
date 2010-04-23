package de.brightbyte.wikiword.processor;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.analyzer.WikiPage;

public class PageTitleFilter implements WikiPageFilter {
	protected Filter<String> filter;
	private String name;
	
	public PageTitleFilter(String name, Filter<String> filter) {
		if (filter==null) throw new NullPointerException();
		this.filter = filter;
		this.name = name;
	}

	public boolean matches(WikiPage page) {
		CharSequence t = page.getResourceName();
		return filter.matches(t.toString());
	}

	public String getName() {
		return name;
	}
	
	public String toString() {
		return getName();
	}

}
