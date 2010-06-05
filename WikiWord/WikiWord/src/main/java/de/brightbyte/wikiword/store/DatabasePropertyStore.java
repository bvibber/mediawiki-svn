package de.brightbyte.wikiword.store;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
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
import de.brightbyte.wikiword.model.ConceptProperties;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.schema.PropertyStoreSchema;
import de.brightbyte.wikiword.store.DatabaseFeatureStore.NormalizationMode;

public class DatabasePropertyStore<T extends WikiWordConcept> 
				extends DatabaseWikiWordStore
				implements PropertyStore<T> {
	
		protected WikiWordConcept.Factory<T> conceptFactory;
		protected DatabaseWikiWordConceptStore<T> conceptStore;
		protected RelationTable propertyTable;
		protected EntityTable conceptTable;
		
		private DatabaseDataSet.Factory<ConceptProperties<T>> conceptPropertiesFactory = new DatabaseDataSet.Factory<ConceptProperties<T>>() {
		
			public ConceptProperties<T> newInstance(ResultSet row) throws Exception {
				int concept = -1;
				String name = null;
				ValueSetMultiMap<String, String> properties = new ValueSetMultiMap<String, String>(); 
				
				do {
					int c = row.getInt("concept");
					if (concept < 0) {
						concept = c;
						name = row.getString("concept_name");
					}
					else if (concept != c) break;
					
					String property = row.getString("property");
					String value = row.getString("value");
					
					properties.put(property, value);
				} while (row.next());
				
				if (concept<0) return null;
				
				T r = newConcept(concept, name, null, 1, -1); //TODO: global vs. local
				return new ConceptProperties<T>(r, properties);
			}
		
		};
			
		protected DatabasePropertyStore(DatabaseWikiWordConceptStore<T> conceptStore, PropertyStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
		    this.conceptStore = conceptStore;
			this.conceptFactory = conceptStore.getConceptFactory();

		    this.conceptTable = (EntityTable)conceptStore.getDatabaseAccess().getTable("concept"); 
			this.propertyTable = (RelationTable)database.getTable("property");
		}

		protected T newConcept(int id, String name, ConceptType type, int cardinality, double relevance) throws PersistenceException {
			return conceptFactory.newInstance(id, name, type, cardinality, relevance);
		}

		public ConceptProperties<T> getConceptProperties(int concept) throws PersistenceException {
			return getConceptProperties(concept, null);
		}
		
		public ConceptProperties<T> getConceptProperties(int concept, Collection<String> properties) throws PersistenceException {
			try {
				String sql = "SELECT concept, concept_name, property, value FROM " +propertyTable.getSQLName()+" as F ";
				sql += " WHERE concept = "+concept;
				if ( properties != null ) sql += " AND property IN " + database.encodeSet(properties) + " ";

				return readConceptProperties("getConceptProperties", sql, "concept", null, null, null, "property", "normal_weight"); //FIXME: name, card, relevance
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public Map<Integer, ConceptProperties<T>> getConceptsProperties(int[] concepts) throws PersistenceException {
			return getConceptsProperties(concepts, null);
		}
		
		public Map<Integer, ConceptProperties<T>> getConceptsProperties(int[] concepts, Collection<String> properties) throws PersistenceException {
			if (concepts.length==0) return Collections.emptyMap();
			
			try {
				String sql = "SELECT concept, concept_name, property, value FROM " +propertyTable.getSQLName()+" as F ";
				sql += " WHERE concept IN "+database.encodeSet(concepts);
				if ( properties != null ) sql += " AND property IN " + database.encodeSet(properties) + " ";

				return readConceptsProperties("getConceptsProperties", sql, "concept", null, null, null, "property", "normal_weight"); //FIXME: name, card, relevance
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  MultiMap<String, String, ? extends Collection<String>> readMultiMap(String name, String sql, String keyField, String valueField, NormalizationMode normalize) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				try {
					MultiMap<String, String, ? extends Collection<String>> v = readMultiMap(rs, null, keyField, valueField, new ValueSetMultiMap<String, String>(), normalize);
					return v;
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  ConceptProperties<T> readConceptProperties(String name, String sql, 
				String conceptField, String nameField, String cardinalityField, String relevanceField,   
				String keyField, String valueField) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				try {
					return readConceptProperties(rs, conceptField, nameField, cardinalityField, relevanceField, keyField, valueField);
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected  Map<Integer, ConceptProperties<T>> readConceptsProperties(String name, String sql, 
				String conceptField, String nameField, String cardinalityField, String relevanceField,   
				String keyField, String valueField) throws PersistenceException {
			try {
				ResultSet rs = database.executeQuery(name, sql);
				Map<Integer, ConceptProperties<T>> propertys = new HashMap<Integer, ConceptProperties<T>>();
				
				try {
					while (!rs.isAfterLast()) {
						ConceptProperties<T> f = readConceptProperties(rs, conceptField, nameField, cardinalityField, relevanceField, keyField, valueField);
						propertys.put(f.getId(), f);
					}
					
					return propertys;
				} finally {
					rs.close();
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public ConceptProperties<T> readConceptProperties(ResultSet rs, 
				String conceptField, String nameField, String cardinalityField, String relevanceField,   
				String keyField, String valueField) throws PersistenceException {
			try {
				rs.next(); //TODO: return what iof this fails??
				int id = DatabaseUtil.asInt(rs.getObject(conceptField));
				String n = nameField == null ? null : DatabaseUtil.asString(rs.getObject(nameField));
				int c = cardinalityField == null ? 1 : DatabaseUtil.asInt(rs.getObject(cardinalityField));
				double r = relevanceField == null ? 1 : DatabaseUtil.asDouble(rs.getObject(relevanceField));
				rs.previous();

				MultiMap<String, String, ? extends Collection<String>> props = readMultiMap(rs, conceptField, keyField, valueField, new ValueSetMultiMap<String, String>(), NormalizationMode.AUTO);
				
				T ref = conceptFactory.newInstance(id, n, null, c, r); //XXX: type??
				return new ConceptProperties<T>(ref, props);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		protected  MultiMap<String, String, ? extends Collection<String>> readMultiMap(ResultSet rs, String conceptField, String propertyField, String valueField, MultiMap<String, String, ? extends Collection<String>> v, NormalizationMode normalize) throws SQLException {
			if (v==null) v = new ValueSetMultiMap<String, String>();
			
			int concept = -1;
			int count = 0;
			
			while (rs.next()) {
				String p = rs.getString(propertyField);
				
				if (conceptField!=null) {
					Object c = rs.getObject(conceptField);
					if (concept<0) concept = DatabaseUtil.asInt(c);
					else if (concept!=DatabaseUtil.asInt(c)) {
						if (!rs.previous()) throw new RuntimeException ("push-back failed on result set! "+rs.getClass()); //push back
						break;
					}
				}
				
				count++;
				
				String n = rs.getString(valueField);
				v.put(p,  n);
			}
			
			return v;
		}

		public DataSet<ConceptProperties<T>> getNeighbourhoodProperties(final int concept) throws PersistenceException {
			/*
			String sql = "SELECT C.id as concept, C.name as name, X.property as property, X.normal_weight as value ";
			sql += " FROM " + propertyTable.getSQLName() + " as X force key (PRIMARY) ";
			sql += " JOIN "+propertyTable.getSQLName()+" as N force key (PRIMARY) ON N.property = X.concept ";
			sql += " JOIN "+propertyTable.getSQLName()+" as F force key (property) ON F.property = N.concept ";
			sql += " JOIN "+conceptTable.getSQLName()+" as C force key (PRIMARY) ON C.id = X.concept ";
			sql += " WHERE F.concept = "+concept;
			sql += " ORDER BY X.concept";
			
			return new QueryDataSet<WikiWordConceptProperties>(database, getConceptPropertiesFactory(), "getNeighbourhoodProperties", sql, false);
			*/
			return new TemporaryTableDataSet<ConceptProperties<T>>(database, getConceptPropertiesFactory()) {
			
				@Override
				public Cursor<ConceptProperties<T>> cursor() throws PersistenceException {
					try {
						String n = database.createTemporaryTable("id integer not null, name varchar(255) default null, primary key (id)");
						String sql = "INSERT IGNORE INTO "+n+" (id) ";
						sql += " SELECT N.property as id ";
						sql += " FROM "+propertyTable.getSQLName()+" AS F ";
						sql += " JOIN "+propertyTable.getSQLName()+" AS N ON F.property = N.concept ";
						sql += " WHERE F.concept = "+concept;
						
						database.executeUpdate("getNeighbourhoodProperties#neighbours", sql);
						
						sql = "UPDATE "+n+" as N ";
						sql+= " JOIN "+conceptTable.getSQLName()+" AS C ON C.id = N.id ";
						sql+= " SET N.name = C.name ";
							
						database.executeUpdate("getNeighbourhoodProperties#names", sql);

						sql = "SELECT N.id as concept, N.name as name, X.property as property, X.normal_weight as value ";
						sql+= " FROM "+n+" AS N ";
						sql+= " JOIN "+propertyTable.getSQLName()+" as X ON X.concept = N.id ";
							
						ResultSet rs = database.executeQuery("getNeighbourhoodProperties#propertys", sql);
						return new TemporaryTableDataSet.Cursor<ConceptProperties<T>>(rs, factory, database, new String[] { n }, database.getLogOutput() );
					} catch (SQLException e) {
						throw new PersistenceException(e);
					}
				}
			};
		}

		private DatabaseDataSet.Factory<ConceptProperties<T>> getConceptPropertiesFactory() {
			return conceptPropertiesFactory;
		}

		public DataSet<? extends T> getNeighbours(int concept) throws PersistenceException {
			String sql = "SELECT DISTINCT C.id as cId, C.name as cName ";
			sql += " FROM " + conceptTable.getSQLName() + " as C ";
			sql += " JOIN "+propertyTable.getSQLName()+" as N ON N.property = C.id ";
			sql += " JOIN "+propertyTable.getSQLName()+" as F ON F.property = N.concept ";
			sql += " WHERE F.concept = "+concept+" ";
			
			return new QueryDataSet<T>(database, conceptStore.getRowConceptFactory(), "getNeighbours", sql, false);
		}

		public List<Integer> getNeighbourList(int concept) throws PersistenceException {
			String sql = "SELECT DISTINCT N.property as concept ";
			sql += " FROM "+propertyTable.getSQLName()+" as N ";
			sql += " JOIN "+propertyTable.getSQLName()+" as F ON F.property = N.concept ";
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