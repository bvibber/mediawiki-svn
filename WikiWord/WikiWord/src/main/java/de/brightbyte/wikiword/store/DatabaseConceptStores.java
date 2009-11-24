package de.brightbyte.wikiword.store;

import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;

public class DatabaseConceptStores {
	
	public static DatabaseWikiWordConceptStore<? extends WikiWordConcept, ? extends WikiWordConceptReference<? extends WikiWordConcept>> createConceptStore(DataSource db, DatasetIdentifier dataset, TweakSet tweaks, boolean allowCorpus, boolean allowThesaurus) throws PersistenceException {
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

			DatabaseWikiWordConceptStore<? extends WikiWordConcept, ? extends WikiWordConceptReference<? extends WikiWordConcept>> store;
			
			if (dataset instanceof Corpus) {
				if (!allowCorpus) throw new RuntimeException("application is not corpus-based, but a corpus dataset was provided");
				
				store = new DatabaseLocalConceptStore((Corpus)dataset, dummy.getConnection(), tweaks);
			}
			else {
				if (!allowThesaurus) throw new RuntimeException("application is not corpus, but no corpus dataset was provided");
				
				store = new DatabaseGlobalConceptStore(dataset, dummy.getConnection(), tweaks);
			}
			
			return store;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

}
