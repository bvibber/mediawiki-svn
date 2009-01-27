package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;

public class TextStoreSchema extends WikiWordStoreSchema {
	
	protected EntityTable rawTextTable;
	protected EntityTable plainTextTable;
	protected Corpus corpus;

	public TextStoreSchema(Corpus corpus, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(corpus, connection, tweaks, useFlushQueue );
		init(corpus, tweaks);
	}

	public TextStoreSchema(Corpus corpus, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(corpus, connectionInfo, tweaks, useFlushQueue);
		init(corpus, tweaks);
	}
	
	private void init(Corpus corpus, TweakSet tweaks) {
		this.corpus = corpus;
		
		rawTextTable = new EntityTable(this, "rawtext", defaultTableAttributes);
		rawTextTable.addField( new DatabaseField(this, "id", "INT", null, true, KeyType.PRIMARY ) );
		rawTextTable.addField( new ReferenceField(this, "resource", "INT", null, true, KeyType.UNIQUE, "resource", "id", null ) );
		rawTextTable.addField( new DatabaseField(this, "text", "LONGTEXT", null, true, null ) );
		rawTextTable.setAutomaticField(null);
		addTable(rawTextTable);

		plainTextTable = new EntityTable(this, "plaintext", defaultTableAttributes);
		plainTextTable.addField( new DatabaseField(this, "id", "INT", null, true, KeyType.PRIMARY ) );
		plainTextTable.addField( new ReferenceField(this, "resource", "INT", null, true, KeyType.UNIQUE, "resource", "id", null ) );
		plainTextTable.addField( new DatabaseField(this, "text", "LONGTEXT", null, true, null ) );
		plainTextTable.setAutomaticField(null);
		addTable(plainTextTable);
	}
	
	@Override
	public ConceptType getConceptType(int type) {
		return corpus.getConceptTypes().getType(type);
	}
	
	@Override
	public void checkConsistency() throws SQLException {
		checkReferentialIntegrity(rawTextTable, "resource", false);   
		checkReferentialIntegrity(plainTextTable, "resource", false);   
	}
	
	public Corpus getCorpus() {
		return corpus;
	}

	@Override
	public boolean isComplete() throws SQLException {
		if (!super.isComplete()) return false;
		if (!this.tableExists("plaintext")) return false;
		if (!this.tableExists("rawtext")) return false;

		String sql = "select count(*) from "+this.getSQLTableName("concept", true);
		int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();

		sql = "select count(*) from "+this.getSQLTableName("plaintext");
		int cp = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (cp!=c) return false;
		
		sql = "select count(*) from "+this.getSQLTableName("rawtext");
		int cr = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (cr!=c) return false;		
		
		return true;
	}
	
}
