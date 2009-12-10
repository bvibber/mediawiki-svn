package de.brightbyte.wikiword.builder;

import java.io.IOException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.builder.ProximityStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordConceptStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildProximity extends ImportApp<WikiWordConceptStoreBuilder<? extends WikiWordConcept>> {

	protected ProximityStoreBuilder proximityStore;
	
	public BuildProximity() {
		super("BuildProximity", true, true);
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();
	}
	
	//protected WikiWordConceptStoreBuilder<?> conceptStore;
	
	@Override
	protected void createStores() throws IOException, PersistenceException {
		super.createStores();
		
		proximityStore = conceptStore.getProximityStoreBuilder();
		registerTargetStore(proximityStore);
	}

	@Override
	protected void run() throws Exception {
		section("-- build features --------------------------------------------------");
		this.proximityStore.buildFeatures();

		section("-- build proximity --------------------------------------------------");
		this.proximityStore.buildProximity();

		section("-- statistics --------------------------------------------------");
		conceptStore.getProximityStoreBuilder().dumpTableStats(out);
	}	
	
	public static void main(String[] argv) throws Exception {
		BuildProximity app = new BuildProximity();
		app.launch(argv);
	}
}