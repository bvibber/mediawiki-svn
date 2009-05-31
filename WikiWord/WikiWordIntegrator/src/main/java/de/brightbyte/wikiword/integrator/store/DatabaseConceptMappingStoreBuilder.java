package de.brightbyte.wikiword.integrator.store;

import java.sql.Connection;
import java.sql.SQLException;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.store.builder.DatabaseWikiWordStoreBuilder;

public class DatabaseConceptMappingStoreBuilder extends DatabaseWikiWordStoreBuilder implements ConceptMappingStoreBuilder {

	protected RelationTable mappingTable;
	protected Inserter mappingInserter;
	protected IntegratorSchema integratorSchema;

	public DatabaseConceptMappingStoreBuilder(String table, Corpus corpus, Connection connection, TweakSet tweaks) throws SQLException, PersistenceException {
		this(table, new IntegratorSchema(corpus, connection, tweaks, true), tweaks, null);
	}
	
	protected DatabaseConceptMappingStoreBuilder(String table, IntegratorSchema integratorSchema, TweakSet tweaks, Agenda agenda) throws SQLException, PersistenceException {
		super(integratorSchema, tweaks, agenda);

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
	
	public void storeMapping(String authority, String extId, String extName, int conceptId, String conceptName, String via, double weight) throws PersistenceException {
		try {
			mappingInserter.updateString("external_authority", authority);
			mappingInserter.updateString("external_id", extId);
			mappingInserter.updateInt("concept", conceptId); 
			mappingInserter.updateString("concept_name", conceptName);
			mappingInserter.updateString("via", via);
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