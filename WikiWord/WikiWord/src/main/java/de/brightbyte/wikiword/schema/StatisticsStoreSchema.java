package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;

/**
 *  Defines the database schema for storing statistics abotu a wikiword thesaurus. 
 *  
 * <h4>Table <tt>degree</tt></h4>
 * <p>Holds accumulated information about the different relations between concepts.</p>
 * <dl>
 * 		<dt>concept</dt><dd>The ID of the concept this entry refers to. Reference to concept.id. Unique key.</dd>
 * 		<dt>concept_name</dt><dd>The name of the concept this entry refers to. Reference to concept.name. Unique key.</dd>
 * 
 * 		<dt>in_degree</dt><dd>The number of incoming links, i.e. entries in the links table where link.target = degree.concept.</dd>
 * 		<dt>in_rank</dt><dd>Rank with respect to in_degree, i.e. sequence number in a list of all concepts sorted by descending in_degree.</dd>
 * 		<dt>in_bias</dt><dd>Bias with respect to in_degree; the bias is defined as: <tt>1 - ( log(in_degree) / log(number_of_concepts) )</tt>. Will be between 0 and 1. </dd>
 * 
 * 		<dt>out_degree</dt><dd>The number of outgoing links, i.e. entries in the links table where link.anchor = degree.concept.</dd>
 * 		<dt>out_rank</dt><dd>Rank with respect to out_degree, i.e. sequence number in a list of all concepts sorted by descending out_degree.</dd>
 * 		<dt>out_bias</dt><dd>Bias with respect to out_degree; the bias is defined as: <tt>1 - ( log(out_degree) / log(number_of_concepts) )</tt>. Will be between 0 and 1.</dd>
 *
 * 		<dt>link_degree</dt><dd>The number of links, i.e. entries in the links table where link.anchor = degree.concept or link.target = degree.concept. It's the sum of in_degree and out_degree.</dd>
 * 		<dt>link_rank</dt><dd>Rank with respect to link_rank, i.e. sequence number in a list of all concepts sorted by descending link_rank.</dd>
 * 
 * 		<dt>up_degree</dt><dd>The number of parent concepts, i.e. entries in the links table where broader.narrow = degree.concept.</dd>
 * 		<dt>up_bias</dt><dd>Bias with respect to up_degree; the bias is defined as: <tt>1 - ( log(up_degree) / log(number_of_concepts) )</tt>.  Will be between 0 and 1.</dd>
 * 
 * 		<dt>down_degree</dt><dd>The number of child concepts, i.e. entries in the links table where broader.broad = degree.concept.</dd>
 * 		<dt>down_bias</dt><dd>Bias with respect to down_degree; the bias is defined as: <tt>1 - ( log(down_degree) / log(number_of_concepts) )</tt>. Will be between 0 and 1.</dd>
 * 
 * 		<dt>lhs</dt><dd>Local hierarchy score, according to Muchnik et.al. (2007 in Physical Review E 76, 016106), without symmetrical counterpart: <tt>in_degree * sqrt( in_degree ) / ( in_degree + out_degree )</tt></dd>
 * 		<dt>idf</dt><dd>Inverse document (reference) frequency, adapted from Salton and McGill (1983): <tt>log( number_of_concepts / in_degree )</tt> resp. <tt>log( number_of_concepts ) - log( in_degree )</tt></dd>
 * </dl>
 * 
 * <h4>Table <tt>stats</tt></h4>
 * <p>Holds statistical figures relating to the entire thesaurus.</p>
 * <dl>
 * 		<dt>block</dt><dd>Name of the statistics. Forms a unique key together with the name field.</dd>
 * 		<dt>name</dt><dd>Name of the statistical figure. Forms a unique key together with the block field.</dd>
 * 		<dt>value</dt><dd>Value of the given figure.</dd>
 * </dl>
 * 
 * @author daniel
 */
public class StatisticsStoreSchema extends WikiWordStoreSchema {
	
	protected EntityTable degreeTable;
	protected EntityTable statsTable;


