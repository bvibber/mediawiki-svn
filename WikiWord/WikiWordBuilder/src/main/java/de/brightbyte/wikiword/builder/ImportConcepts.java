package de.brightbyte.wikiword.builder;

import java.io.IOException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.LocalPropertyStoreBuilder;
import de.brightbyte.wikiword.store.builder.TextStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class ImportConcepts extends ImportDump<LocalConceptStoreBuilder> {

	private LocalPropertyStoreBuilder propertyStore;
	private TextStoreBuilder textStore;

	public ImportConcepts() {
		super("ImportConcepts");
	}
	
	@Override
	protected void createStores() throws IOException, PersistenceException {
		super.createStores();
		registerTargetStore(conceptStore);
		
		textStore = conceptStore.getTextStoreBuilder();
		registerTargetStore(textStore);
		
		propertyStore = conceptStore.getPropertyStoreBuilder();
		registerTargetStore(propertyStore);
	}

	@Override
	protected WikiWordImporter newImporter(WikiTextAnalyzer analyzer) throws PersistenceException {
		return new ConceptImporter(analyzer, conceptStore, tweaks);
	}
	
	@Override
	protected void afterImport() throws PersistenceException {
		conceptStore.getStatisticsStoreBuilder().clear();
	}

	@Override
	protected boolean getDropWarnings() {
		return true;
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declare("debug", null, false, Boolean.class, "debug mode, don't write to store.");

		ConceptImporter.declareOptions(args);
	}
	
	public static void main(String[] argv) throws Exception {
		ImportConcepts app = new ImportConcepts();
		app.launch(argv);
	}
}
