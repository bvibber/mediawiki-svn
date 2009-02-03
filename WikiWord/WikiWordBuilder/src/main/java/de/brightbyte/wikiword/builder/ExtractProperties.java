package de.brightbyte.wikiword.builder;

import java.io.IOException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.PropertyStoreBuilder;

public class ExtractProperties extends ImportDump<LocalConceptStoreBuilder> {

	private PropertyStoreBuilder propertyStore;

	public ExtractProperties() {
		super("ExtractProperties");
	}

	@Override
	protected void createStores(WikiWordStoreFactory<? extends LocalConceptStoreBuilder> factory) throws IOException, PersistenceException {
		super.createStores(factory);
		
		propertyStore = conceptStore.getPropertyStoreBuilder();
		registerStore(propertyStore);
	}
	
	@Override
	protected PropertyImporter newImporter(WikiTextAnalyzer analyzer) throws PersistenceException {
		return new PropertyImporter(analyzer, conceptStore, tweaks);
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		ConceptImporter.declareOptions(args);
		
		args.declareHelp("<dump-file>", "the dump file to process");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the name given by, or " +
			"guessed from, the <wiki-or-dump> parameter)");
	}

	/*
	@Override
	protected PropertyStoreBuilder createStore() throws IOException, PersistenceException {
		if (args.isSet("stream")) {
			String n = args.getOption("stream", null);
			OutputStream out;
			String enc = args.getStringOption("encoding", "utf-8");
			
			if (n.equals("-")) {
				out = System.out;
			}
			else {
				File f = new File(n);
				out = new BufferedOutputStream(new FileOutputStream(f, args.isSet("append")));
			}
			
			//TODO: rdf, etc
			return new TsvPropertyOutput(getCorpus(), out, enc);
		}
		else {
			return super.createStore();
		}
	}
	*/
	
	public static void main(String[] argv) throws Exception {
		ExtractProperties app = new ExtractProperties();
		app.launch(argv);
	}
}
