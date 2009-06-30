package de.brightbyte.wikiword.schema;

import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;

public class ConceptStoreSchemas {
	
	public static WikiWordConceptStoreSchema createConceptStoreSchema(DataSource db, DatasetIdentifier dataset, TweakSet tweaks, boolean useFlushQueue, boolean allowCorpus, boolean allowThesaurus) throws PersistenceException {
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

			WikiWordConceptStoreSchema schema;
			
			if (dataset instanceof Corpus) {
				if (!allowCorpus) throw new RuntimeException("application is not corpus-based, but a corpus dataset was provided");
				
				schema = new LocalConceptStoreSchema((Corpus)dataset, dummy.getConnection(), tweaks, useFlushQueue);
			}
			else {
				if (!allowThesaurus) throw new RuntimeException("application is not corpus, but no corpus dataset was provided");
				
				schema = new GlobalConceptStoreSchema(dataset, dummy.getConnection(), tweaks, useFlushQueue);
			}
			
			return schema;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

}
