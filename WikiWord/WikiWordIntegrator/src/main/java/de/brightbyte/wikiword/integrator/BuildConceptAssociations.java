package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.processor.ConceptAssociationPassThrough;
import de.brightbyte.wikiword.integrator.processor.ConceptAssociationProcessor;
import de.brightbyte.wikiword.integrator.store.ConceptAssociationStoreBuilder;
import de.brightbyte.wikiword.integrator.store.DatabaseConceptAssociationStoreBuilder;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

public class BuildConceptAssociations extends AbstractIntegratorApp<ConceptAssociationStoreBuilder, ConceptAssociationProcessor, Association> {
	
	@Override
	protected WikiWordStoreFactory<? extends ConceptAssociationStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		DatabaseConceptAssociationStoreBuilder.Factory mappingStoreFactory= new DatabaseConceptAssociationStoreBuilder.Factory(
				getTargetTableName(), 
				getConfiguredDataset(), 
				getConfiguredDataSource(), 
				tweaks);
		
		return mappingStoreFactory;
		
		/*
		FeatureSetSourceDescriptor sourceDescriptor = getSourceDescriptor();
		
		//TODO: make aggrregators/accessors configurable
		
		FeatureMapping fm = new FeatureMapping();
		fm.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.FOREIGN_AUTHORITY, sourceDescriptor, "foreign-authority-field", String.class, Functors.<String>firstElement());
		if (!fm.hasAccessor(AssociationFeature2ConceptAssociationStoreBuilder.FOREIGN_AUTHORITY)) fm.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.FOREIGN_AUTHORITY, FeatureSets.constantAccessor(sourceDescriptor.getAuthorityName()) );
		
		fm.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.FOREIGN_ID, sourceDescriptor, "foreign-id-field", String.class, Functors.<String>firstElement());
		fm.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.FOREIGN_NAME, sourceDescriptor, "foreign-name-field", String.class, Functors.<String>firstElement());

		FeatureMapping  cm = new FeatureMapping();
		cm.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.CONCEPT_ID, sourceDescriptor, "concept-id-field", Integer.class, Functors.<Integer>firstElement());
		cm.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.CONCEPT_NAME, sourceDescriptor, "concept-name-field", String.class, Functors.<String>firstElement());

		FeatureMapping am = new FeatureMapping();
		am.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.FOREIGN_PROPERTY, sourceDescriptor, "foreign-property-field", String.class, Functors.concat("|"));
		am.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.CONCEPT_PROPERTY, sourceDescriptor, "concept-property-field", String.class, Functors.concat("|"));
		am.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.CONCEPT_PROPERTY_SOURCE, sourceDescriptor, "concept-property-source-field", String.class, Functors.concat("|"));
		am.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.CONCEPT_PROPERTY_FREQ, sourceDescriptor, "concept-property-freq-field", Integer.class, Functors.Integer.sum);
		
		am.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.ASSOCIATION_VALUE, sourceDescriptor, "association-value-field", String.class, Functors.concat("|"));
		am.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.ASSOCIATION_WEIGHT, sourceDescriptor, "association-weight-field", Double.class, Functors.Integer.sum);
		
		return new AssociationFeature2ConceptAssociationStoreBuilder.Factory<DatabaseConceptAssociationStoreBuilder>(
				mappingStoreFactory,
				fm, cm, am
				);
				*/
	}
	
	@Override
	protected void run() throws Exception {
		ConceptAssociationStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> cursor = openAssociationCursor();
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processAssociations(cursor);
		cursor.close();

		store.finalizeImport();
	}	

	@Override
	protected String getSqlQuery(String table, FeatureSetSourceDescriptor sourceDescriptor, SqlDialect dialect) {
				return null;
	}

	@Override
	protected ConceptAssociationProcessor createProcessor(ConceptAssociationStoreBuilder conceptStore) throws InstantiationException {
		//		FIXME: parameter list is restrictive, pass descriptor 
		return instantiate(sourceDescriptor, "conceptAssociationProcessorClass", ConceptAssociationPassThrough.class, conceptStore);
	}
	
	public static void main(String[] argv) throws Exception {
		BuildConceptAssociations app = new BuildConceptAssociations();
		app.launch(argv);
	}
}