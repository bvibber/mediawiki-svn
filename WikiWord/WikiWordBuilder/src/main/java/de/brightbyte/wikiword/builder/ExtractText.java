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
			"guessed from, the <wiki-or-dump> parameter)");
	}

	/*@Override
	protected TextStoreBuilder createStore() throws IOException, PersistenceException {...
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
			
			return new PlainTextOutput(out, enc);
		}
		else {
			return super.createStore();
		}
	}

	@Override
	protected TextStoreBuilder createStore(DataSource db) throws PersistenceException {
		try {
			return new DatabaseTextStoreBuilder(getCorpus(), db, tweaks);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}*/
	
	public static void main(String[] argv) throws Exception {
		ExtractText app = new ExtractText();
		app.launch(argv);
	}
}
