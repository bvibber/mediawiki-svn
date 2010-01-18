package de.brightbyte.wikiword.store;

import java.sql.SQLException;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;

public class DatabaseProximityStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> 
				extends DatabaseFeatureStore<T, R>
				implements ProximityStore<T, R> {
	
		private RelationTable proximityTable;
		
		protected DatabaseProximityStore(DatabaseWikiWordConceptStore<T, R> conceptStore, ProximityStoreSchema database, TweakSet tweaks) throws SQLException {
			super(conceptStore, database, tweaks);
			
			this.proximityTable = (RelationTable)database.getTable("proximity"); 
		}
		
		public DataSet<? extends R> getEnvironment(int concept, double minProximity) throws PersistenceException {
			String sql = "SELECT C.id as cId, C.name as cName, proximity as qRelev ";
			sql += " FROM " + conceptTable.getSQLName() + " as C ";
			sql += " JOIN "+proximityTable.getSQLName()+" as P ON P.concept2 = C.id ";
			sql += " WHERE concept1 = "+concept;
			if (minProximity>0) sql += " AND proximity >= "+minProximity;
			
			return new QueryDataSet<R>(database, conceptStore.getReferenceFactory(), "getEnvironment", sql, false);
		}

		public LabeledVector<Integer> getEnvironmentVector(int concept, double minProximity) throws PersistenceException {
			String sql = "SELECT concept2, proximity FROM " +proximityTable.getSQLName()+" as P ";
			sql += " WHERE concept1 = "+concept;
			if (minProximity>0) sql += " AND proximity >= "+minProximity;

			return readVector("getEnvironmentVector", sql, "concept2", "proximity");
		}

		public double getProximity(int concept1, int concept2) throws PersistenceException {
			if (concept1 == concept2) return 1;
			
			String sql = "SELECT proximity FROM " +proximityTable.getSQLName()+" as P ";
			sql += " WHERE concept1 = "+concept1;
			sql += " AND concept2 = "+concept2;

			try {
				return ((Number)database.executeSingleValueQuery("getProximity", sql)).doubleValue();
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}


}