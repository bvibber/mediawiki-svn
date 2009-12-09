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
	protected EntityTable featureMagnitudeTable;
	protected RelationTable featureProductTable;
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
		featureTable.addField( new ReferenceField(this, "feature", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		featureTable.addField( new DatabaseField(this, "total_weight", "REAL", "DEFAULT 0", true, null ) );
		featureTable.addField( new DatabaseField(this, "normal_weight", "REAL", "DEFAULT 0", true, null ) );
		featureTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_feature", new String[] {"concept", "feature"}) );
		featureTable.setAutomaticField(null);
		addTable(featureTable);

		/*
		 featureProductTable = new RelationTable(this, "feature_product", getDefaultTableAttributes());
		featureProductTable.addField( new ReferenceField(this, "concept1", "INT", null, true, null, "concept", "id", null ));
		featureProductTable.addField( new ReferenceField(this, "concept2", "INT", null, true, null, "concept", "id", null ) );
		featureProductTable.addField( new ReferenceField(this, "feature", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		featureProductTable.addField( new DatabaseField(this, "total_weight", "REAL", "DEFAULT 0", true, null ) );
		featureProductTable.addField( new DatabaseField(this, "normal_weight", "REAL", "DEFAULT 0", true, null ) );
		featureProductTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept1_concept2_feature", new String[] {"concept1", "concept2", "feature"}) );
		featureProductTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept1_feature", new String[] {"concept1", "feature"}) );
		featureProductTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept2_feature", new String[] {"concept2", "feature"}) );
		featureProductTable.setAutomaticField(null);
		addTable(featureProductTable);*/

		featureMagnitudeTable = new EntityTable(this, "feature_magnitude", getDefaultTableAttributes());
		featureMagnitudeTable.addField( new ReferenceField(this, "concept", "INT", null, true, KeyType.PRIMARY, "concept", "id", null ));
		featureMagnitudeTable.addField( new DatabaseField(this, "magnitude", "REAL", "DEFAULT 0", true, null ) );
		featureMagnitudeTable.setAutomaticField(null);
		addTable(featureMagnitudeTable);

		proximityTable = new RelationTable(this, "proximity", getDefaultTableAttributes());
		proximityTable.addField( new ReferenceField(this, "concept1", "INT", null, true, null, "concept", "id", null ));
		proximityTable.addField( new ReferenceField(this, "concept2", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		proximityTable.addField( new DatabaseField(this, "proximity", "REAL", "DEFAULT 0", true, null ) );
		proximityTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concepts", new String[] {"concept1", "concept2"}) );
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
		if (!this.tableExists("feature")) return false;
		if (!this.tableExists("proximity")) return false;
		
		String sql = "select count(*) from "+this.getSQLTableName("feature");
		int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		
		sql = "select count(*) from "+this.getSQLTableName("proximity");
		c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		
		return true;
	}
	
}
