package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.schema.ConceptStoreSchemas;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.PropertyStoreSchema;

public class DatabasePropertyStoreBuilder extends DatabaseLocalStoreBuilder implements PropertyStoreBuilder {

	protected RelationTable propertyTable;
	protected Inserter propertyInserter;
	private LocalConceptStoreSchema conceptStoreSchema;

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 * @throws PersistenceException 
	 */
	/*public DatabasePropertyStoreBuilder(Corpus corpus, DataSource dbInfo, TweakSet tweaks) throws SQLException, PersistenceException {
		this(new PropertyStoreSchema(corpus, dbInfo, tweaks, true), tweaks);
	}*/
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database accessed by the given database connection.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param db a database connection
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 * @throws PersistenceException 
	 */
	/*public DatabasePropertyStoreBuilder(Corpus corpus, Connection db, TweakSet tweaks) throws SQLException, PersistenceException {
		this(new PropertyStoreSchema(corpus, db, tweaks, true), tweaks);
	}*/
	
	public DatabasePropertyStoreBuilder(DatabaseLocalConceptStoreBuilder conceptStore, TweakSet tweaks) throws SQLException, PersistenceException {
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
	 * @throws PersistenceException 
	 */
	/*protected DatabasePropertyStoreBuilder(PropertyStoreSchema database, TweakSet tweaks) throws SQLException, PersistenceException {
		this( new DatabaseLocalConceptStoreBuilder(database.getCorpus(), database.getConnection(), tweaks),
				database, tweaks);
	}*/
	
	protected DatabasePropertyStoreBuilder(DatabaseLocalConceptStoreBuilder conceptStore, PropertyStoreSchema database, TweakSet tweaks) throws SQLException, PersistenceException {
		super(database, tweaks);

		//this.conceptStore = conceptStore;
		
		this.propertyInserter = configureTable("property", 128, 3*32);
		this.propertyTable =  (RelationTable)propertyInserter.getTable();
		
		this.conceptStoreSchema = new LocalConceptStoreSchema(conceptStore.getCorpus(), 
				conceptStore.getDatabaseAccess().getConnection(), 
				tweaks, true);
		
		this.agenda = conceptStore.getAgenda();
	}	

	@Override
	protected void deleteDataFrom(int rcId, String op) throws PersistenceException {
		deleteDataFrom(rcId, op, propertyTable, "concept");
	}
	
	@Override
	public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
		super.initialize(purge, dropAll);
	}
	
	@Override
	public void flush() throws PersistenceException {
		super.flush();
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

	/*
	public int storeConcept(int rcId, String name, ConceptType ctype) throws PersistenceException {
		return conceptStore.storeConcept(rcId, name, ctype);
	}

	public int storeResource(String name, ResourceType ptype, Date time) throws PersistenceException {
		return conceptStore.storeResource(name, ptype, time);
	}

	public void storeConceptAlias(int rcId, int source, String sourceName, int target, String targetName, AliasScope scope) throws PersistenceException {
		conceptStore.storeConceptAlias(rcId, source, sourceName, target, targetName, scope);
	}*/

	public Corpus getCorpus() {
		return (Corpus)database.getDataset();
	}
	
	public void finishAliases() throws PersistenceException {
		
		if (beginTask("finishAliases", "resolveRedirects:property")) {
			RelationTable aliasTable = (RelationTable)conceptStoreSchema.getTable("alias");
			
			int n = resolveRedirects(aliasTable, propertyTable, "concept_name", "concept", AliasScope.REDIRECT, 3);     
			endTask("finishAliases", "resolveRedirects:property", n+" entries");
		}
	}
}