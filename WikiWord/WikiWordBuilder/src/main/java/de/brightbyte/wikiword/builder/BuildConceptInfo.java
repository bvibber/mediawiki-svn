package de.brightbyte.wikiword.builder;

import javax.sql.DataSource;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.builder.ConceptInfoStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildConceptInfo extends ImportApp<ConceptInfoStoreBuilder<? extends WikiWordConcept>> {

	public BuildConceptInfo() {
		super("BuildConceptInfo", true, true);
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<wiki>", null);
		args.declareHelp("<dataset>", "name of the wiki/thesaurus to process");
		args.declare("dataset", null, true, String.class, "sets the wiki name (overrides the <wiki-or-dump> parameter)");
	}
	
	@Override
	protected ConceptInfoStoreBuilder<? extends WikiWordConcept> createStore(DataSource db) throws PersistenceException {
		return createConceptStoreBuilder(db).getConceptInfoStoreBuilder(); 
	}	

	@Override
	protected void run() throws Exception {
		section("-- build info --------------------------------------------------");
		this.store.buildConceptInfo();
	}	
	
	public static void main(String[] argv) throws Exception {
		BuildConceptInfo app = new BuildConceptInfo();
		app.launch(argv);
	}
}