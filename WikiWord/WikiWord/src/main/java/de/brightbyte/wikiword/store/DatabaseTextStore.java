package de.brightbyte.wikiword.store;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.EntityTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.TextStoreSchema;

import static de.brightbyte.db.DatabaseUtil.asString;

/**
 * A LocalConceptStore implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used by 
 * {@link de.brightbyte.wikiword.store.DatabaseTextStore}, see there.
 */
public class DatabaseTextStore extends DatabaseWikiWordStore implements TextStore {
	
	//protected LocalConceptStoreSchema localConceptDatabase;
	
	protected EntityTable plainTextTable;
	protected EntityTable rawTextTable;

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseTextStore(Corpus corpus, DataSource dbInfo, TweakSet tweaks) throws SQLException {
		this(new TextStoreSchema(corpus, dbInfo, tweaks, false), tweaks);
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
	public DatabaseTextStore(Corpus corpus, Connection db, TweakSet tweaks) throws SQLException {
		this(new TextStoreSchema(corpus, db, tweaks, false), tweaks);
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
	protected DatabaseTextStore(TextStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);
		
		//localConceptDatabase = new LocalConceptStoreSchema(database.getCorpus(), database.getConnection(), tweaks, false);
		
		plainTextTable =  (EntityTable)database.getTable("plaintext");
		rawTextTable =   (EntityTable)database.getTable("rawtext");
	}	

	/*
	protected int getResourceId(String title) throws SQLException {
		String sql = "select id from "+localConceptDatabase.getSQLTableName("resource")
						+" where name = "+localConceptDatabase.quoteString(title);
		return asInt( localConceptDatabase.executeSingleValueQuery("getResourceId", sql) );
	}
	*/

	protected String getText(EntityTable t, int resourceId) throws PersistenceException {
		try {
			String sql = "select text " +
					"from " + t.getSQLName() + " " +
					"where resource = "+resourceId;
			
			return asString( database.executeSingleValueQuery("getPlainText", sql) );
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected String getText(EntityTable t, String name) throws PersistenceException {
		try {
			String sql = "select T.text " +
					"from " + t.getSQLName() + " as T " +
					"join " + database.getSQLTableName("resource", false) + " as R " +
					"on T.resource = R.id " +
					"where R.name = "+database.quoteString(name);
			
			return asString( database.executeSingleValueQuery("getPlainText", sql) );
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public String getPlainText(int resourceId) throws PersistenceException {
		return getText(plainTextTable, resourceId);
	}

	public String getPlainText(String resourceName) throws PersistenceException {
		return getText(plainTextTable, resourceName);
	}

	public String getWikiText(int resourceId) throws PersistenceException {
		return getText(rawTextTable, resourceId);
	}

	public String getWikiText(String resourceName) throws PersistenceException {
		return getText(rawTextTable, resourceName);
	}

	public Corpus getCorpus() {
		return ((TextStoreSchema)database).getCorpus();
	}
		
}
