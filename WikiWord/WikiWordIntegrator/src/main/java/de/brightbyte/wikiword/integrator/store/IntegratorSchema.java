package de.brightbyte.wikiword.integrator.store;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.RelationTable;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;

public class IntegratorSchema extends WikiWordStoreSchema {

	public IntegratorSchema(DatasetIdentifier dataset, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue);
	}

	public IntegratorSchema(DatasetIdentifier dataset, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, tweaks, useFlushQueue);
	}

	public RelationTable newForeignPropertyTable(String name) {
		RelationTable table = new RelationTable(this, name, getDefaultTableAttributes());
		
		table.addField( new DatabaseField(this, "foreign_authority", getTextType(64), null, true, null) );
		table.addField( new DatabaseField(this, "foreign_id", getTextType(255), null, true, null) );
		
		table.addField( new DatabaseField(this, "property", getTextType(255), null, true, KeyType.INDEX) );
		table.addField( new DatabaseField(this, "value", getTextType(255), null, true, null) );
		//table.addField( new DatabaseField(this, "qualifier", getTextType(64), null, false, null) );
		//FIXME: custom qualifier fields!

		table.addKey( new DatabaseKey(this, KeyType.INDEX, "property_value", new String[] {"property", "value"}) );
		table.addKey( new DatabaseKey(this, KeyType.INDEX, "foreign_property", new String[] {"foreign_authority", "foreign_id", "property"}) );
		
		addTable(table);
		
		return table;
	}

	public RelationTable newConceptAssociationTable(String name) {
		RelationTable table = new RelationTable(this, name, getDefaultTableAttributes());
		
		table.addField( new DatabaseField(this, "foreign_authority", getTextType(64), null, true, null) );
		table.addField( new DatabaseField(this, "foreign_id", getTextType(255), null, true, null) );
		table.addField( new DatabaseField(this, "foreign_name", getTextType(255), null, false, null) );
		
		table.addField( new ReferenceField(this, "concept", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		table.addField( new ReferenceField(this, "concept_name", getTextType(255), null, false, KeyType.INDEX, "concept", "name", null ) );

		table.addField( new DatabaseField(this, "foreign_property", getTextType(64), null, false, null ) );
		
		table.addField( new DatabaseField(this, "concept_property", getTextType(64), null, false, null ) );
		table.addField( new DatabaseField(this, "concept_property_source", getTextType(64), null, false, null ) );
		table.addField( new DatabaseField(this, "concept_property_freq", "INT", null, false, null ) );
		
		table.addField( new DatabaseField(this, "value", getTextType(255), null, false, null ) );
		table.addField( new DatabaseField(this, "weight", "FLOAT", null, false, null ) );

		table.addKey( new DatabaseKey(this, KeyType.INDEX, "foreign_id", new String[] {"foreign_authority", "foreign_id"}) );
		table.addKey( new DatabaseKey(this, KeyType.INDEX, "foreign_name", new String[] {"foreign_authority", "foreign_name"}) );
		table.addKey( new DatabaseKey(this, KeyType.INDEX, "foreign_property", new String[] {"foreign_property", "concept_property"}) );
		table.addKey( new DatabaseKey(this, KeyType.INDEX, "concept_property", new String[] {"concept_property", "concept_property_source"}) );

		addTable(table);
		return table;
	}

	/*
	public RelationTable newConceptMappingTable(String name) {
		RelationTable table = new RelationTable(this, name, getDefaultTableAttributes());
		
		table.addField( new DatabaseField(this, "foreign_authority", getTextType(64), null, true, null) );
		table.addField( new DatabaseField(this, "foreign_id", getTextType(255), null, true, null) );
		table.addField( new DatabaseField(this, "foreign_name", getTextType(255), null, false, null) );
		
		table.addField( new ReferenceField(this, "concept", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		table.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );

		table.addField( new DatabaseField(this, "annotation", getTextType(255), null, false, null ) );
		table.addField( new DatabaseField(this, "weight", "FLOAT", null, false, null ) );

		table.addKey( new DatabaseKey(this, KeyType.INDEX, "foreign_id", new String[] {"foreign_authority", "foreign_id"}) );
		table.addKey( new DatabaseKey(this, KeyType.INDEX, "foreign_name", new String[] {"foreign_authority", "foreign_name"}) );
		
		table.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_mapping", new String[] {"concept", "foreign_authority", "foreign_id"}) );
		
		addTable(table);
		return table;
	}
*/
	public void loadForeignRecordTable(String table) throws SQLException {
		DatabaseTable t = loadTableDefinition(table);
		addTable(t);
	}
	
}
