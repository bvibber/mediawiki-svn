package de.brightbyte.wikiword.store.builder;

import java.sql.Connection;
import java.sql.SQLException;

import de.brightbyte.application.Agenda;
import de.brightbyte.data.PersistentIdManager;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.PropertyStoreSchema;

public class DatabasePropertyStoreBuilder extends DatabaseIncrementalStoreBuilder implements PropertyStoreBuilder {

	protected RelationTable propertyTable;
	protected Inserter propertyInserter;
	protected LocalConceptStoreSchema conceptStoreSchema;
	protected PersistentIdManager idManager;

	public DatabasePropertyStoreBuilder(Corpus corpus, Connection connection, TweakSet tweaks) throws SQLException, PersistenceException {
		this(new LocalConceptStoreSchema(corpus, connection, tweaks, true), 
				new PropertyStoreSchema(corpus, connection, false, tweaks, true), 
				null, tweaks, null);
	}
	
	protected DatabaseTable getTable(String name) {
		if (database.hasTable(name))
			return database.getTable(name);
		else
			return conceptStoreSchema.getTable(name);
	}
	
	public DatabasePropertyStoreBuilder(DatabaseLocalConceptStoreBuilder conceptStore, TweakSet tweaks) throws SQLException, PersistenceException {
		this((LocalConceptStoreSchema)conceptStore.getDatabaseAccess(), 
				new PropertyStoreSchema(conceptStore.getCorpus(), 
						conceptStore.getDatabaseAccess().getConnection(), 
						false, tweaks, true), 
				conceptStore.idManager,
				tweaks,
				conceptStore.getAgenda());
		
		database.setBackgroundErrorHandler(conceptStore.getDatabaseAccess().getBackgroundErrorHandler());
	}
	
	protected DatabasePropertyStoreBuilder(LocalConceptStoreSchema conceptStoreSchema, PropertyStoreSchema database, PersistentIdManager idManager, TweakSet tweaks, Agenda agenda) throws SQLException, PersistenceException {
		super(database, tweaks, agenda);

		//this.conceptStore = conceptStore;
		
		this.propertyInserter = configureTable("property", 128, 3*32);
		this.propertyTable =  (RelationTable)propertyInserter.getTable();
		
		this.conceptStoreSchema = conceptStoreSchema;
		this.idManager = idManager;
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
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeRawText(int, java.lang.String)
	 */
	public void storeProperty(int resourceId, int concept, String name, String property, String value) throws PersistenceException {
		try {
			//int cId = getConceptId(name); //TODO: use join? //FIXME: when not provided
			
			propertyInserter.updateInt("resource", resourceId); 
			if (concept>0) propertyInserter.updateInt("concept", concept); 
			else if (idManager!=null) propertyInserter.updateInt("concept", idManager.aquireId(name));   
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
	
	protected boolean hasUnresolvedConceptReferences() throws PersistenceException{
		try {
			Number c = (Number)database.executeSingleValueQuery("DatabasePropertyStoreBuilder.finishAliases#hasNull?", "select exists(select * from "+propertyTable.getSQLName()+" where concept is null)");
			return c.intValue() > 0;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected boolean hasResolvedConceptReferences() throws PersistenceException {
		try {
			Number c = (Number)database.executeSingleValueQuery("DatabasePropertyStoreBuilder.finishAliases#hasNull?", "select exists(select * from "+propertyTable.getSQLName()+" where concept is not null)");
			return c.intValue() > 0;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void finishAliases() throws PersistenceException {
		RelationTable aliasTable = (RelationTable)conceptStoreSchema.getTable("alias");
		
		if (beginTask("DatabasePropertyStoreBuilder.finishAliases", "resolveRedirects:property#id")) {
			int n = 0;
			if (hasResolvedConceptReferences()) {
				n = resolveRedirects(aliasTable, propertyTable, "concept_name", "concept", AliasScope.REDIRECT, 3, null, null, null);
			}
			
			endTask("DatabasePropertyStoreBuilder.finishAliases", "resolveRedirects:property#id", n+" entries");
		}
		
		if (beginTask("DatabasePropertyStoreBuilder.finishAliases", "resolveRedirects:property#name")) {
			int n = 0;
			if (hasUnresolvedConceptReferences()) {
				n = resolveRedirects(aliasTable, propertyTable, "concept_name", null, AliasScope.REDIRECT, 3, propertyTable.getSQLName()+".concept is null", null, null);
			}
			
			endTask("DatabasePropertyStoreBuilder.finishAliases", "resolveRedirects:property#name", n+" entries");
		}
	}

	public void finishIdReferences() throws PersistenceException {
		if (beginTask("DatabasePropertyStoreBuilder.finishIdReferences", "buildIdLinks:property")) {
			int n = 0;
			if (hasUnresolvedConceptReferences()) {
				n = buildIdLinks(propertyTable, "concept_name", "concept", 1);
			}
			
			endTask("DatabasePropertyStoreBuilder.finishIdReferences", "buildIdLinks:property", n+" references");
		}
	}

	public void prepareMassProcessing() throws PersistenceException {
		try {
			this.conceptStoreSchema.enableKeys();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
		
		super.prepareMassProcessing();
	}
	
	
}