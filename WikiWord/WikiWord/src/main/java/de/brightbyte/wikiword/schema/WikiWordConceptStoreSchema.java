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

public class WikiWordConceptStoreSchema extends WikiWordStoreSchema {
	protected EntityTable conceptTable;

	protected RelationTable linkTable;
	protected RelationTable broaderTable;

	protected RelationTable relationTable;
	protected RelationTable langlinkTable;
	
	protected EntityTable definitionTable;
	
	public WikiWordConceptStoreSchema(DatasetIdentifier dataset, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, tweaks, useFlushQueue );
		init(tweaks);
	}

	public WikiWordConceptStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, tweaks, useFlushQueue);
		init(tweaks);
	}
	
	private void init(TweakSet tweaks) {
		conceptTable = new EntityTable(this, "concept", getDefaultTableAttributes());
		conceptTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", true, KeyType.PRIMARY ) );
		conceptTable.addField( new DatabaseField(this, "random", "REAL UNSIGNED", null, true, KeyType.INDEX ) );
		conceptTable.addField( new DatabaseField(this, "name", getTextType(255), null, true, KeyType.UNIQUE ) );
		conceptTable.addField( new DatabaseField(this, "type", "INT", null, true, KeyType.INDEX ) ); //TODO: enum
		conceptTable.setAutomaticField("id");
		addTable(conceptTable);

		broaderTable = new RelationTable(this, "broader", getDefaultTableAttributes());
		//broaderTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", false, KeyType.PRIMARY) );
		broaderTable.addField( new ReferenceField(this, "narrow", "INT", null, false, null, "concept", "id", null ) );
		broaderTable.addField( new ReferenceField(this, "broad", "INT", null, false, null, "concept", "id", null ) );
		//broaderTable.addField( new DatabaseField(this, "confidence", "DECIMAL(3,3)", null, true, null ) );
		broaderTable.addKey( new DatabaseKey(this, KeyType.INDEX, "narrow_broad", new String[] {"narrow", "broad"}) );
		broaderTable.addKey( new DatabaseKey(this, KeyType.INDEX, "broad_narrow", new String[] {"broad", "narrow"}) );
		addTable(broaderTable);
		
		langlinkTable = new RelationTable(this, "langlink", getDefaultTableAttributes());
		//langlinkTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", false, KeyType.PRIMARY) );
		langlinkTable.addField( new ReferenceField(this, "concept", "INT", null, false, null, "concept", "id", null ) );
		langlinkTable.addField( new DatabaseField(this, "language", getTextType(16), null, true, null ) );
		langlinkTable.addField( new DatabaseField(this, "target", getTextType(255), null, true, null ) );
		langlinkTable.addKey( new DatabaseKey(this, KeyType.INDEX, "language_target", new String[] {"language", "target"}) );
		langlinkTable.addKey( new DatabaseKey(this, KeyType.INDEX, "concept_language_target", new String[] {"concept", "language", "target"}) );
		addTable(langlinkTable);
		
		groupStats.add( new GroupStatsSpec("concept", "type", conceptTypeCodeTranslator));

		linkTable = new RelationTable(this, "link", getDefaultTableAttributes());
		linkTable.addField( new ReferenceField(this, "anchor", "INT", null, false, null, "concept", "id", null ) );
		linkTable.addField( new ReferenceField(this, "target", "INT", null, false, null, "concept", "id", null ) );
		linkTable.addKey( new DatabaseKey(this, KeyType.INDEX, "anchor_target", new String[] {"anchor", "target"}) );
		linkTable.addKey( new DatabaseKey(this, KeyType.INDEX, "target_anchor", new String[] {"target", "anchor"}) );
		addTable(linkTable);
		
		relationTable = new RelationTable(this, "relation", getDefaultTableAttributes());
		relationTable.addField( new ReferenceField(this, "concept1", "INT", null, true, null, "concept", "id", null ) );
		relationTable.addField( new ReferenceField(this, "concept2", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		relationTable.addField( new DatabaseField(this, "langmatch", "INT", "DEFAULT 0", true, KeyType.INDEX ) );
		relationTable.addField( new DatabaseField(this, "bilink", "INT", "DEFAULT 0", true, KeyType.INDEX ) );
		relationTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "one_two", new String[] {"concept1", "concept2"}) );		
		addTable(relationTable);
		
		definitionTable = new EntityTable(this, "definition", getDefaultTableAttributes());
		definitionTable.addField( new ReferenceField(this, "concept", "INT", null, true, null, "concept", "id", null ) );
		definitionTable.addField( new DatabaseField(this, "definition", getTextType(1024*8), null, true, null ) );
		definitionTable.setAutomaticField(null);
		addTable(definitionTable);
	}
	
	@Override
	public void checkConsistency() throws SQLException {
		checkIdSanity(conceptTable, "id"); //FIXME: this barfs spuriously. something insconsistent about th db state?!
		
		checkReferentialIntegrity(broaderTable, "narrow", false);
		checkReferentialIntegrity(broaderTable, "broad", false);
		
		checkReferentialIntegrity(langlinkTable, "concept", false);
		
		checkReferentialIntegrity(linkTable, "anchor", true);
		checkReferentialIntegrity(linkTable, "target", false);
		checkReferencePairConsistency(linkTable, "anchor", "anchor_name");
		checkReferencePairConsistency(linkTable, "target", "target_name");
	}
	
	@Override
	public boolean isComplete() throws SQLException {
			if (!super.isComplete()) return false;
			if (!this.tableExists("concept")) return false;
			
			String sql = "select count(*) from "+this.getSQLTableName("concept");
			int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
			return c > 0; //XXX: hack
	}
	
}
