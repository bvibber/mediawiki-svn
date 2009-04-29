package de.brightbyte.wikiword.processor;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.analyzer.WikiPage;

public interface WikiPageFilter extends Filter<WikiPage> {

	public String getName();
		
}
