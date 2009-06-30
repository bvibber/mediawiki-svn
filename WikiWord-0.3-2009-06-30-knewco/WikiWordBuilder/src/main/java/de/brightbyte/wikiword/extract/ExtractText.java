package de.brightbyte.wikiword.extract;

import java.io.IOException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.builder.ConceptImporter;
import de.brightbyte.wikiword.output.PlainTextOutput;
import de.brightbyte.wikiword.output.TextOutput;
import de.brightbyte.wikiword.output.TsvTextOutput;

public class ExtractText extends ExtractFromDump<TextOutput> {

	public ExtractText() {
		super();
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		ConceptImporter.declareOptions(args);
		
		args.declare("tsv", null, false, Boolean.class, "output TSV table");
	}

	@Override
	protected TextOutput createOutput() throws PersistenceException {
		try {
			if (args.isSet("tsv")) return new TsvTextOutput(getCorpus(), getOutputWriter());
			else return new PlainTextOutput(getCorpus(), getOutputStream(), getOutputFileEncoding());
		} catch (IOException e) {
			throw new PersistenceException(e);
		} 
	}

	@Override
	protected TextExtractor newProcessor(WikiTextAnalyzer analyzer) {
		return new TextExtractor(analyzer, output, tweaks);
	}
	
	public static void main(String[] argv) throws Exception {
		ExtractText app = new ExtractText();
		app.launch(argv);
	}
	
}
