package de.brightbyte.wikiword.integrator;

import java.io.IOException;
import java.util.Collection;

import de.brightbyte.data.Functor;
import de.brightbyte.data.Functors;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.CollapsingMatchesCursor;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.processor.ConceptMappingProcessor;
import de.brightbyte.wikiword.integrator.processor.OptimalMappingSelector;
import de.brightbyte.wikiword.integrator.store.AssociationFeature2ConceptMappingStoreBuilder;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;
import de.brightbyte.wikiword.integrator.store.DatabaseConceptMappingStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildConceptMappings extends AbstractIntegratorApp<AssociationFeatureStoreBuilder, ConceptMappingProcessor, MappingCandidates> {
	
	@Override
	protected  AssociationFeature2ConceptMappingStoreBuilder.Factory<DatabaseConceptMappingStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		DatabaseConceptMappingStoreBuilder.Factory mappingStoreFactory= new DatabaseConceptMappingStoreBuilder.Factory(
				getTargetTableName(), 
				getConfiguredDataset(), 
				getConfiguredDataSource(), 
				tweaks);
		
		FeatureSetSourceDescriptor sourceDescriptor = getSourceDescriptor();
		
		FeatureMapping fm = new FeatureMapping();
		fm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.FOREIGN_AUTHORITY, sourceDescriptor, "authority-name-field", "=" + sourceDescriptor.getAuthorityName(), String.class);
		fm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.FOREIGN_ID, sourceDescriptor, "foreign-id-field", null, String.class);
		fm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.FOREIGN_NAME, sourceDescriptor, "foreign-name-field", null, String.class);

		FeatureMapping  cm = new FeatureMapping();
		cm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.CONCEPT_ID, sourceDescriptor, "concept-id-field", null, Integer.class);
		cm.addMapping(AssociationFeature2ConceptMappingStoreBuilder.CONCEPT_NAME, sourceDescriptor, "concept-name-field", null, String.class);

		FeatureMapping am = new FeatureMapping();
		am.addMapping(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_ANNOTATION, sourceDescriptor, "association-annotation-field", null, String.class);
		am.addMapping(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_WEIGHT, sourceDescriptor, "association-weight-field", null, Double.class);
		
		return new AssociationFeature2ConceptMappingStoreBuilder.Factory<DatabaseConceptMappingStoreBuilder>(
				mappingStoreFactory,
				fm, cm, am
				);
	}

	@Override
	protected void run() throws Exception {
		Functor<Number, ? extends Collection<? extends Number>> aggregator = sourceDescriptor.getTweak("optiomization-aggregator", null);
		
		if (aggregator==null) {
			String f = sourceDescriptor.getTweak("optiomization-function", "sum");
			if (f.equals("sum")) aggregator = Functors.Double.sum;
			else if (f.equals("max")) aggregator = Functors.Double.max;
			else throw new IllegalArgumentException("unknwon aggregator function: "+f);
		}
		
		AssociationFeatureStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store, sourceDescriptor.getTweak("optiomization-field", "freq"), aggregator); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> asc = openAssociationCursor(); 

		DataCursor<MappingCandidates> cursor = 
			new CollapsingMatchesCursor(asc, 
					sourceDescriptor.getTweak("foreign-id-field", (String)null), 
					sourceDescriptor.getTweak("concept-id-field", (String)null) );
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processMappings(cursor);
		cursor.close();

		store.finalizeImport();
	}	

	@Override
	protected ConceptMappingProcessor createProcessor(AssociationFeatureStoreBuilder conceptStore) throws InstantiationException {
		//FIXME: parameter list is specific to  OptimalMappingSelector
		throw new UnsupportedOperationException("not supported");
	}
	
	protected ConceptMappingProcessor createProcessor(AssociationFeatureStoreBuilder conceptStore, String property, Functor<Number, ? extends Collection<? extends Number>> aggregator) throws InstantiationException {
		//FIXME: parameter list is specific to  OptimalMappingSelector
		return instantiate(sourceDescriptor, "conceptMappingProcessorClass", OptimalMappingSelector.class, conceptStore, property, aggregator);
	}
	
	public static void main(String[] argv) throws Exception {
		BuildConceptMappings app = new BuildConceptMappings();
		app.launch(argv);
	}
}