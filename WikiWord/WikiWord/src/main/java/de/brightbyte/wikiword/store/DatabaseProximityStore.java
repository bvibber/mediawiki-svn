package de.brightbyte.wikiword.store;

import java.sql.ResultSet;
import java.sql.SQLException;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;
import de.brightbyte.wikiword.store.DatabaseWikiWordConceptStore.DatabaseConceptInfoStore.ConceptFactory;

public class DatabaseProximityStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> 
				extends DatabaseWikiWordStore
				implements ProximityStore<T, R> {
	
		private DatabaseWikiWordConceptStore conceptStore;
		private RelationTable featureTable;
		private RelationTable proximityTable;
		private EntityTable conceptTable;
		
		protected DatabaseProximityStore(DatabaseWikiWordConceptStore conceptStore, ProximityStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
		    this.conceptStore = conceptStore;

		    this.conceptTable = (EntityTable)conceptStore.getDatabaseAccess().getTable("concept"); 
			this.featureTable = (RelationTable)database.getTable("feature"); 
			this.proximityTable = (RelationTable)database.getTable("proximity"); 
		}
		
		public DataSet<? extends R> getEnvironment(int concept, double minProximity) throws PersistenceException {
			String sql = "SELECT C.*, proximity FROM " + conceptTable.getSQLName() + " as C ";
			sql += " JOIN "+proximityTable.getSQLName()+" as P ON P.concept2 = C.id ";
			sql += " WHERE concept1 = "+concept;
			
			return new QueryDataSet<R>(database, referenceFactory, "getEnvironment", sql, false);
		}

		public LabeledVector<Integer> getEnvironmentVector(int concept, double minProximity) throws PersistenceException {
			String sql = "SELECT concept2, proximity FROM " +proximityTable.getSQLName()+" as P ";
			sql += " WHERE concept1 = "+concept;

			return readVector("getEnvironmentVector", sql, "concept2", "proximity");
		}

		public LabeledVector<Integer> getFeatureVector(int concept) throws PersistenceException {
			String sql = "SELECT feature, normal_value FROM " +featureTable.getSQLName()+" as F ";
			sql += " WHERE concept = "+concept;

			return readVector("getFeatireVector", sql, "feature", "normal_value");
		}

		protected  <K> LabeledVector<K> readVector(String name, String sql, String keyField, String valueField) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				try {
					return readVector(rs, keyField, valueField, new MapLabeledVector<K>());
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  <K> LabeledVector<K> readVector(ResultSet rs, String keyField, String valueField, LabeledVector<K> v) throws SQLException {
			if (v==null) v = new MapLabeledVector<K>();
			
			while (rs.next()) {
				K k = (K)rs.getObject(keyField);
				Number n = (Number)rs.getObject(valueField);
				
				v.set(k, n.doubleValue());
			}
			
			return v;
		}
		
		public double getProximity(int concept1, int concept2) throws PersistenceException {
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