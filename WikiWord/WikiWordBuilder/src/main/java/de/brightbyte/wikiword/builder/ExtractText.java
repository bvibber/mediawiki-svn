package de.brightbyte.wikiword.builder;

import java.io.IOException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.TextStoreBuilder;

public class ExtractText extends ImportDump<LocalConceptStoreBuilder> {

	private TextStoreBuilder textStore;

	public ExtractText() {
		super("ExtractText");
	}


	@Override
	protected void createStores(WikiWordStoreFactory<? extends LocalConceptStoreBuilder> factory) throws IOException, PersistenceException {
		super.createStores(factory);
		
		textStore = conceptStore.getTextStoreBuilder();
		registerStore(textStore);
	}

	@Override
	protected TextImporter newImporter(WikiTextAnalyzer analyzer) {
		return new TextImporter(analyzer, textStore, tweaks);
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		ConceptImporter.declareOptions(args);
		
		args.declareHelp("<dump-file>", "the dump file to process");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the name given by, or " +
			"guessed from, the <dump-file> parameter)");
	}
	
	public static void main(String[] argv) throws Exception {
		ExtractText app = new ExtractText();
		app.launch(argv);
	}
}
