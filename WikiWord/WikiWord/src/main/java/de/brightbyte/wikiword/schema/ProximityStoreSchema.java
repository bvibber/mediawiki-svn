package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.RelationTable;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;

public class ProximityStoreSchema extends WikiWordStoreSchema {
	
	protected RelationTable featureTable;
	protected RelationTable proximityTable;


	public ProximityStoreSchema(DatasetIdentifier dataset, Connection connection, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue );
		init(global, tweaks);
	}

	public ProximityStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, tweaks, useFlushQueue);
		init(global, tweaks);
	}
	
	private void init(boolean global, TweakSet tweaks) {
		featureTable = new RelationTable(this, "feature", getDefaultTableAttributes());
		featureTable.addField( new ReferenceField(this, "concept", "INT", null, true, null, "concept", "id", null ));
		featureTable.addField( new ReferenceField(this, "target", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		featureTable.addField( new DatabaseField(this, "weight", "REAL", "DEFAULT 0", true, null ) );
		featureTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_target", new String[] {"concept", "target"}) );
		featureTable.setAutomaticField(null);
		addTable(featureTable);

		proximityTable = new RelationTable(this, "proximity", getDefaultTableAttributes());
		proximityTable.addField( new ReferenceField(this, "concept", "INT", null, true, null, "concept", "id", null ));
		proximityTable.addField( new ReferenceField(this, "target", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		proximityTable.addField( new DatabaseField(this, "proximity", "REAL", "DEFAULT 0", true, null ) );
		proximityTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_target", new String[] {"concept", "target"}) );
		proximityTable.setAutomaticField(null);
		addTable(proximityTable);
	}

	@Override
	public void checkConsistency() throws SQLException {
		checkReferentialIntegrity(featureTable, "concept", false);
		checkReferencePairConsistency(featureTable, "concept", "concept_name");
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
