package de.brightbyte.wikiword.store.builder;

import static de.brightbyte.db.DatabaseUtil.asInt;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

import de.brightbyte.application.Agenda;
import de.brightbyte.data.IntList;
import de.brightbyte.data.IntRelation;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.LogLevels;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.Processor;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;
import de.brightbyte.wikiword.store.WikiWordConceptStore;

public abstract class DatabaseWikiWordConceptStoreBuilder<T extends WikiWordConcept> extends DatabaseWikiWordStoreBuilder implements WikiWordConceptStoreBuilder<T>  {

	protected Inserter conceptInserter;
	protected Inserter broaderInserter;
	protected Inserter linkInserter;
	protected Inserter langlinkInserter;

	protected EntityTable conceptTable;
	protected RelationTable broaderTable;
	protected RelationTable linkTable;
	protected RelationTable langlinkTable;
	protected RelationTable relationTable;	 

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
	public DatabaseWikiWordConceptStoreBuilder(WikiWordConceptStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
		super(database, tweaks, agenda);
		
		conceptInserter =  configureTable("concept", 256, 32);
		broaderInserter =  configureTable("broader", 1024, 64);
		linkInserter =     configureTable("link", 8*1024, 64);
		langlinkInserter = configureTable("langlink", 2*1024, 64);
		
		langlinkInserter.setLenient(true);
	
		conceptTable = (EntityTable)conceptInserter.getTable();
		broaderTable = (RelationTable)broaderInserter.getTable();
		linkTable =    (RelationTable)linkInserter.getTable();
		langlinkTable = (RelationTable)langlinkInserter.getTable();

		Inserter relationInserter = configureTable("relation", 16, 4*1024);
		relationTable = (RelationTable)relationInserter.getTable();
	}	
	
