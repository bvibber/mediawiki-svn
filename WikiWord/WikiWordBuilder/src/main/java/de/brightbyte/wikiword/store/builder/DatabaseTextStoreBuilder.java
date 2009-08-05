package de.brightbyte.wikiword.store.builder;

import java.sql.Connection;
import java.sql.SQLException;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.TextStoreSchema;

/**
 * A LocalConceptStore implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used by 
 * {@link de.brightbyte.wikiword.store.DatabaseTextStore}, see there.
 */
public class DatabaseTextStoreBuilder extends DatabaseIncrementalStoreBuilder implements TextStoreBuilder {
	
	protected LocalConceptStoreSchema localConceptDatabase;
	
	protected EntityTable plainTextTable;
	protected EntityTable rawTextTable;

	protected Inserter plainTextInserter;
	protected Inserter rawTextInserter;
	
	public DatabaseTextStoreBuilder(Corpus corpus, Connection connection, TweakSet tweaks) throws SQLException, PersistenceException {
		this(new LocalConceptStoreSchema(corpus, connection, tweaks, true), 
				new TextStoreSchema(corpus, connection, tweaks, true), 
				tweaks, null);
	}
	
	public DatabaseTextStoreBuilder(DatabaseLocalConceptStoreBuilder conceptStore, TweakSet tweaks) throws SQLException, PersistenceException {
		this((LocalConceptStoreSchema)conceptStore.getDatabaseAccess(), 
				new TextStoreSchema(conceptStore.getCorpus(), 
						conceptStore.getDatabaseAccess().getConnection(), 
						tweaks, true), 
				tweaks,
				conceptStore.getAgenda());
		
		database.setBackgroundErrorHandler(conceptStore.getDatabaseAccess().getBackgroundErrorHandler());
	}
	
	protected DatabaseTextStoreBuilder(LocalConceptStoreSchema conceptStoreSchema, TextStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException, PersistenceException {
		super(database, tweaks, agenda);
		
		localConceptDatabase = new LocalConceptStoreSchema(database.getCorpus(), database.getConnection(), tweaks, false);
		
		//XXX: wen don't need inserters, really...
		plainTextInserter = configureTable("plaintext", 32, 8*1024);
		rawTextInserter = configureTable("rawtext", 32, 8*1024);
		
		plainTextTable =  (EntityTable)plainTextInserter.getTable();
		rawTextTable =   (EntityTable)rawTextInserter.getTable();
		
		//this.idManager = idManager;
	}	

	@Override
	protected void deleteDataFrom(int rcId, String op) throws PersistenceException {
		deleteDataFrom(rcId, op, rawTextTable, "id");
		deleteDataFrom(rcId, op, plainTextTable, "id");
	}
	
	/*
	protected int getResourceId(String title) throws SQLException {
		String sql = "select id from "+localConceptDatabase.getSQLTableName("resource")
						+" where name = "+localConceptDatabase.quoteString(title);
		return (Integer) localConceptDatabase.executeSingleValueQuery("getResourceId", sql);
	}*/
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeRawText(int, java.lang.String)
	 */
	public void storeRawText(int rcId, String title, String text) throws PersistenceException {
		try {
			if (rawTextInserter==null) rawTextInserter = rawTextTable.getInserter();
			//if (rcId<=0) rcId = getResourceId(title); //TODO: use join?
			
			rawTextInserter.updateInt("id", rcId);
			rawTextInserter.updateInt("resource", rcId);
			rawTextInserter.updateString("text", text);
			rawTextInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storePlainText(int, java.lang.String)
	 */
	public void storePlainText(int rcId, String title, String text) throws PersistenceException {
		try {
			if (plainTextInserter==null) plainTextInserter = plainTextTable.getInserter();
//			if (rcId<=0) rcId = getResourceId(title); //TODO: use join?
			
			plainTextInserter.updateInt("id", rcId);
			plainTextInserter.updateInt("resource", rcId);
			plainTextInserter.updateString("text", text);
			plainTextInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	/*
	public void storePlainText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		storePlainText(textId, name, text);
	}

	public void storeRawText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		storeRawText(textId, name, text);
	}
 */
	public ConceptType getConceptType(int type) {
		return localConceptDatabase.getConceptType(type);
	}

	public Corpus getCorpus() {
		return ((TextStoreSchema)database).getCorpus();
	}
	
	/*
	public void finishAliases() throws PersistenceException {
		if (beginTask("DatabaseTextStoreBuilder.finishAliases", "resolveRedirects:property")) {
			RelationTable aliasTable = (RelationTable)database.getTable("alias");
			int n = resolveRedirects(aliasTable, rawTextTable, "concept_name", idManager!=null ? "concept" : null, AliasScope.REDIRECT, 3, null, null);
			endTask("DatabaseTextStoreBuilder.finishAliases", "resolveRedirects:property", n+" entries");
		}
	}

	public void finishIdReferences() throws PersistenceException {
		if (idManager==null && beginTask("DatabaseTextStoreBuilder.finishIdReferences", "buildIdLinks:property")) {
			int n = buildIdLinks(rawTextTable, "concept_name", "concept", 1);     
			endTask("DatabaseTextStoreBuilder.finishIdReferences", "buildIdLinks:property", n+" references");
		}
	}
	*/
	
	
	public void prepareMassInsert() throws PersistenceException {
		try {
				database.disableKeys();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void prepareMassProcessing() throws PersistenceException {
		try {
				database.enableKeys();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
}
