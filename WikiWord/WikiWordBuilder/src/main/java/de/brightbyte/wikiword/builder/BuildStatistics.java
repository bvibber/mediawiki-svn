package de.brightbyte.wikiword.builder;

import javax.sql.DataSource;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.builder.StatisticsStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordConceptStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildStatistics extends ImportApp<StatisticsStoreBuilder> {

	public BuildStatistics() {
		super("BuildStatistics", true, true);
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();
	}
	
	protected WikiWordConceptStoreBuilder<?> conceptStore;
	
	@Override
	protected StatisticsStoreBuilder createStore(DataSource db) throws PersistenceException {
		conceptStore = createConceptStoreBuilder(db);
		return conceptStore.getStatisticsStoreBuilder();
	}

	@Override
	protected void run() throws Exception {
		section("-- buildstats --------------------------------------------------");
		this.store.buildStatistics();

		section("-- statistics --------------------------------------------------");
		conceptStore.getConceptStore().getStatisticsStore().dumpStatistics(getLogOutput());
	}	
	
	public static void main(String[] argv) throws Exception {
		BuildStatistics app = new BuildStatistics();
		app.launch(argv);
	}
}