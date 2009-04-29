package de.brightbyte.wikiword.extract;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.output.DataOutput;
import de.brightbyte.wikiword.processor.AbstractProcessor;

public abstract class AbstractExtractor extends AbstractProcessor implements WikiWordExtractor {
	
	protected DataOutput output;

	public AbstractExtractor(WikiTextAnalyzer analyzer, DataOutput output, TweakSet tweaks) {
		super(analyzer, tweaks);
		
		if (output==null) throw new NullPointerException();
		
		this.output = output;
	}
	
	protected void flush() throws PersistenceException {
		output.flush();
	}
	
}
