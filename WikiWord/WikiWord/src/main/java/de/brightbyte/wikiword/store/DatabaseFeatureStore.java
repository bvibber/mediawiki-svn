package de.brightbyte.wikiword.store;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.db.TemporaryTableDataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;

public class DatabaseFeatureStore<T extends WikiWordConcept> 
				extends DatabaseWikiWordStore
				implements FeatureTopologyStore<T, Integer> {
	
		protected static enum NormalizationMode {
			NEVER, AUTO, ALWAYS
		}

		protected WikiWordConcept.Factory<T> conceptFactory;
		protected DatabaseWikiWordConceptStore<T> conceptStore;
		protected RelationTable featureTable;
		protected EntityTable conceptTable;
		
		private DatabaseDataSet.Factory<ConceptFeatures<T, Integer>> conceptFeaturesFactory = new DatabaseDataSet.Factory<ConceptFeatures<T, Integer>>() {
		
			public ConceptFeatures<T, Integer> newInstance(ResultSet row) throws Exception {
				int concept = -1;
				String name = null;
				LabeledVector<Integer> f = ConceptFeatures.newIntFeaturVector(); 
				
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
				
				T r = newConcept(concept, name, null, 1, -1); //TODO: global vs. local
				return new ConceptFeatures<T, Integer>(r, f);
			}
		
		};
		
		protected int maxConceptFeatures;
		
		protected DatabaseFeatureStore(DatabaseWikiWordConceptStore<T> conceptStore, ProximityStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
		    this.conceptStore = conceptStore;
			this.conceptFactory = conceptStore.getConceptFactory();

		    this.conceptTable = (EntityTable)conceptStore.getDatabaseAccess().getTable("concept"); 
			this.featureTable = (RelationTable)database.getTable("feature");
			
			this.maxConceptFeatures = tweaks.getTweak("dbstore.maxConceptFeatures", 4*1024);
		}

		protected T newConcept(int id, String name, ConceptType type, int cardinality, double relevance) throws PersistenceException {
			return conceptFactory.newInstance(id, name, type, cardinality, relevance);
		}

		public ConceptFeatures<T, Integer> getConceptFeatures(int concept) throws PersistenceException {
			String sql = "SELECT concept, feature, normal_weight FROM " +featureTable.getSQLName()+" as F ";
			sql += " WHERE concept = "+concept;
			if (maxConceptFeatures>0) sql += " ORDER BY normal_weight DESC LIMIT "+maxConceptFeatures; 

			return readConceptFeatures("getFeatureVector", sql, "concept", null, null, null, "feature", "normal_weight"); //FIXME: name, card, relevance
		}

		public Map<Integer, ConceptFeatures<T, Integer>> getConceptsFeatures(int[] concepts) throws PersistenceException {
			if (concepts.length==0) return Collections.emptyMap();
			
			try {
				String sql = "SELECT concept, feature, normal_weight FROM " +featureTable.getSQLName()+" as F ";
				sql += " WHERE concept IN "+database.encodeSet(concepts);
				if (maxConceptFeatures>0) sql += " ORDER BY concept, normal_weight DESC"; 

				return readConceptsFeatures("getFeatureVectors", sql, "concept", null, null, null, "feature", "normal_weight"); //FIXME: name, card, relevance
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  <K> LabeledVector<K> readVector(String name, String sql, String keyField, String valueField, int limit, NormalizationMode normalize) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				try {
					LabeledVector<K> v = readVector(rs, null, keyField, valueField, new MapLabeledVector<K>(), limit, normalize);
					return v;
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  ConceptFeatures<T, Integer> readConceptFeatures(String name, String sql, 
				String conceptField, String nameField, String cardinalityField, String relevanceField,   
				String keyField, String valueField) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				try {
					return readConceptFeatures(rs, conceptField, nameField, cardinalityField, relevanceField, keyField, valueField);
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  Map<Integer, ConceptFeatures<T, Integer>> readConceptsFeatures(String name, String sql, 
				String conceptField, String nameField, String cardinalityField, String relevanceField,   
				String keyField, String valueField) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				Map<Integer, ConceptFeatures<T, Integer>> features = new HashMap<Integer, ConceptFeatures<T, Integer>>();
				
				try {
					while (!rs.isAfterLast()) {
						ConceptFeatures<T, Integer> f = readConceptFeatures(rs, conceptField, nameField, cardinalityField, relevanceField, keyField, valueField);
						features.put(f.getId(), f);
					}
					
					return features;
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public ConceptFeatures<T, Integer> readConceptFeatures(ResultSet rs, 
				String conceptField, String nameField, String cardinalityField, String relevanceField,   
				String keyField, String valueField) throws PersistenceException {
			try {
				rs.next(); //TODO: return what iof this fails??
				int id = DatabaseUtil.asInt(rs.getObject(conceptField));
				String n = nameField == null ? null : DatabaseUtil.asString(rs.getObject(nameField));
				int c = cardinalityField == null ? 1 : DatabaseUtil.asInt(rs.getObject(cardinalityField));
				double r = relevanceField == null ? 1 : DatabaseUtil.asDouble(rs.getObject(relevanceField));
				rs.previous();

				LabeledVector<Integer> v = readVector(rs, conceptField, keyField, valueField, ConceptFeatures.newIntFeaturVector(), maxConceptFeatures, NormalizationMode.AUTO);
				
				T ref = conceptFactory.newInstance(id, n, null, c, r);
				return new ConceptFeatures<T, Integer>(ref, v);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		protected  <K> LabeledVector<K> readVector(ResultSet rs, String conceptField, String keyField, String valueField, LabeledVector<K> v, int limit, NormalizationMode normalize) throws SQLException {
			if (v==null) v = new MapLabeledVector<K>();
			
			int concept = -1;
			int count = 0;
			
			while (rs.next()) {
				K k = (K)rs.getObject(keyField);
				
				if (conceptField!=null) {
					Object c = rs.getObject(conceptField);
					if (concept<0) concept = DatabaseUtil.asInt(c);
					else if (concept!=DatabaseUtil.asInt(c)) {
						if (!rs.previous()) throw new RuntimeException ("push-back failed on result set! "+rs.getClass()); //push back
						break;
					}
				}
				
				count++;
				
				if (limit>0 && count>limit) 
					continue;
				
				Object n = rs.getObject(valueField);
				double d = DatabaseUtil.asDouble(n);
				v.set(k,  d);
			}
			
			if (normalize==NormalizationMode.ALWAYS || (normalize==NormalizationMode.AUTO && limit>0 && v.size()==limit)) { 
				double length = v.getLength();
				v = v.scaled(length);
			}

			return v;
		}

		public DataSet<ConceptFeatures<T, Integer>> getNeighbourhoodFeatures(final int concept) throws PersistenceException {
			/*
			String sql = "SELECT C.id as concept, C.name as name, X.feature as feature, X.normal_weight as value ";
			sql += " FROM " + featureTable.getSQLName() + " as X force key (PRIMARY) ";
			sql += " JOIN "+featureTable.getSQLName()+" as N force key (PRIMARY) ON N.feature = X.concept ";
			sql += " JOIN "+featureTable.getSQLName()+" as F force key (feature) ON F.feature = N.concept ";
			sql += " JOIN "+conceptTable.getSQLName()+" as C force key (PRIMARY) ON C.id = X.concept ";
			sql += " WHERE F.concept = "+concept;
			sql += " ORDER BY X.concept";
			
			return new QueryDataSet<WikiWordConceptFeatures>(database, getConceptFeaturesFactory(), "getNeighbourhoodFeatures", sql, false);
			*/
			return new TemporaryTableDataSet<ConceptFeatures<T, Integer>>(database, getConceptFeaturesFactory()) {
			
				@Override
				public Cursor<ConceptFeatures<T, Integer>> cursor() throws PersistenceException {
					try {
						String n = database.createTemporaryTable("id integer not null, name varchar(255) default null, primary key (id)");
						String sql = "INSERT IGNORE INTO "+n+" (id) ";
						sql += " SELECT N.feature as id ";
						sql += " FROM "+featureTable.getSQLName()+" AS F ";
						sql += " JOIN "+featureTable.getSQLName()+" AS N ON F.feature = N.concept ";
						sql += " WHERE F.concept = "+concept;
						
						database.executeUpdate("getNeighbourhoodFeatures#neighbours", sql);
						
						sql = "UPDATE "+n+" as N ";
						sql+= " JOIN "+conceptTable.getSQLName()+" AS C ON C.id = N.id ";
						sql+= " SET N.name = C.name ";
							
						database.executeUpdate("getNeighbourhoodFeatures#names", sql);

						sql = "SELECT N.id as concept, N.name as name, X.feature as feature, X.normal_weight as value ";
						sql+= " FROM "+n+" AS N ";
						sql+= " JOIN "+featureTable.getSQLName()+" as X ON X.concept = N.id ";
							
						ResultSet rs = database.executeQuery("getNeighbourhoodFeatures#features", sql);
						return new TemporaryTableDataSet.Cursor<ConceptFeatures<T, Integer>>(rs, factory, database, new String[] { n }, database.getLogOutput() );
					} catch (SQLException e) {
						throw new PersistenceException(e);
					}
				}
			};
		}

		private DatabaseDataSet.Factory<ConceptFeatures<T, Integer>> getConceptFeaturesFactory() {
			return conceptFeaturesFactory;
		}

		public DataSet<? extends T> getNeighbours(int concept) throws PersistenceException {
			String sql = "SELECT DISTINCT C.id as cId, C.name as cName ";
			sql += " FROM " + conceptTable.getSQLName() + " as C ";
			sql += " JOIN "+featureTable.getSQLName()+" as N ON N.feature = C.id ";
			sql += " JOIN "+featureTable.getSQLName()+" as F ON F.feature = N.concept ";
			sql += " WHERE F.concept = "+concept+" ";
			
			return new QueryDataSet<T>(database, conceptStore.getRowConceptFactory(), "getNeighbours", sql, false);
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