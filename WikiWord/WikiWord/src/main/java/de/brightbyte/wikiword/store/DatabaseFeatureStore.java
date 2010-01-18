package de.brightbyte.wikiword.store;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;

public class DatabaseFeatureStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> 
				extends DatabaseWikiWordStore
				implements FeatureTopologyStore<T, R> {
	
		protected DatabaseWikiWordConceptStore<T, R> conceptStore;
		protected RelationTable featureTable;
		protected EntityTable conceptTable;
		
		private DatabaseDataSet.Factory<WikiWordConceptFeatures> conceptFeaturesFactory = new DatabaseDataSet.Factory<WikiWordConceptFeatures>() {
		
			public WikiWordConceptFeatures newInstance(ResultSet row) throws Exception {
				int concept = -1;
				String name = null;
				LabeledVector<Integer> f = new MapLabeledVector<Integer>(); 
				
				do {
					int c = row.getInt("concept");
					if (concept < 0) {
						concept = c;
						name = row.getString("name");
					}
					else if (concept != c) break;
					
					int feature = row.getInt("feature");
					double value = row.getDouble("value");
					
					f.set(feature, value);
				} while (row.next());
				
				if (concept<0) return null;
				
				WikiWordConceptReference<WikiWordConcept> r = new WikiWordConceptReference<WikiWordConcept>(concept, name, 1, -1); //TODO: global vs. local
				return new WikiWordConceptFeatures(r, f);
			}
		
		};
		
		protected DatabaseFeatureStore(DatabaseWikiWordConceptStore<T, R> conceptStore, ProximityStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
		    this.conceptStore = conceptStore;

		    this.conceptTable = (EntityTable)conceptStore.getDatabaseAccess().getTable("concept"); 
			this.featureTable = (RelationTable)database.getTable("feature"); 
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
				
				v.set(k, n instanceof Double ? (Double)n : n.doubleValue());
			}
			
			return v;
		}

		public DataSet<WikiWordConceptFeatures> getNeighbourhoodFeatures(int concept) throws PersistenceException {
			String sql = "SELECT X.concept as concept, X.feature as feature, X.value as value ";
			sql += " FROM " + featureTable.getSQLName() + " as X ";
			sql += " JOIN "+featureTable.getSQLName()+" as N ON N.feature = C.id ";
			sql += " JOIN "+featureTable.getSQLName()+" as F ON F.feature = N.concept ";
			sql += " WHERE F.concept = "+concept;
			sql += " ORDER BY C.id";
			
			return new QueryDataSet<WikiWordConceptFeatures>(database, getConceptFeaturesFactory(), "getNeighbourhoodFeatures", sql, false);
		}

		private DatabaseDataSet.Factory<WikiWordConceptFeatures> getConceptFeaturesFactory() {
			return conceptFeaturesFactory;
		}

		public DataSet<? extends R> getNeighbours(int concept) throws PersistenceException {
			String sql = "SELECT DISTINCT C.id as cId, C.name as cName ";
			sql += " FROM " + conceptTable.getSQLName() + " as C ";
			sql += " JOIN "+featureTable.getSQLName()+" as N ON N.feature = C.id ";
			sql += " JOIN "+featureTable.getSQLName()+" as F ON F.feature = N.concept ";
			sql += " WHERE F.concept = "+concept+" ";
			
			return new QueryDataSet<R>(database, conceptStore.getReferenceFactory(), "getNeighbours", sql, false);
		}

		public List<Integer> getNeighbourList(int concept) throws PersistenceException {
			String sql = "SELECT DISTINCT N.feature as concept ";
			sql += " FROM "+featureTable.getSQLName()+" as N ";
			sql += " JOIN "+featureTable.getSQLName()+" as F ON F.feature = N.concept ";
			sql += " WHERE F.concept = "+concept;
			
			return readIdList("getNeighbourList", sql, "concept");
		}
		
		protected  List<Integer> readIdList(String name, String sql, String valueField) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				try {
					return readIdList(rs, valueField, new ArrayList<Integer>());
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  List<Integer> readIdList(ResultSet rs, String valueField, List<Integer> v) throws SQLException {
			if (v==null) v = new ArrayList<Integer>();
			
			while (rs.next()) {
				Number n = (Number)rs.getObject(valueField);
				
				v.add(n instanceof Integer ? (Integer)n : n.intValue());
			}
			
			return v;
		}
		
}