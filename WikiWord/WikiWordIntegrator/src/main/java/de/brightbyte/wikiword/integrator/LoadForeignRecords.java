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
import de.brightbyte.wikiword.integrator.processor.ForeignRecordPassThrough;
import de.brightbyte.wikiword.integrator.store.DatabaseForeignRecordStoreBuilder;
import de.brightbyte.wikiword.integrator.store.ForeignRecordStoreBuilder;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

public class LoadForeignRecords extends AbstractIntegratorApp<ForeignRecordStoreBuilder, ForeignEntityProcessor, ForeignEntityRecord> {
	
	@Override
	protected WikiWordStoreFactory<? extends ForeignRecordStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		return new DatabaseForeignRecordStoreBuilder.Factory(getTargetTableName(), getConfiguredDataset(), getConfiguredDataSource(), tweaks);
	}

	@Override
	protected void run() throws Exception {
		ForeignRecordStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store); //FIXME
		
		section("-- fetching properties --------------------------------------------------");
		DataCursor<Record> fsc = openRecordCursor();

		Functor<? extends ForeignEntityRecord, Record> converter = new DefaultForeignEntityRecord.FromRecord( sourceDescriptor.getAuthorityField(), sourceDescriptor.getPropertySubjectField(), sourceDescriptor.getPropertySubjectNameField() );
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
	protected ForeignEntityProcessor createProcessor(ForeignRecordStoreBuilder conceptStore) throws InstantiationException {
		//	FIXME: need to pass mappings (aggregators) 
		ForeignEntityProcessor processor = instantiate(sourceDescriptor, "foreignRecordProcessorClass", ForeignRecordPassThrough.class, conceptStore);

		return processor;
	}

	public static void main(String[] argv) throws Exception {
		LoadForeignRecords app = new LoadForeignRecords();
		app.launch(argv);
	}
}