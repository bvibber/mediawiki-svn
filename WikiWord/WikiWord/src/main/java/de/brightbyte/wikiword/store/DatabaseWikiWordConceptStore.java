package de.brightbyte.wikiword.store;

import static de.brightbyte.db.DatabaseUtil.asDouble;
import static de.brightbyte.db.DatabaseUtil.asInt;
import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Collection;
import java.util.HashMap;
import java.util.Map;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.ChunkedQueryDataSet;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.PropertyStoreSchema;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;

public abstract class DatabaseWikiWordConceptStore<T extends WikiWordConcept> 
		extends DatabaseWikiWordStore 
		implements WikiWordConceptStore<T>  {


	private class RowConceptFactory implements DatabaseDataSet.Factory<T> {
		private ConceptQuerySpec spec;

		public RowConceptFactory(ConceptQuerySpec spec) {
			super();
			this.spec = spec;
		}

		public T newInstance(ResultSet row) throws SQLException, PersistenceException {
			return newConcept(row, spec);
		}
	}
	
	private class ConceptFactory implements WikiWordConcept.Factory<T> {

		public T[] newArray(int size) {
			return newConceptArray(size);
		}

		public T newInstance(int id, String name, ConceptType type) throws PersistenceException {
			return this.newInstance(id, name, type, 1, 1);
		}

		public T newInstance(int id, String name, ConceptType type, int card, double rel) throws PersistenceException {
			return newConcept(id, name, type, card, rel);
		}
	}
	
	private RowConceptFactory rowConceptFactory = new RowConceptFactory(null);
	private ConceptFactory conceptFactory = new ConceptFactory();
	
	protected EntityTable conceptTable;
	protected RelationTable broaderTable;
	protected RelationTable langlinkTable;
	
	private int numberOfConcepts = -1;

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database represented by the DatabaseSchema.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param db empty DatabaseSchema, wrapping a database connection. Will be configured with the appropriate table defitions
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 * @throws SQLException 
	 */
	public DatabaseWikiWordConceptStore(WikiWordConceptStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);
		
		conceptTable = (EntityTable)database.getTable("concept");
		broaderTable = (RelationTable)database.getTable("broader");
		langlinkTable = (RelationTable)database.getTable("langlink");
	}
	
	protected String getTypeCodeSet( Collection<ConceptType> types) throws SQLException {
		if (types==null || types.isEmpty()) return null;
	
		StringBuilder s = new StringBuilder();
		s.append("(");
		
		boolean first = true;
		for (ConceptType v: types) {
			if (first) first = false;
			else s.append(", ");
				
			s.append( database.encodeValue(v.getCode()) );
		}
		
		s.append(")");
		return s.toString();
	}

	protected DatabaseDataSet.Factory<T> getRowConceptFactory() {
		return rowConceptFactory;
	}
	
	protected WikiWordConcept.Factory<T> getConceptFactory() {
		return conceptFactory;
	}
	
	protected T newConcept(ResultSet row, ConceptQuerySpec spec) throws SQLException, PersistenceException {
		int id = row.getInt("cId");
		String name = asString(row.getObject("cName"));
		int type = row.getInt("cType");
		int card = row.getInt("qCard");
		double relev = row.getDouble("qRelev");
	
		return newConcept(id, name, getConceptType(type), card, relev);
	}
	
	protected T newConcept(Map m, ConceptQuerySpec spec) throws PersistenceException {
		int id = asInt(m.get("cId"));
		String name = asString(m.get("cName"));
		int type = asInt(m.get("cType"));
		int card = m.get("qCard") != null ? asInt(m.get("qCard")) : -1;
		double relev = m.get("qRelev") != null ? asDouble(m.get("qRelev")) : -1;
		
		return newConcept(id, name, getConceptType(type), card, relev);
	}
	
	protected abstract T newConcept(int id, String name, ConceptType type, int card, double relevance) throws PersistenceException;
	protected abstract T[] newConceptArray(int n) ;
		
	protected String conceptSelect(ConceptQuerySpec spec, String card) {
		if (areStatsComplete()) return conceptSelect(spec, card, "DT.idf");
		else return conceptSelect(spec, card, null);
	}
	
	protected String conceptSelect(ConceptQuerySpec spec, String card, String relev) {
		boolean useDistrib = (relev!=null || (spec!=null && spec.getIncludeStatistics())) && areStatsComplete();
		
		if (card!=null) card = ", "+card;
		
		String sql = "SELECT C.id as cId, C.name as cName, C.type as cType, " +
			" "+card+" as qCard, "+relev+" as qRelev " +
			" FROM "+conceptTable.getSQLName()+" as C ";
		
		if (useDistrib) sql += " LEFT JOIN "+database.getSQLTableName("degree", true)+" as DT ON DT.concept = C.id ";
		return sql;
	}
	
	private Boolean statsComplete;
	
	public boolean areStatsComplete() {
		try {
			if (statsComplete==null) statsComplete = getStatisticsStore().isComplete();
			return statsComplete;
		} catch (PersistenceException ex) {
			database.warn("failed to determin areStatsComplete", ex);
			return false;
		}
	}
	
	public DataSet<? extends T> getConcepts(int[] ids, ConceptQuerySpec spec) throws PersistenceException {
		return getConceptInfoStore().getConcepts(ids, spec);
	}
	
	public T getConcept(int id, ConceptQuerySpec spec) throws PersistenceException {
		return getConceptInfoStore().getConcept(id, spec);
	}
	
	public T getRandomConcept(int top, ConceptQuerySpec spec) throws PersistenceException {
		T r = getStatisticsStore().pickRandomConcept(top, spec);
		return getConcept(r.getId(), spec);
	}
	
	public DataSet<T> getAllConcepts(ConceptQuerySpec spec) throws PersistenceException { 
		try {
			String sql = conceptSelect(spec, "-1");
			return new ChunkedQueryDataSet<T>(database, getRowConceptFactory(), "getAllConcepts", "query",  sql, null, null, conceptTable, "id", queryChunkSize);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public int getNumberOfConcepts() throws PersistenceException {
		if (numberOfConcepts<0) {
			String sql = "select count(*) from "+conceptTable.getSQLName();
			try {
				numberOfConcepts = asInt(database.executeSingleValueQuery("getNumberOfConcepts", sql));
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		return numberOfConcepts;
	}
	
	public boolean isGlobal() {
		return !(getDatasetIdentifier() instanceof Corpus);
	}
	
	/*
	public static class ConceptQuerySpec {
		public final String lang;
		public final String table;
		public final String cardField;
		public final String relevField;
		public final String idField;
		public final String nameField;
		public final String join;
		public final String where;
		
		public ConceptQuerySpec(
				final String lang,
				final String table, 
				final String idField, final String nameField,
				final String cardField, final String relevField, 
				final String join,
				final String where) {

			this.lang = lang;
			this.table = table;
			this.idField = DatabaseSchema.qualifyName("T", idField);
			this.nameField = DatabaseSchema.qualifyName("T", nameField);
			this.cardField = DatabaseSchema.qualifyName("T", cardField);
			this.relevField = DatabaseSchema.qualifyName("T", relevField);
			this.join = join;
			this.where = where;
		}
	}
	
	protected String getConceptReferenceQuerySQL(ConceptQuerySpec q) {
		String by; 
		
		if (q.relevField!=null) by = " ORDER BY "+q.relevField+" DESC"; 
		else if (q.cardField!=null) by = " ORDER BY "+q.cardField+" DESC"; 
		else if (q.nameField!=null) by = " ORDER BY "+q.nameField+" ASC"; 
		else if (q.idField!=null) by = " ORDER BY "+q.idField+" ASC"; 
		else by = "";
		
		String sql = "SELECT " + 
		(q.cardField==null?"-1":q.cardField) + " as cardinality, " + 
		(q.relevField==null?"-1":q.relevField) + " as relevance" + 
		(q.idField==null?"":", "+q.idField + " as id") + 
		(q.nameField==null?"":", "+q.nameField + " as name") + 
		" FROM "+database.getSQLTableName(q.table, false)+" as T " +
		( q.join == null ? "" : q.join ) + 
		(q.where==null ? "" : " WHERE " + q.where) +
		by;
		
		return sql;
	}
	*/
	//----------------------------------------------------------------------------------

	protected abstract DatabaseStatisticsStore newStatisticsStore() throws SQLException, PersistenceException;
	protected abstract DatabaseConceptInfoStore<T> newConceptInfoStore() throws SQLException, PersistenceException;

	protected ProximityStore<T, Integer> newProximityStore() throws SQLException, PersistenceException {
		ProximityStoreSchema schema = new ProximityStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), null, isGlobal(), tweaks, false); 
		
		if (tweaks.getTweak("proximity.usedStoredProximity", true) && schema.tableExists("proximity")) {
			int c = schema.getTableSize("proximity");
			if (c>0) { //proximity values are pre-calculated in the database
				return new DatabaseProximityStore<T>(this, schema, tweaks);
			}
		} 

		//proximity values are not in the database, calculate on the fly
		DatabaseFeatureStore<T> store = new DatabaseFeatureStore<T>(this, schema, tweaks);
		return new CalculatedProximityStore<T>(store, getConceptFactory());
	}
	
	private DatabaseStatisticsStore statsStore;
	private DatabaseConceptInfoStore<T> infoStore;

	private ProximityStore<T, Integer> proximityStore;
	private PropertyStore<T> propertyStore;
	
	public DatabaseConceptInfoStore<T> getConceptInfoStore() throws PersistenceException {
		try { 
			if (infoStore==null) infoStore = newConceptInfoStore();
			return infoStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}

	public StatisticsStore<T> getStatisticsStore() throws PersistenceException {
		try {
			if (statsStore==null) statsStore = newStatisticsStore();
			return statsStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}	

	public FeatureStore<T, Integer> getFeatureStore() throws PersistenceException {
		return getProximityStore();
	}
	
	public ProximityStore<T, Integer> getProximityStore() throws PersistenceException {
		try { 
			if (proximityStore==null) proximityStore = newProximityStore();
			return proximityStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}	
	
	public PropertyStore<T> getPropertyStore() throws PersistenceException {
		try { 
			if (propertyStore==null) propertyStore = newPropertyStore();
			return propertyStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}	
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected abstract PropertyStore<T> newPropertyStore() throws SQLException, PersistenceException;

	public abstract class DatabaseStatisticsStore extends DatabaseWikiWordStore implements StatisticsStore<T>  {
		
		protected EntityTable statsTable;
		protected EntityTable degreeTable;
		
		protected DatabaseStatisticsStore(StatisticsStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
			statsTable =  (EntityTable)database.getTable("stats");
			degreeTable = (EntityTable)database.getTable("degree");
		}	

		public void dumpStatistics(Output out) throws PersistenceException {
			dumpStats(getStatistics(), out);
		}

		public Map<String, ? extends Number> getStatistics() throws PersistenceException {
			try {
				String sql = "SELECT block, name, value FROM "+statsTable.getSQLName();
				ResultSet rs = database.executeQuery("getStatistics", sql);
		
				Map<String, Number> stats = new HashMap<String, Number>();
				
				try {
					while (rs.next()) {
						String block = asString(rs.getObject("block"));
						String name = asString(rs.getObject("name"));
						Double value = rs.getDouble("value");
						stats.put(block+": "+name, value);
					}
				}
				finally {
					rs.close();
				}
				
				return stats;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		protected ConceptType getConceptType(int type) throws PersistenceException {
			try {
				return DatabaseWikiWordConceptStore.this.database.getConceptType(type);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public T pickRandomConcept(int top, ConceptQuerySpec spec)
			throws PersistenceException {
			
			if (!areStatsComplete()) throw new IllegalStateException("satistics need to be built before accessing node degree!");
			
			if (top==0) top = getNumberOfConcepts(); //if 0, use all
			if (top<0) top = getNumberOfConcepts() * top / -100; //if negative, interpret as percent
			
			int r = (int)Math.floor(Math.random() * top) +1;
				
			String sql = conceptSelect(spec, "DT.in_degree", "DT.idf")
						+ " WHERE in_rank = "+r;
			
			try {
				T pick = null;
				ResultSet rs = executeQuery("pickRandomConcept", sql);
				if (rs.next()) pick = newConcept(rs, spec); 
				rs.close();
				return pick;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public abstract class DatabaseConceptInfoStore<C extends WikiWordConcept> 
		extends DatabaseWikiWordStore {


		protected class ConceptFactory implements DatabaseDataSet.Factory<C> {
			private ConceptQuerySpec spec;

			public ConceptFactory(ConceptQuerySpec spec) {
				super();
				this.spec = spec;
			}

			public C newInstance(ResultSet row) throws SQLException, PersistenceException {
				Map<String, Object> m = DatabaseSchema.rowMap(row);
				
				return newConcept(m, spec);
			}
		}
		
		protected WikiWordConceptStoreSchema conceptDatabase;
		
		protected EntityTable conceptInfoTable;

		protected DatabaseConceptInfoStore(ConceptInfoStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
			conceptInfoTable = (EntityTable)database.getTable("concept_info");
		}	
		
		protected String conceptSelect(ConceptQuerySpec spec, String card) {
			if (areStatsComplete()) return conceptSelect(spec, card, "DT.idf");
			else return conceptSelect(spec, card, "-1");
		}

		protected abstract String conceptSelect(ConceptQuerySpec spec, String card, String relev);	
		
		public C getConcept(int id, ConceptQuerySpec spec)
			throws PersistenceException {
		
			String sql = conceptSelect(spec, "-1") + " WHERE C.id = "+id;
			
			try {
				ResultSet row = executeQuery("getConcept", sql);
				if (!row.next()) throw new PersistenceException("no concept found with id = "+id);
					
				Map<String, Object> data = DatabaseSchema.rowMap(row); 
				C c = newConcept(data, spec);
				
				return c;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public DataSet<C> getAllConcepts(ConceptQuerySpec spec)
			throws PersistenceException {
		
			try {
				String sql = conceptSelect(spec, "-1");
				return new ChunkedQueryDataSet<C>(database, new ConceptFactory(spec), "getAllConcepts", "query",  sql, null, null, conceptTable, "id", queryChunkSize);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public DataSet<C> getConcepts(int[] ids, ConceptQuerySpec spec)
			throws PersistenceException {
		
			try {
				String sql = conceptSelect(spec, "-1");
				sql += " WHERE C.id IN " + database.encodeSet(ids);
				return new QueryDataSet<C>(database, new ConceptFactory(spec), "getConcepts", sql, false);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	
		protected abstract C newConcept(Map<String, Object> data, ConceptQuerySpec spec) throws PersistenceException;
		
	}

	public int getQueryChunkSize() {
		return queryChunkSize;
	}

}
