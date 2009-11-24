package de.brightbyte.wikiword.builder;

import java.io.IOException;

import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.io.BlockCodec;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.ConceptFeatures;
import de.brightbyte.wikiword.disambig.FeatureFetcher;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.store.builder.ConceptInfoStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordConceptStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class BuildConceptInfo<C extends WikiWordConcept> extends ImportApp<WikiWordConceptStoreBuilder<C>> {

	protected ConceptInfoStoreBuilder<C> infoStore;
	
	public BuildConceptInfo() {
		super("BuildConceptInfo", true, true);
	}
	
	@Override
	protected void createStores() throws IOException, PersistenceException {
		super.createStores();
		
		infoStore = conceptStore.getConceptInfoStoreBuilder();
		registerTargetStore(infoStore);
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
		section("-- build concept relation cache --------------------------------------------------");
		this.infoStore.buildConceptRelationCache();
		
		if (isDatasetLocal()) {
			section("-- build concept description cache --------------------------------------------------");
			this.infoStore.buildConceptDescriptionCache();
		}
		
		section("-- build concept feature cache --------------------------------------------------");
		this.infoStore.buildConceptFeatureCache();
		
		section("-- build concept proximity cache --------------------------------------------------");
		this.infoStore.buildConceptProximetyCache();
	}	
	
	protected FeatureFetcher<C, Integer> featureFetcher;
	protected BlockCodec<FeatureFetcher<C, Integer>> featureCodec;
	
	public static void main(String[] argv) throws Exception {
		BuildConceptInfo app = new BuildConceptInfo();
		app.launch(argv);
	}
}