	protected ConceptType getConceptType(int type) throws PersistenceException {
		try {
			return database.getConceptType(type);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected int deleteConceptBroader(int narrow, int broad) throws SQLException {
		String sql = "DELETE FROM "+broaderTable.getSQLName()+" " +
				" WHERE narrow = "+narrow+" " +
				" AND broad = "+broad;
		
		return database.executeUpdate("deleteConceptBroader", sql);
	}
		
	protected int deleteConceptBroader(String narrow, String broad) throws SQLException {
		String sql = "DELETE FROM "+broaderTable.getSQLName()+" " +
				" WHERE narrow_name = "+database.quoteString(narrow)+" " +
				" AND broad_name = "+database.quoteString(broad);
		
		return database.executeUpdate("deleteConceptBroader", sql);
	}
		
	protected static class IntColumn implements DatabaseDataSet.Factory<Integer> {
		private int column;
		
		public IntColumn(int column) {
			this.column = column;
		}

		public Integer newInstance(ResultSet row) throws Exception {
			return row.getInt(column);
		}
	}
	
	protected DataSet<Integer> listHierarchyRoots(DatabaseTable hierarchy, String narrow, String broad) throws PersistenceException {
		String sql = "SELECT B." + broad + " FROM " + hierarchy.getSQLName() + " AS A " +
				" RIGHT JOIN " + hierarchy.getSQLName() + " AS B " +
				" ON A." + narrow + " = B." + broad + 
				" WHERE A." + narrow + " is null " +
				" GROUP BY B." + broad + "";
		
		return new QueryDataSet<Integer>(database, new IntColumn(1), "queryHierarchyRoots", sql, false);
	}
	
	protected int bindHierarchyRoots(DatabaseTable hierarchy, String narrow, String broad, int id) throws PersistenceException {
		String sql = "INSERT INTO " + hierarchy.getSQLName() + " ( narrow, broad ) " + 
				" SELECT "+id+" as broad,  B." + broad + " as narrow "+
				" FROM " + hierarchy.getSQLName() + " AS A " +
				" RIGHT JOIN " + hierarchy.getSQLName() + " AS B " +
				" ON A." + narrow + " = B." + broad + 
				" WHERE A." + narrow + " is null " +
				" GROUP BY B." + broad + "";
		
		return executeUpdate("bindHierarchyRoots", sql);
	}
	
	public int getCategoryCount() throws PersistenceException {
		return getInnerNodeCount(broaderTable, "broad"); 
	}
	
	protected int getInnerNodeCount(DatabaseTable hierarchy, String broad) throws PersistenceException {
		String sql = "select count(*) from (select 1 from " + hierarchy.getSQLName() + " group by " + broad + ") as x";
		
		try {
			return asInt(database.executeSingleValueQuery("getHierarchySize", sql));
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected IntRelation loadHierarchy(DatabaseTable hierarchy, String narrow, String broad) throws PersistenceException {
		String sql;
		
		sql = "select "+broad+", "+narrow+" " +
				"from " + hierarchy.getSQLName()+" ";

		String rest = "order by "+broad;
		
		final IntList blist = new IntList();
		final IntList nlist = new IntList();
		
		Processor<ResultSet> processor = new Processor<ResultSet>() {
			public void process(ResultSet rs) throws Exception {
				while (rs.next()) {
					int b = rs.getInt(1);
					int n = rs.getInt(2);
					
					blist.add(b);
					nlist.add(n);
				}
			}
		};
				
		DatabaseAccess.SimpleChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(getDatabaseAccess(), "loadHierarchy", "query", sql, null, rest, hierarchy, broad);
		executeChunkedQuery(query, 4, processor);
			
		return new IntRelation(blist, nlist); 
	}
	
	public int getCategorizationCount() throws PersistenceException {
		try {
			return database.getTableSize("broader");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/*
	protected DataSet<Integer> listHierarchyChildren(DatabaseTable hierarchy, String narrow, String broad, int root, int after, int limit) throws PersistenceException {
		String sql = "SELECT " + narrow + " FROM " + hierarchy.getSQLName() + " AS A " +
			" WHERE A." + broad + " = " + root +
			" AND EXISTS ( SELECT * FROM " + hierarchy.getSQLName() +" AS B WHERE B." + broad + " = A." + narrow + ")";
		
		if (after >= 0) sql += " AND "+narrow+" > "+after;
		
		sql += " ORDER BY "+narrow;
		
		if (limit>0) sql += " LIMIT "+limit;

		return new QueryDataSet<Integer>(database, "queryHierarchyRoots", sql, false) {
			@Override
			protected Integer newInstance(ResultSet row) throws SQLException, PersistenceException {
				return row.getInt(1);
			}
		};
	}
	*/

	/*
	protected PreparedStatementDataSet<Integer> prepareHierarchyChildrenQuery(DatabaseTable hierarchy, String narrow, String broad) throws PersistenceException {
		String sql = "SELECT " + narrow + " FROM " + hierarchy.getSQLName() + " AS A " +
			" WHERE A." + broad + " = ? "+
			" AND EXISTS ( SELECT * FROM " + hierarchy.getSQLName() +" AS B " +
						 " WHERE B." + broad + " = A." + narrow + ")" +
			" AND "+narrow+" > ? " +
			" ORDER BY "+narrow+" " +
			" LIMIT ?";
		
		try {
			return new PreparedStatementDataSet<Integer>(database, "queryHierarchyRoots", sql, false) {
				@Override
				protected Integer newInstance(ResultSet row) throws SQLException, PersistenceException {
					return row.getInt(1);
				}
			};
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	*/
	
	/*
	public long catwalk(int root, List<Integer> stop, int level, boolean breakCycles, IntRelation hierarchy) throws PersistenceException {
		try {
			if (level>200) { //FIXME: configure max level!
				throw new IllegalArgumentException("nesting too deep ("+level+" levels): "+stop);
			}

			if (hierarchy==null) {
				hierarchy = loadHierarchy(broaderTable, "narrow", "broad", false);
				log("loaded "+hierarchy.size()+" hierarchy links (without leafs)");
			}
			
			if (stop==null) {
				stop = new ArrayList<Integer>(100);
			}
			else {
				int idx = stop.indexOf(root); 
				if (root>=0 && idx>=0) {
					warning(-1, "cycle in category hierarchy", "at concept "+root+" via "+stop.subList(idx, stop.size()), null);
					
					int parent = stop.get(stop.size()-1);
					hierarchy.remove(parent, root);

					if (breakCycles) {
						deleteConceptBroader(root, parent);
						return 1;
					}
					else {
						return 0;
					}
				}
			}
			
			
			//for (int i=0; i<level; i++) System.out.print('-');
			//System.out.println("- "+root);

			int chunkSize = root>=0 ? 1000 : -1; //no limit when fetching roots //FIXME: configure chunk size!
			int after = -1;
			
			long c = breakCycles ? 0 : 1;

			int[] nodes;
			
			if (root<0) {
				DataSet<Integer> d = listHierarchyRoots(broaderTable, "narrow", "broad");
				List<Integer> n = d.load();
				nodes = new int[n.size()];
				int i= 0;
				
				for (int id: n) {
					nodes[i++] = id;
				}
				
				log("found "+nodes.length+" hierarchy roots");
			}
			else {
				nodes = hierarchy.get(root);
			}
			
			if (nodes==null || nodes.length==0) return c;

			if (nodes.length>1000) {
				warning(-1, "very large category", "concept "+root+" has "+nodes.length+" children", null);				
			}
			
			stop.add(root);

			for (int n: nodes) {
				if (n==root) {
					//NOTE: shouldn't happen, should have been resolved by deleteLoops
					warning(-1, "loop in category hierarchy", "at concept "+root, null);

					int parent = stop.get(stop.size()-1);
					hierarchy.remove(parent, root);

					if (breakCycles) {
						deleteConceptBroader(root, parent);
						c++;
					}
					
					continue; //skip loops 
				}
				
				c+= catwalk(n, stop, level+1, breakCycles, hierarchy);
				after = n;
			}
			
			stop.remove(stop.size()-1);

			return c;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}*/
	
	public long deleteBroaderCycles() throws PersistenceException {
		IntRelation hierarchy = loadHierarchy(broaderTable, "narrow", "broad");

		CycleFinder cf = new CycleFinder(hierarchy, true) {
			@Override
			protected void onCycle(List<Integer> path, int id, int backlinkIndex) throws PersistenceException {
				try {
					warning("cycle in category graph", "at "+id+" via "+path.subList(backlinkIndex, path.size()));
					
					int parent = path.get(path.size()-1);
					deleteConceptBroader(id, parent); //narrow, broad
					super.onCycle(path, id, backlinkIndex);
				} catch (SQLException e) {
					throw new PersistenceException(e);
				}
			}
			
			@Override
			protected void warning(String problem, String details) {
				super.warning(problem, details);
				try {
					storeWarning(-1, problem, details);
				} catch (PersistenceException e) {
					database.error("failed to store warning!", e);
				}
			}

			@Override
			protected void error(String problem, String details) {
				super.error(problem, details);
				try {
					storeWarning(-1, problem, details);
				} catch (PersistenceException e) {
					database.error("failed to store warning!", e);
				}
			}
			
		};

		cf.setLevelWarningThreshold(tweaks.getTweak("dbstore.CycleFinder.levelWarningThreshold", 32));
		cf.setDegreeWarningThreshold(tweaks.getTweak("dbstore.CycleFinder.degreeWarningThreshold", 1024));
		cf.setMaxDepth(tweaks.getTweak("dbstore.CycleFinder.maxDepth", 1024));
		
		if (database.getLogLevel() < LogLevels.LOG_INFO) {
			cf.setOut(database.getLogOutput());
		}
		
		cf.findCycles();
		return cf.getCycleCount();
	}
	

	protected int buildLangMatch() throws PersistenceException {
		final String sql = "INSERT IGNORE INTO "+relationTable.getSQLName()+" (concept1, concept2, langmatch)" +
				" SELECT LL.concept, LR.concept, 1 " +
				" FROM "+langlinkTable.getSQLName()+" as LL force index(concept_language_target) " +
				" JOIN "+langlinkTable.getSQLName()+" as LR force index(language_target) ON LR.language = LL.language AND LR.target = LL.target ";
		
		final String where = " LL.concept != LR.concept ";
		final String rest = " ON DUPLICATE KEY UPDATE langmatch = langmatch + VALUES(langmatch) ";
		
		DatabaseSchema.ChunkedQuery query = new DatabaseSchema.AbstractChunkedQuery(database, "buildLangMatch", "insert:langmatch", langlinkTable, "concept") {
			
			@Override
			protected String getSQL(long first, long end) {
				String q = sql
							+ " WHERE "
				            + where
							+ " AND ( LL.concept >= "+first+" AND LL.concept < "+end+" ) "
							+ rest;
				
				return q;
			}
		
		};

		return executeChunkedUpdate(query, -5); 
	}
	
	protected int buildBiLink() throws PersistenceException {
		String sql = "insert ignore into "+relationTable.getSQLName()+" (concept1, concept2, bilink)" +
		" select A.anchor, A.target, 1 from "+linkTable.getSQLName()+" as A " + 
		" join "+linkTable.getSQLName()+" as B " + 
		" force index (target_anchor) " + //NOTE: avoid table scan!
		" on A.anchor = B.target AND B.anchor = A.target ";
		String suffix = " on duplicate key update bilink = bilink + values(bilink)"; 

		return executeChunkedUpdate("finishGlobalConcepts", "similarities:bilink", sql, suffix, linkTable, "A.anchor", 1);
	}

	
	@Override
	public void flush() throws PersistenceException{
		super.flush();
		
		if (statsStore!=null)
			statsStore.flush();
		
		if (infoStore!=null)
			infoStore.flush();
	}
	
	//----------------------------------------------------------------------------------

	protected abstract DatabaseStatisticsStoreBuilder newStatisticsStoreBuilder() throws SQLException, PersistenceException;
	protected abstract DatabaseConceptInfoStoreBuilder<T> newConceptInfoStoreBuilder() throws SQLException, PersistenceException;
	protected abstract WikiWordConceptStore<T, ? extends WikiWordConceptReference<T>> newConceptStore() throws SQLException, PersistenceException;
	
	private DatabaseStatisticsStoreBuilder statsStore;
	private ProximityStoreBuilder proximityStore;
	private DatabaseConceptInfoStoreBuilder<T> infoStore;
	private WikiWordConceptStore<T, ? extends WikiWordConceptReference<T>> conceptStore;
	
	public WikiWordConceptStore<T, ? extends WikiWordConceptReference<T>> getConceptStore() throws PersistenceException {
		try { 
			if (conceptStore==null) conceptStore = newConceptStore();
			return conceptStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}

	public ConceptInfoStoreBuilder<T> getConceptInfoStoreBuilder() throws PersistenceException {
		try { 
			if (infoStore==null) infoStore = newConceptInfoStoreBuilder();
			return infoStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}

	public ProximityStoreBuilder getProximityStoreBuilder() throws PersistenceException {
		try { 
			if (proximityStore==null) proximityStore = newProximityStoreBuilder();
			return proximityStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}

	public StatisticsStoreBuilder getStatisticsStoreBuilder() throws PersistenceException {
		try { 
			if (statsStore==null) statsStore = newStatisticsStoreBuilder();
			return statsStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}	
	
	private Boolean statsComplete;
	
	public boolean areStatsComplete() {
		try {
			if (statsComplete==null) statsComplete = getStatisticsStoreBuilder().isComplete();
			return statsComplete;
		} catch (PersistenceException ex) {
			database.warn("failed to determin areStatsComplete", ex);
			return false;
		}
	}

	protected abstract DatabaseProximityStoreBuilder newProximityStoreBuilder() throws SQLException;
	
}
