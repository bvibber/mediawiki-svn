/**
 * 
 */
package de.brightbyte.wikiword.store.builder;

import static de.brightbyte.db.DatabaseUtil.asInt;
import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.Map;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;
import de.brightbyte.wikiword.store.GroupNameTranslator;

abstract class DatabaseStatisticsStoreBuilder extends DatabaseWikiWordStoreBuilder implements StatisticsStoreBuilder {
	
	protected EntityTable statsTable;
	protected EntityTable degreeTable;
	
	protected DatabaseWikiWordConceptStoreBuilder conceptStore;
	
	protected DatabaseStatisticsStoreBuilder(DatabaseWikiWordConceptStoreBuilder conceptStore, StatisticsStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
		super(database, tweaks, agenda);
		
		this.conceptStore = conceptStore;
		
		//XXX: wen don't need inserters, really...
		Inserter statsInserter = configureTable("stats", 64, 1024);
		Inserter degreeInserter = configureTable("degree", 64, 1024);
		
		statsTable =  (EntityTable)statsInserter.getTable();
		degreeTable = (EntityTable)degreeInserter.getTable();
	}	

	private int numberOfConcepts = -1;
	
	protected int getNumberOfConcepts() throws PersistenceException {
		if (numberOfConcepts >= 0) return numberOfConcepts; 
		
		String sql = "select count(*) from "+conceptStore.getDatabaseAccess().getTable("concept").getSQLName();
		try {
			numberOfConcepts = asInt(database.executeSingleValueQuery("getNumberOfConcepts", sql));
		    return numberOfConcepts;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * Builds the statistics tables. For a specification of their content, refer to StatisticsStoreSchema
	 */
	public void buildStatistics() throws PersistenceException {		
		if (beginTask("buildStatistics", "stats.prepareDegreeTable")) {
			int n = prepareDegreeTable();
			endTask("buildStatistics", "stats.prepareDegreeTable", n+" entries");
		}
		
		DatabaseTable linkTable = conceptStore.getDatabaseAccess().getTable("link");
		DatabaseTable hierarchyTable = conceptStore.getDatabaseAccess().getTable("broader");
		
		if (beginTask("buildStatistics", "stats.buildDegreeInfo#in")) {
			int n = buildDegreeInfo(linkTable, "anchor", "target", "in", "in_degree", "in_rank",  "in_bias");
			endTask("buildStatistics", "stats.buildDegreeInfo#in", n+" entries");
		}
		
		if (beginTask("buildStatistics", "stats.buildDegreeInfo#out")) {
			int n = buildDegreeInfo(linkTable, "target", "anchor", "out", "out_degree", "out_rank",  "out_bias");
			endTask("buildStatistics", "stats.buildDegreeInfo#out", n+" entries");
		}
		
		if (beginTask("buildStatistics", "stats.buildDegreeInfo#up")) {
			int n = buildDegreeInfo(hierarchyTable, "narrow", "broad", "up", "up_degree", null, "up_bias");
			endTask("buildStatistics", "stats.buildDegreeInfo#up", n+" entries");
		}
		
		if (beginTask("buildStatistics", "stats.buildDegreeInfo#down")) {
			int n = buildDegreeInfo(hierarchyTable, "broad", "narrow", "down", "down_degree", null, "down_bias");
			endTask("buildStatistics", "stats.buildDegreeInfo#down", n+" entries");
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
			int n = buildInverseReferenceFrequency("in_degree", "idf", null);
			endTask("buildStatistics", "stats.idf", n + " entries");
		}
		
		if (beginTask("buildStatistics", "stats.lhs")) {
			int n = buildLocalHierarchyScore("in_degree", "out_degree", "lhs", null);
			endTask("buildStatistics", "stats.lhs", n + " entries");
		}
		
		if (beginTask("buildStatistics", "stats.table")) {
			storeStatsEntries( "table", conceptStore.getTableStats() );
			endTask("buildStatistics", "stats.table");
		}
	}
	
	protected int buildDegreeBias(String degreeField, String biasField) throws PersistenceException {
		//	bias = 1 - ( log(d) / log(D) ) 

		int numberOfConcepts = getNumberOfConcepts();
		String bias = " 1 - ( log("+degreeField+") / "+Math.log(numberOfConcepts)+" )";

		int n = buildDistributionCoefficient(biasField, degreeTable, biasField, bias, degreeField + " > 0");
		return n;
	}
	
	protected int buildInverseReferenceFrequency(String degreeField, String idfField, String rankField) throws PersistenceException {
		//	similar to inverse document frequency
		//  see Salton, G. and McGill, M. J. 1983 Introduction to modern information retrieval
        //  idf = log( D / d ) = log( D ) - log( d )

		int numberOfConcepts = getNumberOfConcepts();
		String idf = Math.log(numberOfConcepts) + " - LOG( "+degreeField+")";   

		int n = buildDistributionCoefficient(idfField, degreeTable, idfField, idf, degreeField + " > 0");
		
		if (rankField!=null && beginTask("buildInverseReferenceFrequency", "stats."+rankField)) {
			buildRank(degreeTable, idfField, rankField);
			endTask("buildInverseReferenceFrequency", "stats."+rankField);
		}
		
		return n;
	}
	
	protected int buildLocalHierarchyScore(String inField, String outField, String lhsField, String rankField) throws PersistenceException {
		// lhs = local hierarchy score
		//      as defined by Muchnik et.al. 2007 in Physical Review E 76, 016106
		//      Note that the symmetrical counterpart has been omitted.
		// lhs = d_in * sqrt( d_in ) / ( d_in + d_out )

		String lhs = inField + " * SQRT("+inField+") / ("+inField+" + "+outField+")";
		
		int n = buildDistributionCoefficient("lhs", degreeTable, lhsField, lhs, ""+inField+" > 0");
		
		if (rankField!=null && beginTask("buildLocalHierarchyScore", "stats."+rankField)) {
			buildRank(degreeTable, lhsField, rankField);
			endTask("buildLocalHierarchyScore", "stats."+rankField);
		}
		
		return n;
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
		DatabaseTable t = conceptStore.getDatabaseAccess().getTable("concept");
		
		String sql = "INSERT ignore INTO "+degreeTable.getSQLName()+" ( concept, concept_name ) "
			+" SELECT id, name "
			+" FROM "+t.getSQLName();

		return executeChunkedUpdate("prepareDegreeTable", "prepareDegreeTable", sql, null, t, "id");
	}
	
	protected int buildDegreeInfo(DatabaseTable t, String linkField, String groupField, String statsField, String degreeField, String rankField, String biasField) throws PersistenceException {
		String sql = "UPDATE "+degreeTable.getSQLName()+" AS D "
			+" JOIN ( SELECT "+groupField+" as concept, count("+linkField+") as degree " 
					+" FROM "+t.getSQLName()+" " 
					+" WHERE "+linkField+" IS NOT NULL AND "+groupField+" IS NOT NULL " 
					+" GROUP BY "+groupField+") AS X "
			+" ON X.concept = D.concept"
			+" SET "+degreeField+" = X.degree";

		//System.out.println("*** "+sql+" ***");
		int n =  executeChunkedUpdate("buildDegreeInfo", linkField+","+groupField+","+statsField, sql, null, degreeTable, "D.concept"); 
		
		if (rankField!=null && beginTask("buildDegreeInfo", "stats."+rankField)) {
			buildRank(degreeTable, degreeField, rankField);
			endTask("buildDegreeInfo", "stats."+rankField);
		}
		
		if (biasField!=null && beginTask("buildDegreeInfo", "stats."+biasField)) {
			buildDegreeBias(degreeField, biasField);
			endTask("buildDegreeInfo", "stats."+biasField);
		}
		
		return n;
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
			return conceptStore.getConceptType(type);
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