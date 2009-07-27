package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.cursor.ConvertingCursor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.integrator.data.DefaultForeignEntityFeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureBuilder;
import de.brightbyte.wikiword.integrator.data.FeatureBuilderCursor;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.ForeignEntityFeatureSet;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.PropertyMappingFeatureBuilder;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.integrator.data.TriplifiedPropertyFeatureBuilder;
import de.brightbyte.wikiword.integrator.processor.ForeignEntityProcessor;
import de.brightbyte.wikiword.integrator.processor.ForeignPropertyPassThrough;
import de.brightbyte.wikiword.integrator.store.DatabaseForeignPropertyStoreBuilder;
import de.brightbyte.wikiword.integrator.store.ForeignPropertyStoreBuilder;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

public class LoadForeignProperties extends AbstractIntegratorApp<ForeignPropertyStoreBuilder, ForeignEntityProcessor, ForeignEntityRecord> {
	
	@Override
	protected WikiWordStoreFactory<? extends ForeignPropertyStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		return new DatabaseForeignPropertyStoreBuilder.Factory(getTargetTableName(), getConfiguredDataset(), getConfiguredDataSource(), tweaks);
	}

	@Override
	protected void run() throws Exception {
		ForeignPropertyStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store); //FIXME
		
		section("-- fetching properties --------------------------------------------------");
		DataCursor<Record> fsc = openRecordCursor();
		
		FeatureBuilder<Record> builder = sourceDescriptor.getFeatureBuilder();
		
		String authorityField = sourceDescriptor.getAuthorityField();
		String authorityFieldName = authorityField;
		if (authorityField==null) {
			authorityField = "=" + sourceDescriptor.getAuthorityName();
			authorityFieldName = "authority";
		}

		if (builder!=null) ; //noop
		else {
			if (sourceDescriptor.getPropertyNameField()!=null) {
				builder =  new TriplifiedPropertyFeatureBuilder<Record>( 
						authorityFieldName, 
						sourceDescriptor.getPropertySubjectField(),
						sourceDescriptor.getPropertyNameField(),
						sourceDescriptor.getPropertyValueField()); 
				
				//FIXME: mapping/builder for qualifiers
				((PropertyMappingFeatureBuilder<Record>)builder).addMapping(authorityFieldName, new Record.Accessor<String>(authorityField, String.class), null);
				((PropertyMappingFeatureBuilder<Record>)builder).addMapping(sourceDescriptor.getPropertySubjectField(), new Record.Accessor<String>(sourceDescriptor.getPropertySubjectField(), String.class), null);
				((PropertyMappingFeatureBuilder<Record>)builder).addMapping(sourceDescriptor.getPropertyNameField(), new Record.Accessor<String>(sourceDescriptor.getPropertyNameField(), String.class), null);
				((PropertyMappingFeatureBuilder<Record>)builder).addMapping(sourceDescriptor.getPropertyValueField(), new Record.Accessor<String>(sourceDescriptor.getPropertyValueField(), String.class), null);
			} else {
				builder = new PropertyMappingFeatureBuilder<Record>( 
						authorityFieldName, 
						sourceDescriptor.getPropertySubjectField() ); 
				
				//FIXME: mapping/builder for qualifiers
				((PropertyMappingFeatureBuilder<Record>)builder).addMapping(authorityFieldName, new Record.Accessor<String>(authorityField, String.class), null);
				((PropertyMappingFeatureBuilder<Record>)builder).addMapping(sourceDescriptor.getPropertySubjectField(), new Record.Accessor<String>(sourceDescriptor.getPropertySubjectField(), String.class), null);
				
				for (String f: recordFields) {
					if (sourceDescriptor.getAuthorityField()!=null && f.equals(sourceDescriptor.getAuthorityField()!=null)) continue;
					if (sourceDescriptor.getPropertySubjectField()!=null && f.equals(sourceDescriptor.getPropertySubjectField()!=null)) continue;
					
					((PropertyMappingFeatureBuilder<Record>)builder).addMapping(f, new Record.Accessor<Object>(f, Object.class), null);
				}
			}
		}
				
		DefaultForeignEntityFeatureSet.FromFeatureSet converter = new DefaultForeignEntityFeatureSet.FromFeatureSet(
				authorityFieldName, 
				sourceDescriptor.getPropertySubjectField(),
				sourceDescriptor.getPropertySubjectNameField()
				);
		
		DataCursor<ForeignEntityFeatureSet> cursor = new ConvertingCursor<FeatureSet, ForeignEntityFeatureSet>( new FeatureBuilderCursor<Record>(fsc,  builder), converter );
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processEntites(cursor);
		cursor.close();

		store.finalizeImport();
	}	

	@Override
	protected String getSqlQuery(String table, FeatureSetSourceDescriptor sourceDescriptor, SqlDialect dialect) {
		String fields = StringUtils.join(", ", getDefaultFields().values());
		return "SELECT " + fields + " FROM " + dialect.quoteQualifiedName(getQualifiedTableName(table));
	}

	@Override
	protected ForeignEntityProcessor createProcessor(ForeignPropertyStoreBuilder conceptStore) throws InstantiationException {
		ForeignEntityProcessor processor = instantiate(sourceDescriptor, "foreignPropertyProcessorClass", ForeignPropertyPassThrough.class, conceptStore);

		if (processor instanceof ForeignPropertyPassThrough) {
			String qualifier = sourceDescriptor.getTweak("property-qualifier", null);
			if (qualifier!=null) ((ForeignPropertyPassThrough)processor).setQualifier(qualifier);
		}
		
		return processor;
	}

	public static void main(String[] argv) throws Exception {
		LoadForeignProperties app = new LoadForeignProperties();
		app.launch(argv);
	}
}