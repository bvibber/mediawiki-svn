package de.brightbyte.wikiword.extract;

import java.io.IOException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.output.TextFileOutput;
import de.brightbyte.wikiword.output.TextOutput;
import de.brightbyte.wikiword.output.TextStreamOutput;
import de.brightbyte.wikiword.output.TsvTextOutput;

public class ExtractText extends ExtractFromDump<TextOutput> {

	public ExtractText() {
		super();
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		TextExtractor.declareOptions(args);
		
		args.declare("tsv", null, false, Boolean.class, "output TSV table. Default is a http-like stream.");
		args.declare("files", null, false, Boolean.class, "write output into separate files instead of a single stream. target dir must be given as second parameter");
		args.declare("hashdirs", null, false, Boolean.class, "with --files, create hash-based subdirectories. Avoids large flat directories.");
	}

	@Override
	protected TextOutput createOutput() throws PersistenceException {
		try {
			if (args.isSet("tsv")) return new TsvTextOutput(getCorpus(), getOutputWriter());
			else if (args.isSet("files")) return new TextFileOutput(getCorpus(), getOutputFile(), getOutputFileEncoding(), args.isSet("hashdirs"));
			else return new TextStreamOutput(getCorpus(), getOutputStream(), getOutputFileEncoding());
		} catch (IOException e) {
			throw new PersistenceException(e);
		} 
	}

	@Override
	protected TextExtractor newProcessor(WikiTextAnalyzer analyzer) {
		TextExtractor extractor = new TextExtractor(analyzer, output, tweaks);
		return extractor;
	}
	
	public static void main(String[] argv) throws Exception {
		ExtractText app = new ExtractText();
		app.launch(argv);
	}
	
}
