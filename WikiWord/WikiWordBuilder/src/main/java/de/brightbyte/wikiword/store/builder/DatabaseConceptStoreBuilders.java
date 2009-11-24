package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;

public class DatabaseConceptStoreBuilders {
		
	public static DatabaseWikiWordConceptStoreBuilder<? extends WikiWordConcept> createConceptStoreBuilder(DataSource db, DatasetIdentifier dataset, TweakSet tweaks, Agenda agenda, boolean allowCorpus, boolean allowThesaurus) throws PersistenceException {
		//XXX: UGLY HACK!
		try {
			DatabaseSchema dummy = new WikiWordStoreSchema(dataset, db, tweaks, false);
			
			if (!(dataset instanceof Corpus)) {
				String sql = "show tables like "+dummy.quoteString(dummy.getSQLTableName("resource", true));
				Object x = dummy.executeSingleValueQuery("createConceptStore#probe", sql);
				
				//found resource table, must be a wiki corpus (local concept store)
				if (x != null) {
					dataset = Corpus.forDataset(dataset, tweaks);
				}
			}

			DatabaseWikiWordConceptStoreBuilder<?> store;
			
			if (dataset instanceof Corpus) {
				if (!allowCorpus) throw new RuntimeException("application is not corpus-based, but a corpus dataset was provided");
				
				store = new DatabaseLocalConceptStoreBuilder((Corpus)dataset, dummy.getConnection(), tweaks, agenda);
			}
			else {
				if (!allowThesaurus) throw new RuntimeException("application is not corpus, but no corpus dataset was provided");
				
				store = new DatabaseGlobalConceptStoreBuilder(dataset, dummy.getConnection(), tweaks, agenda);
			}
			
			return store;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

}
