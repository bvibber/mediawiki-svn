package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;

public class DatabaseProximityStoreBuilder 
				extends DatabaseWikiWordStoreBuilder 
				implements ProximityStoreBuilder {
		
		protected WikiWordConceptStoreSchema conceptDatabase;
		
		protected RelationTable proximityTable;
		protected RelationTable featureTable;
		
		private DatabaseWikiWordConceptStoreBuilder conceptStore;
		
		protected DatabaseProximityStoreBuilder(DatabaseWikiWordConceptStoreBuilder conceptStore, ProximityStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
		    this.conceptStore = conceptStore;
			
			Inserter featureInserter = configureTable("feature", 8*1024, 32);
			featureTable = (RelationTable)featureInserter.getTable();
			
			Inserter proximityInserter = configureTable("proximity", 8*1024, 32);
			proximityTable = (RelationTable)proximityInserter.getTable();
		}

		public void buildFeatures() throws PersistenceException {
			...
		}

		public void buildProximity() throws PersistenceException {
			...
		}	
		
}