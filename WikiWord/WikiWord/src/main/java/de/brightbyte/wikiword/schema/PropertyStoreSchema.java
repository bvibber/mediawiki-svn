package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.RelationTable;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;

public class PropertyStoreSchema extends WikiWordStoreSchema {
	
	protected RelationTable propertyTable;
	protected DatasetIdentifier dataset;
	private boolean global;

	public PropertyStoreSchema(DatasetIdentifier dataset, Connection connection, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue );
		init(dataset, global, tweaks);
	}

	public PropertyStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, boolean global, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, tweaks, useFlushQueue);
		init(dataset, global, tweaks);
	}
	
	private void init(DatasetIdentifier dataset, boolean global, TweakSet tweaks) {
		this.dataset = dataset;
		this.global = global;
		
		propertyTable = new RelationTable(this, "property", getDefaultTableAttributes());
		propertyTable.addField( new ReferenceField(this, "concept", "INT", null, false, null, "concept", "id", null ) );
		propertyTable.addField( new DatabaseField(this, "property", getTextType(255), null, true, null) );
		propertyTable.addField( new DatabaseField(this, "value", getTextType(255), null, true, null) );
		
		propertyTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept_name_property", new String[] {"concept", "property", "value"}) );
		propertyTable.addKey( new DatabaseKey(this, KeyType.INDEX, "property_value", new String[] {"property", "value"}) );
		
		if (!global) propertyTable.addField( new ReferenceField(this, "resource", "INT", null, false, KeyType.INDEX, "resource", "id", null ) );
		if (!global) propertyTable.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, null, "concept", "name", null ) );
		if (!global) propertyTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept_name_property", new String[] {"concept_name", "property"}) );

		addTable(propertyTable);
	}
	
	public boolean isGlobal()  {
		return global;
	}
	
	@Override
	public ConceptType getConceptType(int type) {
		//TODO: concept types should be defined and accessible for globla datasets!
		if (dataset instanceof Corpus) return ((Corpus)dataset).getConceptTypes().getType(type);
		else throw new UnsupportedOperationException("can't get concept type from global dataset!"); 
	}
	
	@Override
	public void checkConsistency() throws SQLException {
		checkReferentialIntegrity(propertyTable, "resource", false);   
		checkReferentialIntegrity(propertyTable, "concept", false);   
		checkReferentialIntegrity(propertyTable, "concept_name", true);   
		checkReferencePairConsistency(propertyTable, "concept", "concept_name");   
	}
	
	public DatasetIdentifier getDataset() {
		return dataset;
	}

	@Override
	public boolean isComplete() throws SQLException {
		if (!super.isComplete()) return false;
		if (!this.tableExists("property")) return false;

		String sql = "select count(*) from "+this.getSQLTableName("property");
		int pr = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (pr<1) return false;		
		
		return true;
	}
	
}
