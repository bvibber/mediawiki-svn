package de.brightbyte.wikiword.builder;

import java.io.IOException;
import java.util.Arrays;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.store.builder.GlobalConceptStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildThesaurus extends ImportApp<GlobalConceptStoreBuilder> {

	private Corpus[] languages;

	public BuildThesaurus() {
		super("BuildThesaurus", true, false);
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<wiki>", null); //FIXME: remove!
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the <wiki-or-dump> parameter)");
		args.declare("name", null, true, String.class, "internal name for the thesaurus");
		args.declare("languages", null, true, String.class, "languages to combine into the thesaurus. If omitted, all available data-sets will be used.");
	}
	
	/*@Override
	protected GlobalConceptStoreBuilder createStore(DataSource db) throws PersistenceException {...
		try {
			String lang = args.getStringOption("languages", null); 
			Connection connection = null;
			
			if (lang==null) {
				GlobalConceptStoreSchema dummy = new GlobalConceptStoreSchema(dataset, db, tweaks, false); 
				
				languages = dummy.getLanguages();
				connection = dummy.getConnection();
			}
			else {
				String[] ll = lang.split("[,;/|\\s+]+");
				languages = new Corpus[ll.length];
				
				int i = 0;
				for (String l: ll) {
					languages[i++] = Corpus.forName(getCollectionName(), l, tweaks);
				}
			}
			
			if (connection == null) connection = db.getConnection();
			
			DatabaseGlobalConceptStoreBuilder store = new DatabaseGlobalConceptStoreBuilder(dataset, connection, tweaks);
			store.setLanguages(languages);
			((GlobalConceptStoreSchema)store.getDatabaseAccess()).setLanguages(languages);
			
			return store;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}*/
	
	@Override
	protected void run() throws Exception {
		section("-- importConcepts --------------------------------------------------");
		if (languages==null) {
			String lang = args.getStringOption("languages", null); 
			if (lang!=null) {
				String[] ll = lang.split("[,;/|\\s+]+");
				((GlobalConceptStoreBuilder)this.conceptStore).setLanguages(ll);
			}
			
			languages = ((GlobalConceptStoreBuilder)this.conceptStore).getLanguages();
		} else {
			((GlobalConceptStoreBuilder)this.conceptStore).setLanguages(languages);
		}
		
		info("Using languages: "+Arrays.toString(languages));
		
		if (agenda.beginTask("BuildThesaurus.run", "importConcepts")) {
			this.conceptStore.importConcepts();
			agenda.endTask("BuildThesaurus.run", "importConcepts");
		}
		
		section("-- buildGlobalConcepts --------------------------------------------------");
		if (agenda.beginTask("BuildThesaurus.run", "buildGlobalConcepts")) {
			this.conceptStore.buildGlobalConcepts();
			agenda.endTask("BuildThesaurus.run", "buildGlobalConcepts");
		}

		section("-- buildMeaningIndex --------------------------------------------------");
		if (agenda.beginTask("BuildThesaurus.run", "buildGlobalIndexes")) {
			this.conceptStore.buildGlobalIndexes();
			agenda.endTask("BuildThesaurus.run", "buildGlobalIndexes");
		}

		/*
		section("-- buildConceptInfo --------------------------------------------------");
		if (agenda.beginTask("BuildThesaurus.run", "buildConceptInfo")) {
			((GlobalConceptStoreBuilder)this.store).buildConceptInfo();
			agenda.endTask("BuildThesaurus.run", "buildConceptInfo");
		}
		*/

		section("-- statistics --------------------------------------------------");
		dumpTableStats();
	}	
	
	public static void main(String[] argv) throws Exception {
		BuildThesaurus app = new BuildThesaurus();
		app.launch(argv);
	}

	@Override
	protected void createStores() throws IOException, PersistenceException {
		super.createStores();
		registerTargetStore(conceptStore);
	}
	
}