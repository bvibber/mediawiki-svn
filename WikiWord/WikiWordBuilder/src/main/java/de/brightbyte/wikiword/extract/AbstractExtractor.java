package de.brightbyte.wikiword.extract;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.output.DataOutput;
import de.brightbyte.wikiword.processor.AbstractPageProcessor;

public abstract class AbstractExtractor<S extends DataOutput> extends AbstractPageProcessor implements WikiWordExtractor {
	
	protected S output;

	public AbstractExtractor(WikiTextAnalyzer analyzer, S output, TweakSet tweaks) {
		super(analyzer, tweaks);
		
		if (output==null) throw new NullPointerException();
		
		this.output = output;
	}
	
	protected void flush() throws PersistenceException {
		output.flush();
	}
	
}
