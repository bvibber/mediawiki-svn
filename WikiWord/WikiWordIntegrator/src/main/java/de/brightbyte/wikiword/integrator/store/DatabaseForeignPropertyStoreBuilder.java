package de.brightbyte.wikiword.integrator.store;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.DatabaseWikiWordStoreBuilder;

public class DatabaseForeignPropertyStoreBuilder extends DatabaseWikiWordStoreBuilder implements ForeignPropertyStoreBuilder {

	public static class Factory implements WikiWordStoreFactory<DatabaseForeignPropertyStoreBuilder> {
		private String table;
		private DataSource db;
		private DatasetIdentifier dataset;
		private TweakSet tweaks;

		public Factory(String table, DatasetIdentifier dataset, DataSource db, TweakSet tweaks) {
			super();
			this.table = table;
			this.db = db;
			this.dataset = dataset;
			this.tweaks = tweaks;
		}

		@SuppressWarnings("unchecked")
		public DatabaseForeignPropertyStoreBuilder newStore() throws PersistenceException {
			try {
				return new DatabaseForeignPropertyStoreBuilder(table, dataset, db.getConnection(), tweaks);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	}
	
	protected RelationTable propertyTable;
	protected Inserter propertyInserter;
	protected IntegratorSchema integratorSchema;

	public DatabaseForeignPropertyStoreBuilder(String table, DatasetIdentifier dataset, Connection connection, TweakSet tweaks) throws SQLException, PersistenceException {
		this(table, new IntegratorSchema(dataset, connection, tweaks, true), tweaks, null);
	}
	
	protected DatabaseForeignPropertyStoreBuilder(String table, IntegratorSchema integratorSchema, TweakSet tweaks, Agenda agenda) throws SQLException, PersistenceException {
		super(integratorSchema, tweaks, agenda);

		integratorSchema.newForeignPropertyTable(table); 
		
		this.propertyInserter = configureTable(table, 128, 4*32);
		this.propertyTable =  (RelationTable)propertyInserter.getTable();
	}	
	
	@Override
	public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
		super.initialize(purge, dropAll);
	}
	
	@Override
	public void flush() throws PersistenceException {
		super.flush();
	}
	
	public void storeProperty(String authority, String extId, String property, Object value, Record qualifiers) throws PersistenceException {
		try {
			propertyInserter.updateString("foreign_authority", authority);
			propertyInserter.updateString("foreign_id", extId);
			propertyInserter.updateString("property", property);
			propertyInserter.updateObject("value", value);
			//propertyInserter.updateString("qualifier", qualifier); //FIXME: store qualifiers
			propertyInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public Corpus getCorpus() {
		return (Corpus)database.getDataset();
	}
	
	/*
	public void finishAliases() throws PersistenceException {
		if (beginTask("DatabasePropertyStoreBuilder.finishAliases", "resolveRedirects:property")) {
			RelationTable aliasTable = (RelationTable)conceptStoreSchema.getTable("alias");
			int n = resolveRedirects(aliasTable, propertyTable, "concept_name", idManager!=null ? "concept" : null, AliasScope.REDIRECT, 3, null, null);
			endTask("DatabasePropertyStoreBuilder.finishAliases", "resolveRedirects:property", n+" entries");
		}
	}

	public void finishIdReferences() throws PersistenceException {
		if (idManager==null && beginTask("DatabasePropertyStoreBuilder.finishIdReferences", "buildIdLinks:property")) {
			int n = buildIdLinks(propertyTable, "concept_name", "concept", 1);     
			endTask("DatabasePropertyStoreBuilder.finishIdReferences", "buildIdLinks:property", n+" references");
		}
	}
	*/
	
	public void prepareImport() throws PersistenceException {
		createTables(true);
	}

}