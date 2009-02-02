package de.brightbyte.wikiword.store.builder;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;
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
public class DatabaseTextStoreBuilder extends DatabaseLocalStoreBuilder implements TextStoreBuilder {
	
	protected LocalConceptStoreSchema localConceptDatabase;
	
	protected EntityTable plainTextTable;
	protected EntityTable rawTextTable;

	protected Inserter plainTextInserter;
	protected Inserter rawTextInserter;
	
	public DatabaseTextStoreBuilder(DatabaseLocalConceptStoreBuilder conceptStore, TweakSet tweaks) throws SQLException {
		this(new TextStoreSchema(conceptStore.getCorpus(), 
				conceptStore.getDatabaseAccess().getConnection(), tweaks, true), tweaks);
	}


	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseTextStoreBuilder(Corpus corpus, DataSource dbInfo, TweakSet tweaks) throws SQLException {
		this(new TextStoreSchema(corpus, dbInfo, tweaks, true), tweaks);
	}
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database accessed by the given database connection.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param db a database connection
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseTextStoreBuilder(Corpus corpus, Connection db, TweakSet tweaks) throws SQLException {
		this(new TextStoreSchema(corpus, db, tweaks, true), tweaks);
	}
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database represented by the DatabaseSchema.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param db empty DatabaseSchema, wrapping a database connection. Will be configured with the appropriate table defitions
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 * @throws SQLException 
	 */
	protected DatabaseTextStoreBuilder(TextStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);
		
		localConceptDatabase = new LocalConceptStoreSchema(database.getCorpus(), database.getConnection(), tweaks, false);
		
		//XXX: wen don't need inserters, really...
		plainTextInserter = configureTable("plaintext", 32, 8*1024);
		rawTextInserter = configureTable("rawtext", 32, 8*1024);
		
		plainTextTable =  (EntityTable)plainTextInserter.getTable();
		rawTextTable =   (EntityTable)rawTextInserter.getTable();
	}	

	@Override
	protected void deleteDataFrom(int rcId, String op) throws PersistenceException {
		deleteDataFrom(rcId, op, rawTextTable, "id");
		deleteDataFrom(rcId, op, plainTextTable, "id");
	}
	
	protected int getResourceId(String title) throws SQLException {
		String sql = "select id from "+localConceptDatabase.getSQLTableName("resource")
						+" where name = "+localConceptDatabase.quoteString(title);
		return (Integer) localConceptDatabase.executeSingleValueQuery("getResourceId", sql);
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeRawText(int, java.lang.String)
	 */
	public void storeRawText(int textId, String title, String text) throws PersistenceException {
		try {
			if (rawTextInserter==null) rawTextInserter = rawTextTable.getInserter();
			int rcId = getResourceId(title); //TODO: use join?
			
			rawTextInserter.updateInt("id", textId);
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
	public void storePlainText(int textId, String title, String text) throws PersistenceException {
		try {
			if (plainTextInserter==null) plainTextInserter = plainTextTable.getInserter();
			int rcId = getResourceId(title); //TODO: use join?
			
			plainTextInserter.updateInt("id", textId);
			plainTextInserter.updateInt("resource", rcId);
			plainTextInserter.updateString("text", text);
			plainTextInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void storePlainText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		storePlainText(textId, name, text);
	}

	public void storeRawText(int textId, String name, ResourceType ptype, String text) throws PersistenceException {
		storeRawText(textId, name, text);
	}

	public ConceptType getConceptType(int type) {
		return localConceptDatabase.getConceptType(type);
	}

	public Corpus getCorpus() {
		return ((TextStoreSchema)database).getCorpus();
	}
}
