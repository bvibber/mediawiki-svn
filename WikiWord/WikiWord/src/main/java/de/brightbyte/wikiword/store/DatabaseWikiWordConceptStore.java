package de.brightbyte.wikiword.store;

import static de.brightbyte.db.DatabaseUtil.asDouble;
import static de.brightbyte.db.DatabaseUtil.asInt;
import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
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
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;

public abstract class DatabaseWikiWordConceptStore<T extends WikiWordConcept, R extends WikiWordConceptReference<T>> 
		extends DatabaseWikiWordStore 
		implements WikiWordConceptStore<T, R>  {


	protected class ReferenceFactory implements DatabaseDataSet.Factory<R> {
		public R newInstance(ResultSet row) throws SQLException, PersistenceException {
			return newReference(row);
		}
	}
	
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
	
	protected R newReference(ResultSet row) throws SQLException {
		int id = row.getInt("cId");
		String name = asString(row.getObject("cName"));
		int card = row.getInt("qCard");
		double relev = row.getInt("qRelev");
	
		return newReference(id, name, card, relev);
	}
	
	protected R newReference(Map m) {
		int id = asInt(m.get("cId"));
		String name = asString(m.get("cName"));
		int card = m.get("qCard") != null ? asInt(m.get("qCard")) : -1;
		double relev = m.get("qRelev") != null ? asDouble(m.get("qRelev")) : -1;
		
		return newReference(id, name, card, relev);
	}
	
	protected abstract R newReference(int id, String name, int card, double relevance);
		
	protected String referenceSelect(String card) {
		if (areStatsComplete()) return referenceSelect(card, "DT.idf", true);
		else return referenceSelect(card, "-1", false);
	}
	
	protected String referenceSelect(String card, String relev, boolean useDistrib) {
		if (card!=null) card = ", "+card;
		
		String sql = "SELECT C.id as cId, C.name as cName, C.type as cType " +
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
	
	public DataSet<? extends T> getConcepts(int[] ids) throws PersistenceException {
		return getConceptInfoStore().getConcepts(ids);
	}
	
	public DataSet<? extends T> getConcepts(Iterable<R> refs) throws PersistenceException {
		return getConceptInfoStore().getConcepts(refs);
	}
	
	public T getConcept(int id) throws PersistenceException {
		return getConceptInfoStore().getConcept(id);
	}
	
	public T getRandomConcept(int top) throws PersistenceException {
		R r = pickRandomConcept(top);
		return getConcept(r.getId());
	}
	
	public DataSet<R> listAllConcepts() throws PersistenceException { 
		try {
			String sql = referenceSelect("-1");
			return new ChunkedQueryDataSet<R>(database, new ReferenceFactory(), "listAllConcepts", "query",  sql, null, null, conceptTable, "id", queryChunkSize);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public R pickRandomConcept(int top) throws PersistenceException {
		return getStatisticsStore().pickRandomConcept(top);
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
	
	private DatabaseStatisticsStore statsStore;
	private DatabaseConceptInfoStore<T> infoStore;
	
	public DatabaseConceptInfoStore<T> getConceptInfoStore() throws PersistenceException {
		try { //FIXME: read-only!
			if (infoStore==null) infoStore = newConceptInfoStore();
			return infoStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}

	public StatisticsStore<T, R> getStatisticsStore() throws PersistenceException {
		try { //FIXME: read-only!
			if (statsStore==null) statsStore = newStatisticsStore();
			return statsStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}	
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public abstract class DatabaseStatisticsStore extends DatabaseWikiWordStore implements StatisticsStore<T, R>  {
		
		protected EntityTable statsTable;
		protected EntityTable degreeTable;
		
		protected boolean readOnly = false; //FIXME: read-only mode?!

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
		
		public R pickRandomConcept(int top)
			throws PersistenceException {
			
			if (!areStatsComplete()) throw new IllegalStateException("satistics need to be built before accessing node degree!");
			
			if (top==0) top = getNumberOfConcepts(); //if 0, use all
			if (top<0) top = getNumberOfConcepts() * top / -100; //if negative, interpret as percent
			
			int r = (int)Math.floor(Math.random() * top) +1;
				
			String sql = referenceSelect("DT.in_degree", "DT.idf", true)
						+ " WHERE in_rank = "+r;
			
			try {
				R pick = null;
				ResultSet rs = executeQuery("pickRandomConcept", sql);
				if (rs.next()) pick = newReference(rs); 
				rs.close();
				return pick;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public abstract class DatabaseConceptInfoStore<C extends WikiWordConcept> 
		extends DatabaseWikiWordStore
		implements ConceptInfoStore<C> {


		protected class ConceptFactory implements DatabaseDataSet.Factory<C> {
			public C newInstance(ResultSet row) throws SQLException, PersistenceException {
				Map<String, Object> m = DatabaseSchema.rowMap(row);
				
				return newConcept(m);
			}
		}
		
		protected WikiWordConceptStoreSchema conceptDatabase;
		
		protected EntityTable conceptInfoTable;

		protected DatabaseConceptInfoStore(ConceptInfoStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
			conceptInfoTable = (EntityTable)database.getTable("concept_info");
		}	
		
		protected String conceptSelect(String card) {
			if (areStatsComplete()) return conceptSelect(card, "DT.idf", true);
			else return conceptSelect(card, "-1", false);
		}

		protected abstract String conceptSelect(String card, String relev, boolean useDistrib);	
		
		public C getConcept(int id)
			throws PersistenceException {
		
			String sql = conceptSelect("-1") + " WHERE C.id = "+id;
			
			try {
				ResultSet row = executeQuery("getConcept", sql);
				if (!row.next()) throw new PersistenceException("no concept found with id = "+id);
					
				Map<String, Object> data = DatabaseSchema.rowMap(row); 
				C c = newConcept(data);
				
				return c;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public DataSet<C> getAllConcepts()
			throws PersistenceException {
		
			try {
				String sql = conceptSelect("-1");
				return new ChunkedQueryDataSet<C>(database, new ConceptFactory(), "getAllConcepts", "query",  sql, null, null, conceptTable, "id", queryChunkSize);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public DataSet<C> getConcepts(int[] ids)
			throws PersistenceException {
		
			try {
				String sql = conceptSelect("-1");
				sql += " WHERE C.id IN " + database.encodeSet(ids);
				return new QueryDataSet<C>(database, new ConceptFactory(), "getConcepts", sql, false);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	
		protected abstract C newConcept(Map<String, Object> data) throws PersistenceException;

		public DataSet<C> getConcepts(Iterable<? extends WikiWordConceptReference<? extends WikiWordConcept>> refs) throws PersistenceException {
			List<Integer> ids = new ArrayList<Integer>();
			
			for (WikiWordConceptReference<? extends WikiWordConcept> r: refs) {
				ids.add(r.getId());
			}
			
			int[] ii = new int[ids.size()];
			int i = 0;
			for (int id: ids) ii[i++] = id;
			
			return getConcepts(ii);
		}
		
	}

	public int getQueryChunkSize() {
		return queryChunkSize;
	}

}
