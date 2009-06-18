package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.processor.ConceptAssociationPassThrough;
import de.brightbyte.wikiword.integrator.processor.ConceptAssociationProcessor;
import de.brightbyte.wikiword.integrator.store.AssociationAsMappingStoreBuilder;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;
import de.brightbyte.wikiword.integrator.store.DatabaseConceptMappingStoreBuilder;
import de.brightbyte.wikiword.integrator.store.DatabaseConceptMappingStoreBuilder.Factory;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildConceptAssociations extends AbstractIntegratorApp<AssociationFeatureStoreBuilder, ConceptAssociationProcessor, Association> {
	
	@Override
	protected WikiWordStoreFactory<? extends AssociationFeatureStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		Factory mappingStoreFactory= new DatabaseConceptMappingStoreBuilder.Factory(
				getTargetTableName(), 
				getConfiguredDataset(), 
				getConfiguredDataSource(), 
				tweaks);
		
		FeatureSetSourceDescriptor sourceDescriptor = getSourceDescriptor();
		
		return new AssociationAsMappingStoreBuilder.Factory<DatabaseConceptMappingStoreBuilder>(
				mappingStoreFactory,
				sourceDescriptor.getTweak("authority-name", "=" + sourceDescriptor.getAuthorityName()),
				sourceDescriptor.getTweak("foreign-id-field", (String)null),
				sourceDescriptor.getTweak("foreign-name-field", (String)null),
				sourceDescriptor.getTweak("concept-id-field", (String)null),
				sourceDescriptor.getTweak("concept-name-field", (String)null),
				sourceDescriptor.getTweak("association-via-field", (String)null),
				sourceDescriptor.getTweak("association-weight-field", (String)null)
				);
	}

	@Override
	protected void run() throws Exception {
		this.propertyProcessor = createProcessor(conceptStore); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> cursor = openAssociationCursor();
		
		section("-- process properties --------------------------------------------------");
		this.conceptStore.prepareImport();
		
		this.propertyProcessor.processAssociations(cursor);
		cursor.close();

		this.conceptStore.finalizeImport();
	}	

	@Override
	protected ConceptAssociationProcessor createProcessor(AssociationFeatureStoreBuilder conceptStore) throws InstantiationException {
		//		FIXME: parameter list is restrictive, pass descriptor 
		return instantiate(sourceDescriptor, "conceptAssociationProcessorClass", ConceptAssociationPassThrough.class, conceptStore);
	}
	
	public static void main(String[] argv) throws Exception {
		LoadForeignProperties app = new LoadForeignProperties();
		app.launch(argv);
	}
}