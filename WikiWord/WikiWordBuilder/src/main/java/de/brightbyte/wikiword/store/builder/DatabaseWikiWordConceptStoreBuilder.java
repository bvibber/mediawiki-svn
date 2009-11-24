package de.brightbyte.wikiword.store.builder;

import static de.brightbyte.db.DatabaseUtil.asInt;
import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.Blob;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

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
import de.brightbyte.wikiword.disambig.ConceptFeatures;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema.ReferenceListEntrySpec;
import de.brightbyte.wikiword.store.GroupNameTranslator;
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
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected abstract class DatabaseStatisticsStoreBuilder extends DatabaseWikiWordStoreBuilder implements StatisticsStoreBuilder {
		
		protected EntityTable statsTable;
		protected EntityTable degreeTable;
		
		protected DatabaseStatisticsStoreBuilder(StatisticsStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
			//XXX: wen don't need inserters, really...
			Inserter statsInserter = configureTable("stats", 64, 1024);
			Inserter degreeInserter = configureTable("degree", 64, 1024);
			
			statsTable =  (EntityTable)statsInserter.getTable();
			degreeTable = (EntityTable)degreeInserter.getTable();
		}	

		protected int getNumberOfConcepts() throws PersistenceException {
			String sql = "select count(*) from "+conceptTable.getSQLName();
			try {
				return asInt(database.executeSingleValueQuery("getNumberOfConcepts", sql));
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public void buildStatistics() throws PersistenceException {		
			if (beginTask("buildStatistics", "stats.prepareDegreeTable")) {
				int n = prepareDegreeTable();
				endTask("buildStatistics", "stats.prepareDegreeTable", n+" entries");
			}
			
			if (beginTask("buildStatistics", "stats.buildDegreeInfo#in")) {
				int n = buildDegreeInfo("anchor", "target", "in");
				endTask("buildStatistics", "stats.buildDegreeInfo#in", n+" entries");
			}
			
			if (beginTask("buildStatistics", "stats.buildDegreeInfo#out")) {
				int n = buildDegreeInfo("target", "anchor", "out");
				endTask("buildStatistics", "stats.buildDegreeInfo#out", n+" entries");
			}
			
			if (beginTask("buildStatistics", "stats.combineDegreeInfo")) {
				int n = combineDegreeInfo("in", "out", "link");
				endTask("buildStatistics", "stats.combineDegreeInfo", n+" entries");
			}

			if (beginTask("buildStatistics", "stats.inDegreeDistribution")) {
				buildDistributionStats("in-degree distr", degreeTable, "in_rank", "in_degree");
				endTask("buildStatistics", "stats.inDegreeDistribution");
			}
			
			if (beginTask("buildStatistics", "stats.outDegreeDistribution")) {
				buildDistributionStats("out-degree distr", degreeTable, "out_rank", "out_degree");
				endTask("buildStatistics", "stats.outDegreeDistribution");
			}
			
			if (beginTask("buildStatistics", "stats.linkDegreeDistribution")) {
				buildDistributionStats("link-degree distr", degreeTable, "link_rank", "link_degree");
				endTask("buildStatistics", "stats.linkDegreeDistribution");
			}
			
			if (beginTask("buildStatistics", "stats.idf")) {
				
				//idf = inverse document frequency
				//      see Salton, G. and McGill, M. J. 1983 Introduction to modern information retrieval

				int numberOfConcepts = getNumberOfConcepts();
				String idf = "LOG("+numberOfConcepts+" / in_degree)";

				int n = buildDistributionCoefficient("idf", degreeTable, "idf", idf, "in_degree > 0");
				endTask("buildStatistics", "stats.idf", n + " entries");
				
				if (beginTask("buildStatistics", "stats.idfRank")) {
					buildRank(degreeTable, "idf", "idf_rank");
					endTask("buildStatistics", "stats.idfRank");
				}
			}

			if (beginTask("buildStatistics", "stats.lhs")) {
				
				//lhs = local hierarchy score
				//      as defined by Muchnik et.al. 2007 in Physical Review E 76, 016106
				//      Note that the symmetrical counterpart has been omitted.

				String lhs = "in_degree * SQRT(in_degree) / (in_degree + out_degree)";
				
				int n = buildDistributionCoefficient("lhs", degreeTable, "lhs", lhs, "in_degree > 0");
				endTask("buildStatistics", "stats.lhs", n + " entries");
				
				if (beginTask("buildStatistics", "stats.lhsRank")) {
					buildRank(degreeTable, "lhs", "lhs_rank");
					endTask("buildStatistics", "stats.lhsRank");
				}
			}
			
			if (beginTask("buildStatistics", "stats.table")) {
				storeStatsEntries( "table", DatabaseWikiWordConceptStoreBuilder.this.getTableStats() );
				endTask("buildStatistics", "stats.table");
			}
		}

		protected int buildDistributionCoefficient(String name, DatabaseTable table, String coefField, String coefFormula, String coefCond) throws PersistenceException {
			String sql = "UPDATE "+table.getSQLName()+" SET "+coefField+" = "+coefFormula;
			
			return executeChunkedUpdate("buildDistributionCoefficient", name, sql, coefCond, table, "concept");
		}
		
		protected void buildDistributionStats(String name, DatabaseTable table, String rankField, String valueField) throws PersistenceException {
			try {
				//log("building distribution statistics \""+name+"\" from "+table.getName()+"."+rankField+" x "+table.getName()+"."+valueField);
				String sql = "SELECT count(*) as t, sum("+valueField+") as N FROM "+table.getSQLName();
				Map<String, Object> m = database.executeSingleRowQuery("buildZipfStats", sql);
				
				double t = ((Number)m.get("t")).doubleValue(); //number of types (different terms)
				
				if (t==0) {
					warning(-1, "no distribution stats", "no types found for "+name, null);
					return;
				}
				
				double N = ((Number)m.get("N")).doubleValue(); //number of tokens (term occurrances)
				
				if (N==0) {
					warning(-1, "no distribution stats", "no tokens found for "+name, null);
					return;
				}
				
				sql = "SELECT avg(rank*value) as k, stddev(rank*value/"+N+") as kd " +
						"FROM (SELECT MAX("+rankField+") as rank, "+valueField+" as value " +
								"FROM "+table.getSQLName()+" " +
								"GROUP BY "+valueField+") as G";
				
				m = database.executeSingleRowQuery("buildDistributionStats", sql);
				
				double k = ((Number)m.get("k")).doubleValue();   //average of maxrank * freq
				double kd = ((Number)m.get("kd")).doubleValue(); //deviation of maxrank * freq
				double c = k / N; 								 //characteristic fitting constant (normalized k) 
				
				Map<String, Number> stats = new HashMap<String, Number>(10);
				stats.put("total distinct types", t);
				stats.put("total token occurrences", N);
				stats.put("average type value", N/t);
				stats.put("average type value x rank", k);
				stats.put("characteristic fitting", c); //c = k / N
				stats.put("deviation from char. fit.", kd);
				
				storeStatsEntries(name, stats);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		protected void storeStatsEntry(String block, String name, double value) throws PersistenceException {
			try {
				//TODO: inserter?...
				String sql = "REPLACE INTO "+statsTable.getSQLName()+" (block, name, value) VALUES (" 
						+database.encodeValue(block)+", " 
						+database.encodeValue(name)+", " 
						+database.encodeValue(value)+") ";
				
				executeUpdate("storeStatsEntry", sql);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		protected void storeStatsEntries(String block, ResultSet rs, GroupNameTranslator translator) throws PersistenceException {
			try {
				StringBuilder sql = new StringBuilder();
				sql.append( "REPLACE INTO " );
				sql.append( statsTable.getSQLName() );
				sql.append( " (block, name, value) VALUES" );
				
				boolean first = true;
				while (rs.next()) {
					if (first) first = false;
					else sql.append(", ");
						
					String name = asString(rs.getObject("name"));
					double value = rs.getDouble("value");
					
					if (translator!=null) name = translator.translate(name);
					
					sql.append( "(" ); 
					sql.append(database.encodeValue(block));
					sql.append(", "); 
					sql.append(database.encodeValue(name));
					sql.append(", "); 
					sql.append(database.encodeValue(value));
					sql.append( ")" ); 
				}
				
				executeUpdate("storeStatsEntries", sql.toString());
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		protected void storeStatsEntries(String block, Map<String, ? extends Number> stats) throws PersistenceException {
			StringBuilder sql = new StringBuilder();
			sql.append( "REPLACE INTO " );
			sql.append( statsTable.getSQLName() );
			sql.append( " (block, name, value) VALUES" );
			
			boolean first = true;
			for (Map.Entry<String, ? extends Number> e: stats.entrySet()) {
				if (first) first = false;
				else sql.append(", ");
					
				String name = e.getKey();
				double value = e.getValue().doubleValue();
				
				try {
					sql.append( "(" ); 
					sql.append(database.encodeValue(block));
					sql.append(", "); 
					sql.append(database.encodeValue(name));
					sql.append(", "); 
					sql.append(database.encodeValue(value));
					sql.append( ")" );
				} catch (SQLException e1) {
					throw new PersistenceException(e1);
				} 
			}
			
			executeUpdate("storeStatsEntries", sql.toString());
		}
		
		protected int prepareDegreeTable() throws PersistenceException {
			DatabaseTable t = DatabaseWikiWordConceptStoreBuilder.this.database.getTable("concept");
			
			String sql = "INSERT ignore INTO "+degreeTable.getSQLName()+" ( concept, concept_name ) "
				+" SELECT id, name "
				+" FROM "+t.getSQLName();

			return executeChunkedUpdate("prepareDegreeTable", "prepareDegreeTable", sql, null, t, "id");
		}
		
		protected int buildDegreeInfo(String linkField, String groupField, String statsField) throws PersistenceException {
			DatabaseTable t = DatabaseWikiWordConceptStoreBuilder.this.database.getTable("link");
			
			String sql = "UPDATE "+degreeTable.getSQLName()+" AS D "
				+" JOIN ( SELECT "+groupField+" as concept, count("+linkField+") as degree " 
						+" FROM "+t.getSQLName()+" " 
						+" WHERE anchor IS NOT NULL " 
						+" GROUP BY "+groupField+") AS X "
				+" ON X.concept = D.concept"
				+" SET "+statsField+"_degree = X.degree";

			//System.out.println("*** "+sql+" ***");
			int n =  executeChunkedUpdate("buildDegreeInfo", linkField+","+groupField+","+statsField, sql, null, degreeTable, "D.concept"); 
			
			//TODO: solve tangle!
			return buildRank(degreeTable, statsField+"_degree", statsField+"_rank");
		}
		
		protected int buildRank(DatabaseTable table, String valueField, String rankField) throws PersistenceException {
			log("building ranks in "+table.getName()+"."+rankField+" based on "+table.getName()+"."+valueField);
			
			executeUpdate("buildDegreeInfo#init", "set @num = 0;");
			
			String sql = "UPDATE "+degreeTable.getSQLName()
				+" SET "+rankField+" = (@num := @num + 1)" 
				+" ORDER BY "+valueField+" DESC ";

			//System.out.println("*** "+sql+" ***");
			
			long t = System.currentTimeMillis();
			int n = executeUpdate("buildRank", sql); //XXX: chunk? if yes, how? need to set @num first!
		
			log("built ranks info in "+table.getName()+"."+rankField+" based on "+table.getName()+"."+valueField+" on "+n+" rows "+(System.currentTimeMillis()-t)/1000+" sec");
			return n;
		}
		
		protected int combineDegreeInfo(String firstField, String secondField, String sumField) throws PersistenceException {
			String sql = "UPDATE "+degreeTable.getSQLName()
				+" SET "+sumField+"_degree = "+firstField+"_degree + "+secondField+"_degree";

			//System.out.println("*** "+sql+" ***");
			int n = executeChunkedUpdate("buildDegreeInfo", firstField+","+secondField+","+sumField, sql, null, degreeTable, "concept");
			
			//TODO: solve tangle!
			return buildRank(degreeTable, sumField+"_degree", sumField+"_rank");
		}
		
		protected ConceptType getConceptType(int type) throws PersistenceException {
			try {
				return DatabaseWikiWordConceptStoreBuilder.this.database.getConceptType(type);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public void clear() throws PersistenceException {
			try {
				database.truncateTable(statsTable.getName(), true);
				database.truncateTable(degreeTable.getName(), true);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	protected abstract class DatabaseConceptInfoStoreBuilder<T extends WikiWordConcept> 
		extends DatabaseWikiWordStoreBuilder 
		implements ConceptInfoStoreBuilder<T> {

		protected WikiWordConceptStoreSchema conceptDatabase;
		
		protected EntityTable conceptInfoTable;
		protected EntityTable conceptFeaturesTable;
		protected Inserter conceptFeaturesInserter;

		protected DatabaseConceptInfoStoreBuilder(ConceptInfoStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(database, tweaks, agenda);
			
			Inserter conceptInfoInserter = configureTable("concept_info", 64, 1024);
			conceptInfoTable = (EntityTable)conceptInfoInserter.getTable();
			
			conceptFeaturesInserter = configureTable("concept_features", 64, 1024);
			conceptFeaturesInserter.setLenient(true); //ignore dupes. //TODO: replace instead!
			conceptFeaturesTable = (EntityTable)conceptFeaturesInserter.getTable();
		}	

		public void buildConceptInfo() throws PersistenceException {
			if (!areStatsComplete()) throw new IllegalStateException("statistics need to be built before concept infos!");
			
			if (beginTask("buildConceptInfo", "prepareConceptCache:concept_info")) {
				int n = prepareConceptCache(conceptInfoTable, "concept");
				endTask("buildConceptInfo", "prepareConceptCache:concept_info", n+" entries");
			}
			
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,langlinks")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "langlinks", "langlink", "concept", ((ConceptInfoStoreSchema)database).langlinkReferenceListEntry, false, null, 1);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,langlinks", n+" entries");
			}

			
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,inlinks")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "inlinks", "link", "target", ((ConceptInfoStoreSchema)database).inLinksReferenceListEntry, false, null, 5);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,inlinks", n+" entries");
			}
			
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,outlinks")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "outlinks", "link", "anchor", ((ConceptInfoStoreSchema)database).outLinksReferenceListEntry, false, null, 5);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,outlinks", n+" entries");
			}
			
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,narrower")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "narrower", "broader", "broad", ((ConceptInfoStoreSchema)database).narrowerReferenceListEntry, false, null, 2);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,narrower", n+" entries");
			}
			
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,broader")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "broader", "broader", "narrow", ((ConceptInfoStoreSchema)database).broaderReferenceListEntry, false, null, 2);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,broader", n+" entries");
			}

			//XXX: different similarities / thresholds!
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,similar")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "similar", "relation", "concept1", ((ConceptInfoStoreSchema)database).similarReferenceListEntry, false, "langmatch > 0", 1);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,similar", n+" entries");
			}

			//XXX: different similarities / thresholds!
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,related#1")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "related", "relation", "concept1", ((ConceptInfoStoreSchema)database).relatedReferenceListEntry, false, "bilink > 0", 2);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,related#1", n+" entries");
			}

			//XXX: different similarities / thresholds!
			if (beginTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,related#2")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "related", "relation", "concept2", ((ConceptInfoStoreSchema)database).related2ReferenceListEntry, false, "bilink > 0", 2);
				endTask("buildConceptInfo", "buildConceptPropertyCache:concept_info,related#2", n+" entries");
			}
			
		}
		
		protected int prepareConceptCache(DatabaseTable cacheTable, String conceptIdField) throws PersistenceException {
			DatabaseTable t = DatabaseWikiWordConceptStoreBuilder.this.database.getTable("concept");
			
			String sql = "INSERT ignore INTO "+cacheTable.getSQLName()+" ( " + conceptIdField + " ) "
				+" SELECT id "
				+" FROM "+t.getSQLName();

			return executeChunkedUpdate("prepareConceptCache", cacheTable.getName()+"."+conceptIdField, sql, null, t, "id");
		}
		
		public int buildConceptPropertyCache(String targetField, String propertyTable, String propertyConceptField,
				ReferenceListEntrySpec spec, String threshold) throws PersistenceException {
			return buildConceptPropertyCache(conceptInfoTable, "concept", targetField, propertyTable, propertyConceptField, spec, false, threshold, 1);
		}
		
		protected int buildConceptPropertyCache(
				final DatabaseTable cacheTable, final String cacheIdField, 
				final String propertyField, final String realtion, final String relConceptField, 
				final ReferenceListEntrySpec spec, final boolean append, final String threshold,
				final int chunkFactor) throws PersistenceException {
			
			final DatabaseTable relationTable = DatabaseWikiWordConceptStoreBuilder.this.database.getTable(realtion);
			
			//XXX: if no frequency-field, evtl use inner grouping by nameField to determin frequency! (expensive, though)

			//XXX: for outlinks, langlinks, etc, we could exclude UNKN OWN concepts...
			
			final String v = !append ? "s" : "if ("+propertyField+" IS NOT NULL, concat("+propertyField+", '"+((ConceptInfoStoreSchema)database).referenceSeparator+"', s), s)";
			
			final String joinDistrib = !spec.useRelevance ? "" : " JOIN "+database.getSQLTableName("degree", true)+" as DT ON DT.concept = "+spec.joinField;
			final String andThreashold = threshold==null ? "" : " AND ("+threshold+")";

			DatabaseSchema.ChunkedQuery query = new DatabaseSchema.AbstractChunkedQuery(database, "buildConceptPropertyCache", cacheTable.getName()+"."+propertyField, cacheTable, "C."+cacheIdField) {
				
				@Override
				protected String getSQL(long first, long end) {
					String sql = "UPDATE "+cacheTable.getSQLName()+" AS C "
								+" JOIN ( SELECT "+relConceptField+", group_concat("+spec.valueExpression+" separator '"+((ConceptInfoStoreSchema)database).referenceSeparator+"' ) as s" 
								+"        FROM "+relationTable.getSQLName()
										+joinDistrib
										+" WHERE ( "+relConceptField+" >= "+first+" AND "+relConceptField+" < "+end+" )"
										+andThreashold
										+" group by "+relConceptField+" )" 
								+" AS R "
								+" ON R."+relConceptField+" = C."+cacheIdField
								+" SET "+propertyField+" = " + v
								+" WHERE ( C."+cacheIdField+" >= "+first+" AND C."+cacheIdField+" < "+end+" )";
					
					return sql;
				}
			
			};

			return executeChunkedUpdate(query, chunkFactor);
		}

		/**
		 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeRawText(int, java.lang.String)
		 */
		public void  storeConceptFeatures(ConceptFeatures<T> features) throws PersistenceException {
			try {
				if (conceptFeaturesInserter==null) conceptFeaturesInserter = conceptFeaturesTable.getInserter();
				
				conceptFeaturesInserter.updateInt("concept", features.getConceptId());
				conceptFeaturesInserter.updateBlob("features", features.getFeatureVectorData());
				conceptFeaturesInserter.updateRow();
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	}
}
