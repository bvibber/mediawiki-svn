package de.brightbyte.wikiword.builder;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.analyzer.WikiPage;

public interface WikiPageFilter extends Filter<WikiPage> {

	public String getName();
		
}
