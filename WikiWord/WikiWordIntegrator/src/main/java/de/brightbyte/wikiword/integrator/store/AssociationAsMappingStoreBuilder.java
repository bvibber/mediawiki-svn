package de.brightbyte.wikiword.integrator.store;

import java.util.Map;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.application.Agenda;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSets;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

public class AssociationAsMappingStoreBuilder implements
		AssociationFeatureStoreBuilder {
	
	public static class Factory<F extends ConceptMappingStoreBuilder> implements WikiWordStoreFactory<AssociationAsMappingStoreBuilder> {
		protected WikiWordStoreFactory<F> mappingStoreFactory;
		protected PropertyAccessor<FeatureSet, String> authorityAccessor;
		protected PropertyAccessor<FeatureSet, String> externalIdAccessor;
		protected PropertyAccessor<FeatureSet, String> externalNameAccessor;
		protected PropertyAccessor<FeatureSet, Integer> conceptIdAccessor;
		protected PropertyAccessor<FeatureSet, String> conceptNameAccessor;
		protected PropertyAccessor<FeatureSet, String> associationViaAccessor;
		protected PropertyAccessor<FeatureSet, Double> associationWeightAccessor;
		
		public Factory(
				WikiWordStoreFactory<F> mappingStoreFactory, 
				String authorityField, 
				String externalIdField,
				String externalNameField, 
				String conceptIdField, 
				String conceptNameField, 
				String associationViaField, 
				String associationWeightField) {
			
			this(mappingStoreFactory,
					FeatureSets.fieldAccessor(authorityField, String.class),
					FeatureSets.fieldAccessor(externalIdField, String.class),
					externalNameField==null ? null : FeatureSets.fieldAccessor(externalNameField, String.class),
					FeatureSets.fieldAccessor(conceptIdField, Integer.class),
					conceptNameField==null ? null : FeatureSets.fieldAccessor(conceptNameField, String.class),
					associationViaField==null ? null : FeatureSets.fieldAccessor(associationViaField, String.class),
					associationWeightField==null ? null : FeatureSets.fieldAccessor(associationWeightField, Double.class)
					);
		}
		
		public Factory(
				WikiWordStoreFactory<F> mappingStoreFactory, 
				PropertyAccessor<FeatureSet, String> authorityAccessor, 
				PropertyAccessor<FeatureSet, String> externalIdAccessor, 
				PropertyAccessor<FeatureSet, String> externalNameAccessor, 
				PropertyAccessor<FeatureSet, Integer> conceptIdAccessor, 
				PropertyAccessor<FeatureSet, String> conceptNameAccessor, 
				PropertyAccessor<FeatureSet, String> associationViaAccessor, 
				PropertyAccessor<FeatureSet, Double> associationWeightAccessor) {
			
			this.mappingStoreFactory = mappingStoreFactory;
			this.authorityAccessor = authorityAccessor;
			this.externalIdAccessor = externalIdAccessor;
			this.externalNameAccessor = externalNameAccessor;
			this.conceptIdAccessor = conceptIdAccessor;
			this.conceptNameAccessor = conceptNameAccessor;
			this.associationViaAccessor = associationViaAccessor;
			this.associationWeightAccessor = associationWeightAccessor;
		}

		@SuppressWarnings("unchecked")
		public AssociationAsMappingStoreBuilder newStore() throws PersistenceException {
				ConceptMappingStoreBuilder store = mappingStoreFactory.newStore();
			
				return new AssociationAsMappingStoreBuilder(
						store, 
						authorityAccessor, 
						externalIdAccessor, 
						externalNameAccessor, 
						conceptIdAccessor, 
						conceptNameAccessor, 
						associationViaAccessor, 
						associationWeightAccessor);
		}
	}

	
	protected ConceptMappingStoreBuilder store;
	protected PropertyAccessor<FeatureSet, String> authorityAccessor;
	protected PropertyAccessor<FeatureSet, String> externalIdAccessor;
	protected PropertyAccessor<FeatureSet, String> externalNameAccessor;
	protected PropertyAccessor<FeatureSet, Integer> conceptIdAccessor;
	protected PropertyAccessor<FeatureSet, String> conceptNameAccessor;
	protected PropertyAccessor<FeatureSet, String> associationViaAccessor;
	protected PropertyAccessor<FeatureSet, Double> associationWeightAccessor;
	
	public AssociationAsMappingStoreBuilder(
			ConceptMappingStoreBuilder store, 
			String authorityField, 
			String externalIdField,
			String externalNameField, 
			String conceptIdField, 
			String conceptNameField, 
			String associationViaField, 
			String associationWeightField) {
		
		this(store,
				FeatureSets.fieldAccessor(authorityField, String.class),
				FeatureSets.fieldAccessor(externalIdField, String.class),
				externalNameField==null ? null : FeatureSets.fieldAccessor(externalNameField, String.class),
				FeatureSets.fieldAccessor(conceptIdField, Integer.class),
				conceptNameField==null ? null : FeatureSets.fieldAccessor(conceptNameField, String.class),
				associationViaField==null ? null : FeatureSets.fieldAccessor(associationViaField, String.class),
				associationWeightField==null ? null : FeatureSets.fieldAccessor(associationWeightField, Double.class)
				);
	}
	
	public AssociationAsMappingStoreBuilder(
			ConceptMappingStoreBuilder store, 
			PropertyAccessor<FeatureSet, String> authorityAccessor, 
			PropertyAccessor<FeatureSet, String> externalIdAccessor, 
			PropertyAccessor<FeatureSet, String> externalNameAccessor, 
			PropertyAccessor<FeatureSet, Integer> conceptIdAccessor, 
			PropertyAccessor<FeatureSet, String> conceptNameAccessor, 
			PropertyAccessor<FeatureSet, String> associationViaAccessor, 
			PropertyAccessor<FeatureSet, Double> associationWeightAccessor) {
		
		super();
		this.store = store;
		this.authorityAccessor = authorityAccessor;
		this.externalIdAccessor = externalIdAccessor;
		this.externalNameAccessor = externalNameAccessor;
		this.conceptIdAccessor = conceptIdAccessor;
		this.conceptNameAccessor = conceptNameAccessor;
		this.associationViaAccessor = associationViaAccessor;
		this.associationWeightAccessor = associationWeightAccessor;
	}

	public void storeMapping(FeatureSet foreign, FeatureSet concept, FeatureSet props) throws PersistenceException {
		String authority = authorityAccessor.getValue(foreign); 
		String extId = externalIdAccessor.getValue(foreign);
		String extName = externalNameAccessor.getValue(foreign);
		int conceptId = conceptIdAccessor.getValue(concept);
		String name = conceptNameAccessor.getValue(concept);
		String via = associationViaAccessor.getValue(concept);
		double weight = associationWeightAccessor.getValue(concept);
		
		authorityAccessor.getValue(foreign);
		store.storeMapping( authority, extId, extName, conceptId, name, via, weight);
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

	public void storeMapping(String authority, String extId, String extName, int concept, String name, String via, double weight) throws PersistenceException {
		store.storeMapping(authority, extId, extName, concept, name, via, weight);
	}

	public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
		store.storeWarning(rcId, problem, details);
	}
	
	
}
