package de.brightbyte.wikiword.store;

import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.Map;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.RelationTable;
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

				return readConceptProperties("getConceptProperties", sql, "concept", null, null, null, "property", "value"); //FIXME: name, card, relevance
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

				return readConceptsProperties("getConceptsProperties", sql, "concept", null, null, null, "property", "value"); //FIXME: name, card, relevance
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
					while (!rs.isAfterLast()) { // JDBC sais isAfterLast() returns false for empty sets. WTF??
						ConceptProperties<T> f = readConceptProperties(rs, conceptField, nameField, cardinalityField, relevanceField, keyField, valueField);
						if ( f == null ) break; //result set was empty.
						
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
				if ( !rs.next() ) return  null;
				
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
				String p = asString(rs.getObject(propertyField));
				
				if (conceptField!=null) {
					Object c = rs.getObject(conceptField);
					if (concept<0) concept = DatabaseUtil.asInt(c);
					else if (concept!=DatabaseUtil.asInt(c)) {
						if (!rs.previous()) throw new RuntimeException ("push-back failed on result set! "+rs.getClass()); //push back
						break;
					}
				}
				
				count++;
				
				String n = asString(rs.getObject(valueField));
				v.put(p,  n);
			}
			
			return v;
		}

		private DatabaseDataSet.Factory<ConceptProperties<T>> getConceptPropertiesFactory() {
			return conceptPropertiesFactory;
		}
}