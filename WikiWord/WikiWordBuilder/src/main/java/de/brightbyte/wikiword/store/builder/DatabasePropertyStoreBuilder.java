package de.brightbyte.wikiword.store.builder;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.Date;

import javax.sql.DataSource;

import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.schema.PropertyStoreSchema;

public class DatabasePropertyStoreBuilder extends DatabaseWikiWordStoreBuilder implements PropertyStoreBuilder {

	protected DatabaseLocalConceptStoreBuilder conceptStore;
	
	protected RelationTable propertyTable;
	protected Inserter propertyInserter;

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabasePropertyStoreBuilder(Corpus corpus, DataSource dbInfo, TweakSet tweaks) throws SQLException {
		this(new PropertyStoreSchema(corpus, dbInfo, tweaks, true), tweaks);
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
	public DatabasePropertyStoreBuilder(Corpus corpus, Connection db, TweakSet tweaks) throws SQLException {
		this(new PropertyStoreSchema(corpus, db, tweaks, true), tweaks);
	}
	
	public DatabasePropertyStoreBuilder(DatabaseLocalConceptStoreBuilder conceptStore, TweakSet tweaks) throws SQLException {
		this(conceptStore, 
				new PropertyStoreSchema(conceptStore.getCorpus(), 
								conceptStore.getDatabaseAccess().getConnection(), 
								tweaks, true), 
			tweaks);
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
	protected DatabasePropertyStoreBuilder(PropertyStoreSchema database, TweakSet tweaks) throws SQLException {
		this( new DatabaseLocalConceptStoreBuilder(database.getCorpus(), database.getConnection(), tweaks),
				database, tweaks);
	}
	
	protected DatabasePropertyStoreBuilder(DatabaseLocalConceptStoreBuilder conceptStore, PropertyStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);

		this.conceptStore = conceptStore;
		
		this.propertyInserter = configureTable("property", 128, 3*32);
		this.propertyTable =  (RelationTable)propertyInserter.getTable();
	}	

	@Override
	protected void deleteDataFrom(int rcId, String op) throws PersistenceException {
		conceptStore.deleteDataFrom(rcId, op);
		deleteDataFrom(rcId, op, propertyTable, "concept");
	}
	
	@Override
	public void prepare(boolean purge, boolean dropAll) throws PersistenceException {
		super.prepare(purge, dropAll);
		conceptStore.prepare(purge, dropAll);
	}
	
	@Override
	public void flush() throws PersistenceException {
		super.flush();
		conceptStore.flush();
	}
	
	protected int getConceptId(String title) throws SQLException {
		//String sql = "select id from "+localConceptDatabase.getSQLTableName("resource")
		//				+" where name = "+localConceptDatabase.quoteString(title);
		//return (Integer) localConceptDatabase.executeSingleValueQuery("getResourceId", sql);
		
		//TODO: get concept id
		throw new UnsupportedOperationException();
		//return -1;
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeRawText(int, java.lang.String)
	 */
	public void storeProperty(int resourceId, int concept, String name, String property, String value) throws PersistenceException {
		try {
			//int cId = getConceptId(name); //TODO: use join? //FIXME: when not provided
			
			propertyInserter.updateInt("resource", resourceId); 
			if (concept>0) propertyInserter.updateInt("concept", concept); //FIXME: id cache!
			propertyInserter.updateString("concept_name", name);
			propertyInserter.updateString("property", property);
			propertyInserter.updateString("value", value);
			propertyInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public int storeConcept(int rcId, String name, ConceptType ctype) throws PersistenceException {
		return conceptStore.storeConcept(rcId, name, ctype);
	}

	public int storeResource(String name, ResourceType ptype, Date time) throws PersistenceException {
		return conceptStore.storeResource(name, ptype, time);
	}

	public void storeConceptAlias(int rcId, int source, String sourceName, int target, String targetName, AliasScope scope) throws PersistenceException {
		conceptStore.storeConceptAlias(rcId, source, sourceName, target, targetName, scope);
	}

	public Corpus getCorpus() {
		return (Corpus)database.getDataset();
	}
	
}