	public StatisticsStoreSchema(DatasetIdentifier dataset, Connection connection, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue );
		init(global, tweaks);
	}

	public StatisticsStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, tweaks, useFlushQueue);
		init(global, tweaks);
	}
	
	private void init(boolean global, TweakSet tweaks) {
		degreeTable = new EntityTable(this, "degree", getDefaultTableAttributes());
		degreeTable.addField( new ReferenceField(this, "concept", "INT", null, true, KeyType.PRIMARY, "concept", "id", null ));
		degreeTable.addField( new ReferenceField(this, "concept_name", getTextType(global ? 32*255+32*16 : 255), null, true, global ? null : KeyType.UNIQUE, "concept", "name", null ) ); 
		degreeTable.addField( new DatabaseField(this, "in_rank", "INT", null, false, KeyType.INDEX ) );
		degreeTable.addField( new DatabaseField(this, "in_degree", "INT", "DEFAULT 0", false, KeyType.INDEX ) );
		degreeTable.addField( new DatabaseField(this, "in_bias", "REAL", "DEFAULT 1", false, null ) ); 
		degreeTable.addField( new DatabaseField(this, "out_rank", "INT", null, false, KeyType.INDEX ) );
		degreeTable.addField( new DatabaseField(this, "out_degree", "INT", "DEFAULT 0", false, KeyType.INDEX ) );
		degreeTable.addField( new DatabaseField(this, "out_bias", "REAL", "DEFAULT 1", false, null ) );
		degreeTable.addField( new DatabaseField(this, "up_degree", "INT", "DEFAULT 0", false, KeyType.INDEX ) );
		degreeTable.addField( new DatabaseField(this, "up_bias", "REAL", "DEFAULT 1", false, null ) );
		degreeTable.addField( new DatabaseField(this, "down_degree", "INT", "DEFAULT 0", false, KeyType.INDEX ) );
		degreeTable.addField( new DatabaseField(this, "down_bias", "REAL", "DEFAULT 1", false, null ) );
		degreeTable.addField( new DatabaseField(this, "link_rank", "INT", null, false, KeyType.INDEX ) );
		degreeTable.addField( new DatabaseField(this, "link_degree", "INT", "DEFAULT 0", false, KeyType.INDEX ) );
		//degreeTable.addField( new DatabaseField(this, "link_bias", "REAL", "DEFAULT 1", false, null ) );
		degreeTable.addField( new DatabaseField(this, "lhs", "REAL", null, false, KeyType.INDEX ) ); //local hierarchy score
		degreeTable.addField( new DatabaseField(this, "idf", "REAL", null, false, KeyType.INDEX ) );  //inverse document frequency
		//degreeTable.addField( new DatabaseField(this, "lhs_rank", "INT", null, true, KeyType.INDEX ) );
		//degreeTable.addField( new DatabaseField(this, "idf_rank", "INT", null, true, KeyType.INDEX ) );
		degreeTable.setAutomaticField(null);
		addTable(degreeTable);

		statsTable = new EntityTable(this, "stats", getDefaultTableAttributes());
		statsTable.addField( new DatabaseField(this, "block", getTextType(32), null, true, null ) );
		statsTable.addField( new DatabaseField(this, "name", getTextType(64), null, true, null ) );
		statsTable.addField( new DatabaseField(this, "value", "REAL(16,4)", null, true, null ) );
		statsTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "block_name", new String[] {"block", "name"}) );
		statsTable.setAutomaticField(null);
		addTable(statsTable);
	}

	@Override
	public void checkConsistency() throws SQLException {
		checkReferentialIntegrity(degreeTable, "concept", false);
		checkReferencePairConsistency(degreeTable, "concept", "concept_name");
	}
	
	@Override
	public boolean isComplete() throws SQLException {
		if (!super.isComplete()) return false;
		if (!this.tableExists("stats")) return false;
		
		/*
		if (!this.tableExists("term")) return false;
		if (!this.tableExists("degree")) return false;

		String sql = "select count(*) from "+this.getSQLTableName("term");
		int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		
		sql = "select count(*) from "+this.getSQLTableName("degree");
		c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		*/
		
		String sql = "select count(*) from "+this.getSQLTableName("stats");
		int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		
		return true;
	}
	
}
