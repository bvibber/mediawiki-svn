package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.Functors;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.CollapsingMappingCandidateCursor;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.FeatureSets;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.processor.AssociationFeaturePassThrough;
import de.brightbyte.wikiword.integrator.processor.ConceptMappingProcessor;
import de.brightbyte.wikiword.integrator.store.AssociationFeature2ConceptAssociationStoreBuilder;
import de.brightbyte.wikiword.integrator.store.AssociationFeature2ConceptMappingStoreBuilder;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;
import de.brightbyte.wikiword.integrator.store.DatabaseConceptMappingStoreBuilder;

public class BuildConceptMappings extends AbstractIntegratorApp<AssociationFeatureStoreBuilder, ConceptMappingProcessor, MappingCandidates> {
	
	@Override
	protected  AssociationFeature2ConceptMappingStoreBuilder.Factory<DatabaseConceptMappingStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		DatabaseConceptMappingStoreBuilder.Factory mappingStoreFactory= new DatabaseConceptMappingStoreBuilder.Factory(
				getTargetTableName(), 
				getConfiguredDataset(), 
				getConfiguredDataSource(), 
				tweaks);
		
		FeatureSetSourceDescriptor sourceDescriptor = getSourceDescriptor();
		
		//TODO: make aggrregators/accessors configurable
		
		FeatureMapping fm = new FeatureMapping();
		fm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.FOREIGN_AUTHORITY, sourceDescriptor, "foreign-authority-field", String.class, Functors.<String>firstElement());
		if (!fm.hasAccessor(AssociationFeature2ConceptMappingStoreBuilder.FOREIGN_AUTHORITY)) fm.addMapping(AssociationFeature2ConceptAssociationStoreBuilder.FOREIGN_AUTHORITY, FeatureSets.constantAccessor(sourceDescriptor.getAuthorityName()) );

		fm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.FOREIGN_ID, sourceDescriptor, "foreign-id-field", String.class, Functors.<String>firstElement());
		fm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.FOREIGN_NAME, sourceDescriptor, "foreign-name-field", String.class, Functors.<String>firstElement());

		FeatureMapping  cm = new FeatureMapping();
		cm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.CONCEPT_ID, sourceDescriptor, "concept-id-field", Integer.class, Functors.<Integer>firstElement());
		cm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.CONCEPT_NAME, sourceDescriptor, "concept-name-field", String.class, Functors.<String>firstElement());

		FeatureMapping am = new FeatureMapping();
		am.addMapping(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_ANNOTATION, sourceDescriptor, "association-annotation-field", String.class, Functors.concat("|"));
		am.addMapping(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_WEIGHT, sourceDescriptor, "association-weight-field", Double.class, Functors.Double.sum);

		if (!am.hasAccessor(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_WEIGHT) 
				&& sourceDescriptor.getTweak("optimization-field", null)!=null) 
			am.addMapping(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_WEIGHT, sourceDescriptor, "optimization-field", Double.class, Functors.Double.sum);
		
		return new AssociationFeature2ConceptMappingStoreBuilder.Factory<DatabaseConceptMappingStoreBuilder>(
				mappingStoreFactory,
				fm, cm, am
				);
	}

	@Override
	protected void run() throws Exception {
		AssociationFeatureStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> asc = openAssociationCursor(); 

		DataCursor<MappingCandidates> cursor = 
			new CollapsingMappingCandidateCursor(asc, 
					sourceDescriptor.getTweak("foreign-id-field", (String)null), 
					sourceDescriptor.getTweak("concept-id-field", (String)null) );
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processMappings(cursor);
		cursor.close();

		store.finalizeImport();
	}	

	@Override
	protected String getSqlQuery(String table, FeatureSetSourceDescriptor sourceDescriptor, SqlDialect dialect) {
		String fields = StringUtils.join(", ", getDefaultFields(dialect));
		return "SELECT " + fields + " FROM " + dialect.quoteName(getQualifiedTableName(table)) ;
	}

	@Override
	protected ConceptMappingProcessor createProcessor(AssociationFeatureStoreBuilder conceptStore) throws InstantiationException {
		return instantiate(sourceDescriptor, "conceptMappingProcessorClass", AssociationFeaturePassThrough.class, conceptStore);
	}

	public static void main(String[] argv) throws Exception {
		BuildConceptMappings app = new BuildConceptMappings();
		app.launch(argv);
	}
}