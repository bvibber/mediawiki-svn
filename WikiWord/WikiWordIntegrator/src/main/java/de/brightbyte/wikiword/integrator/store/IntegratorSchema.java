package de.brightbyte.wikiword.integrator.store;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
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
		
		table.addField( new DatabaseField(this, "external_authority", getTextType(64), null, true, null) );
		table.addField( new DatabaseField(this, "external_id", getTextType(255), null, true, null) );
		
		table.addField( new DatabaseField(this, "property", getTextType(255), null, true, KeyType.INDEX) );
		table.addField( new DatabaseField(this, "value", getTextType(255), null, true, null) );
		table.addField( new DatabaseField(this, "qualifier", getTextType(64), null, false, null) );

		table.addKey( new DatabaseKey(this, KeyType.INDEX, "property_value", new String[] {"property", "value"}) );
		table.addKey( new DatabaseKey(this, KeyType.INDEX, "external_property", new String[] {"external_authority", "external_id", "property"}) );
		
		addTable(table);
		
		return table;
	}

	public RelationTable newConceptMappingTable(String name, boolean unique) {
		RelationTable table = new RelationTable(this, name, getDefaultTableAttributes());
		
		table.addField( new DatabaseField(this, "external_authority", getTextType(64), null, true, null) );
		table.addField( new DatabaseField(this, "external_id", getTextType(255), null, true, null) );
		table.addField( new DatabaseField(this, "external_name", getTextType(255), null, false, null) );
		
		table.addField( new ReferenceField(this, "concept", "INT", null, false, KeyType.INDEX, "concept", "id", null ) );
		table.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );

		table.addField( new DatabaseField(this, "via", getTextType(32), null, false, KeyType.INDEX ) );
		table.addField( new DatabaseField(this, "weight", "FLOAT", null, false, KeyType.INDEX ) );

		table.addKey( new DatabaseKey(this, KeyType.INDEX, "external_id", new String[] {"external_authority", "external_id"}) );
		table.addKey( new DatabaseKey(this, KeyType.INDEX, "external_name", new String[] {"external_authority", "external_name"}) );
		
		if (unique) table.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_mapping", new String[] {"concept", "external_authority", "external_id"}) );
		
		addTable(table);
		return table;
	}
	
}
