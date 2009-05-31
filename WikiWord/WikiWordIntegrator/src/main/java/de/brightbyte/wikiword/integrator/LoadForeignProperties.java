package de.brightbyte.wikiword.integrator;

import java.io.IOException;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.builder.ImportApp;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;
import de.brightbyte.wikiword.integrator.processor.ForeignPropertyProcessor;
import de.brightbyte.wikiword.integrator.store.DatabaseForeignPropertyStoreBuilder;
import de.brightbyte.wikiword.integrator.store.ForeignPropertyStoreBuilder;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.ConceptInfoStoreBuilder;
import de.brightbyte.wikiword.store.builder.DatabaseConceptStoreBuilders;
import de.brightbyte.wikiword.store.builder.WikiWordConceptStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class LoadForeignProperties extends StoreBackedApp<ForeignPropertyStoreBuilder> {

	protected ForeignPropertyStoreBuilder propertyStore;
	protected ForeignPropertyProcessor propertyProcessor;
	
	public LoadForeignProperties() {
		super(true, true);
	}
	
	@Override
	protected WikiWordStoreFactory<? extends ForeignPropertyStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		return new DatabaseForeignPropertyStoreBuilder.Factory(getTargetTableName(), getConfiguredDataset(), getConfiguredDataSource(), tweaks);
	}

	private String getTargetTableName() {
		return args.getParameterCount() > 3 ? args.getParameter(3) : "foreign_property";
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<wiki>", null);
		args.declareHelp("<dataset>", "name of the wiki/thesaurus to process");
		args.declare("dataset", null, true, String.class, "sets the wiki name (overrides the <wiki-or-dump> parameter)");
	}
	
	@Override
	protected void run() throws Exception {
		section("-- fetching properties --------------------------------------------------");
		DataCursor<ForeignEntity> cursor = openPropertySource();
		
		section("-- process properties --------------------------------------------------");
		this.propertyProcessor.processProperties(cursor);
		
		cursor.close();
	}	
	
	protected DataCursor<ForeignEntity> openPropertySource() {
		// TODO Auto-generated method stub
		return null;
	}

	public static void main(String[] argv) throws Exception {
		LoadForeignProperties app = new LoadForeignProperties();
		app.launch(argv);
	}
}