package de.brightbyte.wikiword.extract;

import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.processor.AbstractProcessor;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public abstract class AbstractExtractor extends AbstractProcessor {

	public AbstractExtractor(WikiTextAnalyzer analyzer, WikiWordStoreBuilder store, TweakSet tweaks) {
		super(analyzer, store, tweaks);
	}
	
}
