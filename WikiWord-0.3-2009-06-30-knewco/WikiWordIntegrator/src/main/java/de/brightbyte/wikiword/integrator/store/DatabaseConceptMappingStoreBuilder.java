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
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.DatabaseWikiWordStoreBuilder;

public class DatabaseConceptMappingStoreBuilder extends DatabaseWikiWordStoreBuilder implements ConceptMappingStoreBuilder {

	public static class Factory implements WikiWordStoreFactory<DatabaseConceptMappingStoreBuilder> {
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
		public DatabaseConceptMappingStoreBuilder newStore() throws PersistenceException {
			try {
				return new DatabaseConceptMappingStoreBuilder(table, dataset, db.getConnection(), tweaks);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	}
	
	protected RelationTable mappingTable;
	protected Inserter mappingInserter;
	protected IntegratorSchema integratorSchema;

	public DatabaseConceptMappingStoreBuilder(String table, DatasetIdentifier corpus, Connection connection, TweakSet tweaks) throws SQLException, PersistenceException {
		this(table, new IntegratorSchema(corpus, connection, tweaks, true), tweaks, null);
	}
	
	protected DatabaseConceptMappingStoreBuilder(String table, IntegratorSchema integratorSchema, TweakSet tweaks, Agenda agenda) throws SQLException, PersistenceException {
		super(integratorSchema, tweaks, agenda);

		integratorSchema.newConceptMappingTable(table); 
		
		this.mappingInserter = configureTable(table, 128, 5*32);
		this.mappingTable =  (RelationTable)mappingInserter.getTable();
	}	
	
	@Override
	public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
		super.initialize(purge, dropAll);
	}
	
	@Override
	public void flush() throws PersistenceException {
		super.flush();
	}
	
	public void storeMapping(String authority, String extId, String extName, int conceptId, String conceptName, double weight, String annotation) throws PersistenceException {
		try {
			mappingInserter.updateString("foreign_authority", authority);
			mappingInserter.updateString("foreign_id", extId);
			mappingInserter.updateString("foreign_name", extName);
			mappingInserter.updateInt("concept", conceptId); 
			mappingInserter.updateString("concept_name", conceptName);
			mappingInserter.updateString("annotation", annotation);
			mappingInserter.updateDouble("weight", weight);
			mappingInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public Corpus getCorpus() {
		return (Corpus)database.getDataset();
	}
	
}