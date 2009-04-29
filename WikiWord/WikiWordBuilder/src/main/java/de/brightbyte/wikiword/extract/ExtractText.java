package de.brightbyte.wikiword.extract;

import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.builder.ConceptImporter;
import de.brightbyte.wikiword.output.TextOutput;
import de.brightbyte.wikiword.output.TsvTextOutput;

public class ExtractText extends ExtractFromDump<TextOutput> {

	public ExtractText() {
		super();
	}


	@Override
	protected TextExtractor newProcessor(WikiTextAnalyzer analyzer) {
		return new TextExtractor(analyzer, output, tweaks);
	}
	
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		ConceptImporter.declareOptions(args);
		
		args.declareHelp("<dump-file>", "the dump file to process");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the name given by, or " +
			"guessed from, the <dump-file> parameter)");

		args.declare("plain", null, false, Boolean.class, "output plain text");
		args.declare("tsv", null, false, Boolean.class, "output TSV table");
	}
	
	public static void main(String[] argv) throws Exception {
		ExtractText app = new ExtractText();
		app.launch(argv);
	}

	@Override
	protected TextOutput createOutput() {
		return new TsvTextOutput(getCorpus(), getOutputWriter());
	}
	
}
