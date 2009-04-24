package de.brightbyte.wikiword.builder;

import de.brightbyte.data.filter.Filter;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public interface WikiPageFilter extends Filter<WikiTextAnalyzer.WikiPage> {

	public String getName();
		
}
