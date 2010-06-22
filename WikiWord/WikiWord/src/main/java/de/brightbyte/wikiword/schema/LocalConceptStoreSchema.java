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
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;

public class LocalConceptStoreSchema extends WikiWordConceptStoreSchema {
	protected EntityTable resourceTable;
	
	protected EntityTable sectionTable;

	protected RelationTable meaningTable;
	protected RelationTable aliasTable;
	protected RelationTable aboutTable;

	//protected EntityTable conceptDescriptionTable;
	
	protected Corpus corpus;

	public LocalConceptStoreSchema(Corpus corpus, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(corpus, connection, tweaks, useFlushQueue );
		init(corpus, tweaks);
	}

	public LocalConceptStoreSchema(Corpus corpus, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(corpus, connectionInfo, tweaks, useFlushQueue);
		init(corpus, tweaks);
	}
	
	private void init(Corpus corpus, TweakSet tweaks) {
		this.corpus = corpus;
		
		broaderTable.addField( new ReferenceField(this, "resource", "INT", null, false, KeyType.INDEX, "resource", "id", null ) ); //NOTE: not required. see buildSectionBroader.
		broaderTable.addField( new DatabaseField(this, "rule", "INT", null, true, KeyType.INDEX) );
		broaderTable.addField( new ReferenceField(this, "narrow_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		broaderTable.addField( new ReferenceField(this, "broad_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		//broaderTable.addKey( new DatabaseKey(this, KeyType.UNIQUE, "narrow_broad", new String[] {"narrow_name", "broad_name"}) );

		groupStats.add( new GroupStatsSpec("broader", "rule", extractionRulCodeTranslator));

		langlinkTable.addField( new ReferenceField(this, "resource", "INT", null, true, KeyType.INDEX, "resource", "id", null ) );
		langlinkTable.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, null, "concept", "name", null ) );
		langlinkTable.addKey( new DatabaseKey(this, KeyType.UNIQUE, "name_language_target", new String[] {"concept_name", "language", "target"}) );

		linkTable.addField( new ReferenceField(this, "anchor_name", getTextType(255), null, false, KeyType.INDEX, "concept", "name", null ) );
		linkTable.addField( new ReferenceField(this, "target_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		linkTable.addField( new ReferenceField(this, "resource", "INT", null, false, KeyType.INDEX, "resource", "id", null ) );
		linkTable.addField( new DatabaseField(this, "term_text", getTextType(255), null, true, null) );
		linkTable.addField( new DatabaseField(this, "rule", "INT", null, true, KeyType.INDEX) );
		linkTable.addKey( new DatabaseKey(this, KeyType.INDEX, "term_target", new String[] {"term_text", "target"}) );
				
		definitionTable.addField( new ReferenceField(this, "resource", "INT", null, true, KeyType.UNIQUE, "resource", "id", null ) );
		definitionTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "concept", new String[] {"concept"}) );
		//--------
		
		resourceTable = new EntityTable(this, "resource", getDefaultTableAttributes());
		resourceTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", true, KeyType.PRIMARY ) );
		resourceTable.addField( new DatabaseField(this, "page_id", "INT", null, false, KeyType.UNIQUE ) );
		resourceTable.addField( new DatabaseField(this, "revision_id", "INT", null, false, KeyType.UNIQUE ) );
		//resourceTable.addField( new ReferenceField(this, "corpus", "INT", null, true, null, "corpus", "id", null ) );
		resourceTable.addField( new DatabaseField(this, "name", getTextType(255), null, true, KeyType.UNIQUE ) );
		resourceTable.addField( new DatabaseField(this, "type", "INT", null, true, KeyType.INDEX ) ); //TODO: enum
		resourceTable.addField( new DatabaseField(this, "timestamp", "CHAR(14)", null, true, null ) ); //TODO: which type in which db?
		
		resourceTable.setAutomaticField("id");
		addTable(resourceTable);

		sectionTable = new EntityTable(this, "section", getDefaultTableAttributes());
		sectionTable.addField( new ReferenceField(this, "resource", "INT", null, true, KeyType.INDEX, "resource", "id", null ) );
		sectionTable.addField( new DatabaseField(this, "section_name", getTextType(255), null, true, KeyType.INDEX ) );
		sectionTable.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		sectionTable.addField( new DatabaseField(this, "section_concept", "INT", "default NULL", false, KeyType.INDEX ) );
		sectionTable.addField( new ReferenceField(this, "concept", "INT", "default NULL", false, KeyType.INDEX, "concept", "id", null ) );
		sectionTable.setAutomaticField(null);
		addTable(sectionTable);
		
		aliasTable = new RelationTable(this, "alias", getDefaultTableAttributes());
		//aliasTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", false, KeyType.PRIMARY) );
		aliasTable.addField( new ReferenceField(this, "resource", "INT", null, true, KeyType.INDEX, "resource", "id", null ) );
		aliasTable.addField( new ReferenceField(this, "source", "INT", null, false, KeyType.INDEX, "concept", "id", null ) );
		aliasTable.addField( new ReferenceField(this, "source_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		aliasTable.addField( new ReferenceField(this, "target", "INT", null, false, KeyType.INDEX, "concept", "id", null ) );
		aliasTable.addField( new ReferenceField(this, "target_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		aliasTable.addField( new DatabaseField(this, "scope", "INT", null, true, KeyType.INDEX) );
		//aliasTable.addField( new DatabaseField(this, "confidence", "DECIMAL(3,3)", null, true, null ) );
		//aliasTable.addKey( new DatabaseKey(this, KeyType.INDEX, "usage", new String[] {"narrow", "broad"}) );
		//aliasTable.addKey( new DatabaseKey(this, KeyType.UNIQUE, "ident", new String[] {"resource", "concept_name", "term_text"}) );
		addTable(aliasTable);

		aboutTable = new RelationTable(this, "about", getDefaultTableAttributes());
		//aliasTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", false, KeyType.PRIMARY) );
		aboutTable.addField( new ReferenceField(this, "resource", "INT", null, true, null, "resource", "id", null ) );
		aboutTable.addField( new ReferenceField(this, "resource_name", getTextType(255), null, true, KeyType.INDEX, "resource", "name", null ) );
		aboutTable.addField( new ReferenceField(this, "concept", "INT", null, false, KeyType.INDEX, "concept", "id", null ) );
		aboutTable.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		aboutTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "about", new String[] {"resource", "concept_name"}) );
		addTable(aboutTable);

		meaningTable = new RelationTable(this, "meaning", getDefaultTableAttributes());
		//meaningTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", false, KeyType.PRIMARY) );
		meaningTable.addField( new ReferenceField(this, "concept", "INT", null, true, KeyType.INDEX, "concept", "id", null ) );
		meaningTable.addField( new ReferenceField(this, "concept_name", getTextType(255), null, true, KeyType.INDEX, "concept", "name", null ) );
		meaningTable.addField( new DatabaseField(this, "freq", "INT", null, true, KeyType.INDEX ) );
		meaningTable.addField( new DatabaseField(this, "rule", "INT", null, true, KeyType.INDEX ) );
		//XXX: meaningTable.addField( new ReferenceField(this, "term", "INT", null, false, KeyType.INDEX, "term", "id", null ) );
		//XXX: meaningTable.addField( new ReferenceField(this, "term_text", getTextType(255), null, true, KeyType.INDEX, "term", "term", null ) );
		meaningTable.addField( new DatabaseField(this, "term_text", getTextType(255), null, true, null) );
		//XXX: meaningTable.addKey( new DatabaseKey(this, KeyType.INDEX, "usage", new String[] {"concept", "term"}) );
		meaningTable.addKey( new DatabaseKey(this, KeyType.PRIMARY, "term_concept", new String[] {"term_text", "concept"}) );
		addTable(meaningTable);
		
		groupStats.add( new GroupStatsSpec("resource", "type", resourceTypeCodeTranslator));
		groupStats.add( new GroupStatsSpec("link", "rule", extractionRulCodeTranslator));
	}
	
	@Override
	public ConceptType getConceptType(int type) {
		return corpus.getConceptTypes().getType(type);
	}

	public Corpus getCorpus() {
		return corpus;
	}
	
	/**
	 * @see de.brightbyte.wikiword.LocalConceptQuerior#checkConsistency()
	 */
	@Override
	public void checkConsistency() throws SQLException {
		super.checkConsistency();
		
		checkReferentialIntegrity(conceptTable, "resource", true); //NOTE: red links generate concepts with no resource assigned  
		
		checkReferentialIntegrity(meaningTable, "concept", false);

		checkIdSanity(resourceTable, "id");
		checkIdSanity(definitionTable, "concept");
		
		checkReferentialIntegrity(definitionTable, "concept", false);
	
		checkReferentialIntegrity(linkTable, "resource", true);
	
		checkReferencePairConsistency(broaderTable, "narrow", "narrow_name");
		checkReferencePairConsistency(broaderTable, "broad", "broad_name");

		//checkReferentialIntegrity(aliasTable, "source", false);
		//checkReferencePairConsistency(aliasTable, "source", "source_name"); //NOTE: source concepts get deleted!
		checkReferentialIntegrity(aliasTable, "target", false);
		checkReferencePairConsistency(aliasTable, "target", "target_name");
	
		checkReferencePairConsistency(langlinkTable, "concept", "concept_name");
		checkReferencePairConsistency(meaningTable, "concept", "concept_name");
	}
	
	@Override
	public boolean isComplete() throws SQLException {
			if (!super.isComplete()) return false;
			if (!this.tableExists("meaning")) return false;
			
			String sql = "select count(*) from "+this.getSQLTableName("meaning");
			int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
			if (c == 0) return false; //XXX: hack
			
			return true;
	}
	
	
}
