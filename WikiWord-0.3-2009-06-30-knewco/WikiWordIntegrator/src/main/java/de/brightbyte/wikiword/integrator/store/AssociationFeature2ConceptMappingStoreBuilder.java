package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

public class AssociationFeature2ConceptMappingStoreBuilder extends AbstractMappingAssociationFeatureStoreBuilder<ConceptMappingStoreBuilder> {
	
	public static final String FOREIGN_AUTHORITY = "FOREIGN_AUTHORITY";
	public static final String FOREIGN_ID = "FOREIGN_ID";
	public static final String FOREIGN_NAME = "FOREIGN_NAME";
	public static final String CONCEPT_ID = "CONCEPT_ID";
	public static final String CONCEPT_NAME = "CONCEPT_NAME";
	public static final String ASSOCIATION_ANNOTATION = "ASSOCIATION_ANNOTATION";
	public static final String ASSOCIATION_WEIGHT = "ASSOCIATION_WEIGHT";
	
	public static class Factory<D extends ConceptMappingStoreBuilder> extends AbstractMappingAssociationFeatureStoreBuilder.Factory<AssociationFeature2ConceptMappingStoreBuilder, D> {
		public Factory(WikiWordStoreFactory<? extends D> associationStoreFactory, FeatureMapping foreignMapping, FeatureMapping conceptMapping, FeatureMapping assocMapping) {
			super(associationStoreFactory, foreignMapping, conceptMapping, assocMapping);
			
			foreignMapping.assertAccessor(FOREIGN_AUTHORITY);
			foreignMapping.assertAccessor(FOREIGN_ID);
			if (!foreignMapping.hasAccessor(FOREIGN_NAME)) foreignMapping.addMapping(FOREIGN_NAME, foreignMapping.getAccessor(FOREIGN_ID));

			conceptMapping.assertAccessor(CONCEPT_ID);
			//CONCEPT_NAME is optional
			
			//all assoc mappings are optional
		}

		protected AssociationFeature2ConceptMappingStoreBuilder newStore(D store) {
				return new AssociationFeature2ConceptMappingStoreBuilder(
						store, foreignMapping, conceptMapping, assocMapping);
		}
	}
	
	public AssociationFeature2ConceptMappingStoreBuilder(
			ConceptMappingStoreBuilder store,
			FeatureMapping foreignMapping, 
			 FeatureMapping conceptMapping, 
			 FeatureMapping assocMapping) {
		
		super(store, foreignMapping, conceptMapping, assocMapping);

		foreignMapping.assertAccessor(FOREIGN_AUTHORITY);
		foreignMapping.assertAccessor(FOREIGN_ID);
		if (!foreignMapping.hasAccessor(FOREIGN_NAME)) foreignMapping.addMapping(FOREIGN_NAME, foreignMapping.getAccessor(FOREIGN_ID));

		conceptMapping.assertAccessor(CONCEPT_ID);
		//CONCEPT_NAME is optional
		
		//all assoc mappings are optional
	}
	
	public void storeAssociationFeatures(FeatureSet foreign, FeatureSet concept, FeatureSet props) throws PersistenceException {
		String authority = foreignMapping.requireValue(foreign, FOREIGN_AUTHORITY, String.class);
		String extId = foreignMapping.requireValue(foreign, FOREIGN_ID, String.class);
		String extName = foreignMapping.getValue(foreign, FOREIGN_NAME, String.class);
		int conceptId = conceptMapping.requireValue(concept, CONCEPT_ID, Integer.class);
		String name = conceptMapping.getValue(concept, CONCEPT_NAME, String.class);
		String annotation = assocMapping.getValue(props, ASSOCIATION_ANNOTATION, String.class);
		double weight = assocMapping.getValue(props, ASSOCIATION_WEIGHT, Double.class, 1.0);
		
		store.storeMapping(authority, extId, extName, conceptId, name, weight, annotation);
	}
}
