package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.Functor;
import de.brightbyte.data.cursor.ConvertingCursor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.integrator.data.DefaultForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.Record;
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
		
		Functor<? extends ForeignEntityRecord, Record> converter = new DefaultForeignEntityRecord.FromRecord( sourceDescriptor.getAuthorityName(), sourceDescriptor.getPropertySubjectField(), sourceDescriptor.getPropertySubjectNameField() );
		DataCursor<ForeignEntityRecord> cursor = new ConvertingCursor<Record, ForeignEntityRecord>(fsc,  converter);
		
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