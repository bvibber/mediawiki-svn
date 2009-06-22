package de.brightbyte.wikiword.integrator.store;

import java.util.Map;

import de.brightbyte.application.Agenda;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public abstract class AbstractMappingAssociationFeatureStoreBuilder<D extends WikiWordStoreBuilder> implements
		AssociationFeatureStoreBuilder {
	
	public static abstract class Factory<T extends AbstractMappingAssociationFeatureStoreBuilder, D extends WikiWordStoreBuilder> implements WikiWordStoreFactory<T> {
		protected WikiWordStoreFactory<? extends D> associationStoreFactory;
		protected FeatureMapping foreignMapping;
		protected FeatureMapping conceptMapping;
		protected FeatureMapping assocMapping;
		
		public Factory(
				WikiWordStoreFactory<? extends D> associationStoreFactory, 
				 FeatureMapping foreignMapping, 
				 FeatureMapping conceptMapping, 
				 FeatureMapping assocMapping) {
			
			this.associationStoreFactory = associationStoreFactory;
			this.foreignMapping = foreignMapping;
			this.conceptMapping = conceptMapping;
			this.assocMapping = assocMapping;
		}

		@SuppressWarnings("unchecked")
		public T newStore() throws PersistenceException {
			D store = associationStoreFactory.newStore();
			
				return newStore(store);
		}

		protected abstract T newStore(D store);
	}

	
	protected D store;
	protected FeatureMapping foreignMapping;
	protected FeatureMapping conceptMapping;
	protected FeatureMapping assocMapping;
	
	public AbstractMappingAssociationFeatureStoreBuilder(
			D store,
			FeatureMapping foreignMapping, 
			 FeatureMapping conceptMapping, 
			 FeatureMapping assocMapping) {
		
		super();
		this.store = store;
		this.foreignMapping = foreignMapping;
		this.conceptMapping = conceptMapping;
		this.assocMapping = assocMapping;
	}

	public void storeAssociationFeatures(FeatureSet association) throws PersistenceException {
		storeAssociationFeatures(association, association, association);
	}
	
	public void checkConsistency() throws PersistenceException {
		store.checkConsistency();
	}

	public void close(boolean flush) throws PersistenceException {
		store.close(flush);
	}

	public Agenda createAgenda() throws PersistenceException {
		return store.createAgenda();
	}

	public void dumpTableStats(Output out) throws PersistenceException {
		store.dumpTableStats(out);
	}

	public void finalizeImport() throws PersistenceException {
		store.finalizeImport();
	}

	public void flush() throws PersistenceException {
		store.flush();
	}

	public Agenda getAgenda() throws PersistenceException {
		return store.getAgenda();
	}

	public DatasetIdentifier getDatasetIdentifier() {
		return store.getDatasetIdentifier();
	}

	public int getNumberOfWarnings() throws PersistenceException {
		return store.getNumberOfWarnings();
	}

	public Map<String, ? extends Number> getTableStats() throws PersistenceException {
		return store.getTableStats();
	}

	public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
		store.initialize(purge, dropAll);
	}

	public boolean isComplete() throws PersistenceException {
		return store.isComplete();
	}

	public void open() throws PersistenceException {
		store.open();
	}

	public void optimize() throws PersistenceException {
		store.optimize();
	}

	public void prepareImport() throws PersistenceException {
		store.prepareImport();
	}

	public void setLogLevel(int loglevel) {
		store.setLogLevel(loglevel);
	}

	public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
		store.storeWarning(rcId, problem, details);
	}

}
