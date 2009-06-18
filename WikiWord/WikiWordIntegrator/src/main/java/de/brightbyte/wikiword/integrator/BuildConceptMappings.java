package de.brightbyte.wikiword.integrator;

import java.io.IOException;
import java.util.Collection;

import de.brightbyte.data.Functor;
import de.brightbyte.data.Functors;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.CollapsingMatchesCursor;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.processor.ConceptMappingProcessor;
import de.brightbyte.wikiword.integrator.processor.OptimalMappingSelector;
import de.brightbyte.wikiword.integrator.store.ConceptMappingStoreBuilder;
import de.brightbyte.wikiword.integrator.store.DatabaseConceptMappingStoreBuilder;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildConceptMappings extends AbstractIntegratorApp<ConceptMappingStoreBuilder, ConceptMappingProcessor, MappingCandidates> {
	
	@Override
	protected WikiWordStoreFactory<? extends ConceptMappingStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		return new DatabaseConceptMappingStoreBuilder.Factory(
				getTargetTableName(), 
				getConfiguredDataset(), 
				getConfiguredDataSource(), 
				tweaks);
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
		
		this.propertyProcessor = createProcessor(conceptStore, sourceDescriptor.getTweak("optiomization-field", "freq"), aggregator); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> asc = openAssociationCursor(); 

		DataCursor<MappingCandidates> cursor = 
			new CollapsingMatchesCursor(asc, 
					sourceDescriptor.getTweak("foreign-id-field", (String)null), 
					sourceDescriptor.getTweak("concept-id-field", (String)null) );
		
		section("-- process properties --------------------------------------------------");
		this.conceptStore.prepareImport();
		
		this.propertyProcessor.processMappings(cursor);
		cursor.close();

		this.conceptStore.finalizeImport();
	}	

	@Override
	protected ConceptMappingProcessor createProcessor(ConceptMappingStoreBuilder conceptStore) throws InstantiationException {
		//FIXME: parameter list is specific to  OptimalMappingSelector
		throw new UnsupportedOperationException("not supported");
	}
	
	protected ConceptMappingProcessor createProcessor(ConceptMappingStoreBuilder conceptStore, String property, Functor<Number, ? extends Collection<? extends Number>> aggregator) throws InstantiationException {
		//FIXME: parameter list is specific to  OptimalMappingSelector
		return instantiate(sourceDescriptor, "conceptMappingProcessorClass", OptimalMappingSelector.class, conceptStore, property, aggregator);
	}
	
	public static void main(String[] argv) throws Exception {
		BuildConceptMappings app = new BuildConceptMappings();
		app.launch(argv);
	}
}