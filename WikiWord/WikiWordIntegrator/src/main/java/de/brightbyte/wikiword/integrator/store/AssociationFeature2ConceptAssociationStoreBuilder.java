package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

public class AssociationFeature2ConceptAssociationStoreBuilder extends AbstractMappingAssociationFeatureStoreBuilder<ConceptAssociationStoreBuilder> {
	
	public static final String FOREIGN_AUTHORITY = "FOREIGN_AUTHORITY";
	public static final String FOREIGN_ID = "FOREIGN_ID";
	public static final String FOREIGN_NAME = "FOREIGN_NAME";
	public static final String CONCEPT_ID = "CONCEPT_ID";
	public static final String CONCEPT_NAME = "CONCEPT_NAME";
	public static final String FOREIGN_PROPERTY = "FOREIGN_PROPERTY";
	public static final String CONCEPT_PROPERTY = "CONCEPT_PROPERTY";
	public static final String CONCEPT_PROPERTY_SOURCE = "CONCEPT_PROPERTY_SOURCE";
	public static final String CONCEPT_PROPERTY_FREQ = "CONCEPT_PROPERTY_FREQ";
	public static final String ASSOCIATION_WEIGHT = "ASSOCIATION_WEIGHT";
	
	public static class Factory<D extends ConceptAssociationStoreBuilder> extends AbstractMappingAssociationFeatureStoreBuilder.Factory<AssociationFeature2ConceptAssociationStoreBuilder, D> {
		public Factory(WikiWordStoreFactory<? extends D> associationStoreFactory, FeatureMapping foreignMapping, FeatureMapping conceptMapping, FeatureMapping assocMapping) {
			super(associationStoreFactory, foreignMapping, conceptMapping, assocMapping);
			
			foreignMapping.assertAccessor(FOREIGN_AUTHORITY);
			foreignMapping.assertAccessor(FOREIGN_ID);
			if (!foreignMapping.hasAccessor(FOREIGN_NAME)) foreignMapping.addMapping(FOREIGN_NAME, foreignMapping.getAccessor(FOREIGN_ID));

			conceptMapping.assertAccessor(CONCEPT_ID);
			//CONCEPT_NAME is optional
			
			//all assoc mappings are optional
		}

		protected AssociationFeature2ConceptAssociationStoreBuilder newStore(D store) {
				return new AssociationFeature2ConceptAssociationStoreBuilder(
						store, foreignMapping, conceptMapping, assocMapping);
		}
	}
	
	public AssociationFeature2ConceptAssociationStoreBuilder(
			ConceptAssociationStoreBuilder store,
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
		String authority = foreignMapping.getValue(foreign, FOREIGN_AUTHORITY, String.class);
		String extId = foreignMapping.getValue(foreign, FOREIGN_ID, String.class);
		String extName = foreignMapping.getValue(foreign, FOREIGN_NAME, String.class);
		int conceptId = conceptMapping.getValue(concept, CONCEPT_ID, Integer.class);
		String name = conceptMapping.getValue(concept, CONCEPT_NAME, String.class);
		String foreignProperty = assocMapping.getValue(props, FOREIGN_PROPERTY, String.class);
		String conceptProperty = assocMapping.getValue(props, CONCEPT_PROPERTY, String.class);
		String conceptPropertySource = assocMapping.getValue(props, CONCEPT_PROPERTY_SOURCE, String.class);
		int conceptPropertyFreq = assocMapping.getValue(props, CONCEPT_PROPERTY_FREQ, Integer.class);
		double weight = assocMapping.getValue(props, ASSOCIATION_WEIGHT, Double.class);
		
		store.storeAssociation(authority, extId, extName, conceptId, name, foreignProperty, conceptProperty, conceptPropertySource, conceptPropertyFreq, weight);
	}
}

