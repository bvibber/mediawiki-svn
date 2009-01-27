package de.brightbyte.wikiword.builder;

import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.store.builder.DatabaseLocalConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class ImportConcepts extends ImportDump<LocalConceptStoreBuilder> {

	public ImportConcepts() {
		super("ImportDump");
	}

	@Override
	protected ConceptImporter newImporter(WikiTextAnalyzer analyzer, LocalConceptStoreBuilder store, TweakSet tweaks) {
		return new ConceptImporter(analyzer, store, tweaks);
	}
	
	@Override
	protected void afterImport() throws PersistenceException {
		store.getStatisticsStoreBuilder().clear();
	}

	@Override
	protected LocalConceptStoreBuilder createStore(DataSource db) throws PersistenceException  {
		try {
			return new DatabaseLocalConceptStoreBuilder(getCorpus(), db, tweaks);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected boolean getDropWarnings() {
		return true;
	}

	public static void main(String[] argv) throws Exception {
		ImportConcepts app = new ImportConcepts();
		app.launch(argv);
	}
}
