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
import de.brightbyte.wikiword.TweakSet;

public class PropertyStoreSchema extends WikiWordStoreSchema {
	
	protected RelationTable propertyTable;
	protected Corpus corpus;

	public PropertyStoreSchema(Corpus corpus, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(corpus, connection, tweaks, useFlushQueue );
		init(corpus, tweaks);
	}

	public PropertyStoreSchema(Corpus corpus, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(corpus, connectionInfo, tweaks, useFlushQueue);
		init(corpus, tweaks);
	}
	
	private void init(Corpus corpus, TweakSet tweaks) {
		this.corpus = corpus;
		
		propertyTable = new RelationTable(this, "property", defaultTableAttributes);
		propertyTable.addField( new ReferenceField(this, "resource", "INT", null, false, KeyType.INDEX, "resource", "id", null ) );
		propertyTable.addField( new ReferenceField(this, "concept", "INT", null, false, KeyType.INDEX, "concept", "id", null ) );
		propertyTable.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		propertyTable.addField( new DatabaseField(this, "property", getTextType(255), null, true, KeyType.INDEX) );
		propertyTable.addField( new DatabaseField(this, "value", getTextType(255), null, true, null) );
		propertyTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept_property", new String[] {"concept_name", "property"}) );
		propertyTable.addKey( new DatabaseKey(this, KeyType.INDEX, "property_value", new String[] {"property", "value"}) );
		addTable(propertyTable);
	}
	
	@Override
	public ConceptType getConceptType(int type) {
		return corpus.getConceptTypes().getType(type);
	}
	
	@Override
	public void checkConsistency() throws SQLException {
		checkReferentialIntegrity(propertyTable, "resource", false);   
		checkReferentialIntegrity(propertyTable, "concept", false);   
		checkReferentialIntegrity(propertyTable, "concept_name", true);   
		checkReferencePairConsistency(propertyTable, "concept", "concept_name");   
	}
	
	public Corpus getCorpus() {
		return corpus;
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
