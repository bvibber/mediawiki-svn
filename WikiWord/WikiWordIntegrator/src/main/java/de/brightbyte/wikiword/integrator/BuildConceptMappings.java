package de.brightbyte.wikiword.integrator;

import java.io.IOException;
import java.math.BigDecimal;
import java.math.BigInteger;
import java.util.Collection;
import java.util.Comparator;

import de.brightbyte.data.Functor;
import de.brightbyte.data.Functors;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.CollapsingMatchesCursor;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.FeatureSets;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.processor.ConceptMappingProcessor;
import de.brightbyte.wikiword.integrator.processor.OptimalMappingSelector;
import de.brightbyte.wikiword.integrator.store.AssociationFeature2ConceptAssociationStoreBuilder;
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
		if (!am.hasAccessor(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_WEIGHT)) am.addMapping(AssociationFeature2ConceptMappingStoreBuilder.ASSOCIATION_WEIGHT, sourceDescriptor, "optimization-field", Double.class, Functors.Double.sum);
		
		return new AssociationFeature2ConceptMappingStoreBuilder.Factory<DatabaseConceptMappingStoreBuilder>(
				mappingStoreFactory,
				fm, cm, am
				);
	}

	@Override
	protected void run() throws Exception {
		Functor<Number, ? extends Collection<? extends Number>> aggregator = sourceDescriptor.getTweak("optimization-aggregator", null);
		
		if (aggregator==null) {
			String f = sourceDescriptor.getTweak("optimization-aggregator-function", "sum");
			if (f.equals("sum")) aggregator = Functors.Double.sum;
			else if (f.equals("max")) aggregator = Functors.Double.max;
			else throw new IllegalArgumentException("unknwon aggregator function: "+f);
		}
		
		Class<? extends Number> type = sourceDescriptor.getTweak("optimization-class", null);

		if (type==null) {
			String c = sourceDescriptor.getTweak("optimization-type", "double");
			if (c.equals("double")) type = Double.class;
			else if (c.equals("int")) type = Integer.class;
			else if (c.equals("long")) type = Long.class;
			else if (c.equals("bigint")) type = BigInteger.class;
			else if (c.equals("decimal") || c.equals("bigdecimal")) type = BigDecimal.class;
			else throw new IllegalArgumentException("unknwon comparator type: "+c);
		}
		
		Comparator<? extends Number> comp = sourceDescriptor.getTweak("optimization-comparator", null);

		if (comp==null) {
			if (type==Double.class) comp = Functors.Double.comparator;
			else if (type==Integer.class) comp = Functors.Integer.comparator;
			else if (type==Long.class) comp = Functors.Long.comparator;
			else if (type==BigInteger.class) comp = Functors.BigInteger.comparator;
			else if (type==BigDecimal.class) comp = Functors.BigDecimal.comparator;
			else throw new IllegalArgumentException("unknwon comparator function: "+type);
		}
		
		AssociationFeatureStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store, sourceDescriptor.getTweak("optimization-field", "freq"), aggregator, comp, type); 

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
	
	protected ConceptMappingProcessor createProcessor(AssociationFeatureStoreBuilder conceptStore, String property, Functor<? extends Number, ? extends Collection<? extends Number>> aggregator, Comparator<? extends Number> comp, Class<? extends Number> type) throws InstantiationException {
		//FIXME: parameter list is specific to  OptimalMappingSelector
		return instantiate(sourceDescriptor, "conceptMappingProcessorClass", OptimalMappingSelector.class, conceptStore, property, aggregator, comp, type);
	}
	
	public static void main(String[] argv) throws Exception {
		BuildConceptMappings app = new BuildConceptMappings();
		app.launch(argv);
	}
}