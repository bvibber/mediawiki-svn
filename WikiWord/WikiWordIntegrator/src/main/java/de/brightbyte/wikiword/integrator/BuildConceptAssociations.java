package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.Functors;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.FeatureSets;
import de.brightbyte.wikiword.integrator.processor.ConceptAssociationPassThrough;
import de.brightbyte.wikiword.integrator.processor.ConceptAssociationProcessor;
import de.brightbyte.wikiword.integrator.store.AssociationFeature2ConceptAssociationStoreBuilder;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;
import de.brightbyte.wikiword.integrator.store.DatabaseConceptAssociationStoreBuilder;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

public class BuildConceptAssociations extends AbstractIntegratorApp<AssociationFeatureStoreBuilder, ConceptAssociationProcessor, Association> {
	
	@Override
	protected WikiWordStoreFactory<? extends AssociationFeatureStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		DatabaseConceptAssociationStoreBuilder.Factory mappingStoreFactory= new DatabaseConceptAssociationStoreBuilder.Factory(
				getTargetTableName(), 
				getConfiguredDataset(), 
				getConfiguredDataSource(), 
				tweaks);
		
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
	}
	
	@Override
	protected void run() throws Exception {
		AssociationFeatureStoreBuilder store = getStoreBuilder();
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
		String foreignIdField = sourceDescriptor.getTweak("foreign-id-field", (String)null);  //XXX: require!
		String conceptIdField = sourceDescriptor.getTweak("concept-id-field", (String)null); //XXX: require!

		String foreignProperty = sourceDescriptor.getTweak("foreign-property", null); //XXX: require!
		String foreignPropertyField = sourceDescriptor.getTweak("association-property-field", "property");
		String foreignValueField = sourceDescriptor.getTweak("association-value-field", "value");
		
		String associationProperty = sourceDescriptor.getTweak("association-property", (String)null);
		boolean terms = associationProperty == null; 
		
		String associationPropertyTable = sourceDescriptor.getTweak("association-property-table", terms ? "meaning" : "property");
		String associationPropertyField = sourceDescriptor.getTweak("association-property-field", terms ? null : "property");
		String associationValueField = sourceDescriptor.getTweak("association-value-field", terms ? "term_text" : "value");
		
		String fields = StringUtils.join(", ", getDefaultFields(dialect));
		String sql = "SELECT " + fields + " FROM " + dialect.quoteName(getQualifiedTableName(table)) + " as F ";
		sql += " JOIN " + dialect.quoteName(getQualifiedTableName(associationPropertyTable)) +" as P ";
		sql += " ON F."+ foreignValueField + " = P." +  associationValueField;
		if (foreignProperty!=null && foreignPropertyField!=null) sql += " AND " + foreignPropertyField + " = " + dialect.quoteString(foreignProperty);
		if (associationProperty!=null && associationPropertyField!=null) sql += " AND " + associationPropertyField + " = " + dialect.quoteString(associationProperty);
		
		return sql;
	}

	@Override
	protected ConceptAssociationProcessor createProcessor(AssociationFeatureStoreBuilder conceptStore) throws InstantiationException {
		//		FIXME: parameter list is restrictive, pass descriptor 
		return instantiate(sourceDescriptor, "conceptAssociationProcessorClass", ConceptAssociationPassThrough.class, conceptStore);
	}
	
	public static void main(String[] argv) throws Exception {
		BuildConceptAssociations app = new BuildConceptAssociations();
		app.launch(argv);
	}
}