package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.ForeignEntityFeatureSetCursor;
import de.brightbyte.wikiword.integrator.processor.ForeignPropertyPassThrough;
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
		DataCursor<FeatureSet> fsc = openFeatureSetCursor();
		DataCursor<ForeignEntityRecord> cursor = new ForeignEntityFeatureSetCursor(fsc, sourceDescriptor.getAuthorityName(), sourceDescriptor.getPropertySubjectField(), sourceDescriptor.getPropertySubjectNameField());
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processEntites(cursor);
		cursor.close();

		store.finalizeImport();
	}	

	@Override
	protected String getSqlQuery(String table, FeatureSetSourceDescriptor sourceDescriptor, SqlDialect dialect) {
		String fields = StringUtils.join(", ", getDefaultFields(dialect));
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