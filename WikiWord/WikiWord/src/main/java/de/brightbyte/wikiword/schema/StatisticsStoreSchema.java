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
