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
import de.brightbyte.wikiword.integrator.data.ConceptEntityRecord;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.DatabaseWikiWordStoreBuilder;

public class DatabaseConceptAssociationStoreBuilder extends DatabaseWikiWordStoreBuilder implements ConceptAssociationStoreBuilder {

	public static class Factory implements WikiWordStoreFactory<DatabaseConceptAssociationStoreBuilder> {
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
		public DatabaseConceptAssociationStoreBuilder newStore() throws PersistenceException {
			try {
				return new DatabaseConceptAssociationStoreBuilder(table, dataset, db.getConnection(), tweaks);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	}
	
	protected RelationTable mappingTable;
	protected Inserter mappingInserter;
	protected IntegratorSchema integratorSchema;

	public DatabaseConceptAssociationStoreBuilder(String table, DatasetIdentifier corpus, Connection connection, TweakSet tweaks) throws SQLException, PersistenceException {
		this(table, new IntegratorSchema(corpus, connection, tweaks, true), tweaks, null);
	}
	
	protected DatabaseConceptAssociationStoreBuilder(String table, IntegratorSchema integratorSchema, TweakSet tweaks, Agenda agenda) throws SQLException, PersistenceException {
		super(integratorSchema, tweaks, agenda);

		integratorSchema.newConceptAssociationTable(table); 
		
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
	
	public void storeAssociation(ForeignEntityRecord subject, ConceptEntityRecord object, Record qualifiers) throws PersistenceException {
		storeAssociation(
				subject.getAuthority(),
				subject.getID(),
				subject.getName(),
				object.getID(),
				object.getName(),
				qualifiers);
		
		//FIXME: custom qualifiers!
		/*
		String foreignProperty,
		String conceptProperty, String conceptPropertySource, int conceptPropertyFreq,
		String value, double weight
		*/
	}
	
	public void storeAssociation(String authority, String extId, String extName, 
			int conceptId, String conceptName, Record qualifiers
			) throws PersistenceException {
		try {
			mappingInserter.updateString("foreign_authority", authority);
			mappingInserter.updateString("foreign_id", extId);
			mappingInserter.updateString("foreign_name", extName);
			mappingInserter.updateInt("concept", conceptId); 
			mappingInserter.updateString("concept_name", conceptName);
			
//			FIXME: custom qualifiers!
			/*mappingInserter.updateString("foreign_property", foreignProperty);
			mappingInserter.updateString("concept_property", conceptProperty);
			mappingInserter.updateString("concept_property_source", conceptPropertySource);
			mappingInserter.updateInt("concept_property_freq", conceptPropertyFreq);
			mappingInserter.updateString("value", value);
			mappingInserter.updateDouble("weight", weight);*/
			mappingInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public Corpus getCorpus() {
		return (Corpus)database.getDataset();
	}
	
}