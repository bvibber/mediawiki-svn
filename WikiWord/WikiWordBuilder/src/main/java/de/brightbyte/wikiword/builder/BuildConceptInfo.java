package de.brightbyte.wikiword.builder;

import java.io.IOException;

import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.data.cursor.DataCursor;
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
		section("-- build concept property cache --------------------------------------------------");
		this.infoStore.buildConceptInfo();

		section("-- build concept feature vector cache --------------------------------------------------");
		if (agenda.beginTask("buildConceptInfo", "buildConceptFeatureVectors")) {
			   //TODO: cleanup incomplete run
				buildConceptFeatureVectors();
				agenda.endTask("buildConceptInfo", "buildConceptFeatureVectors");
		}
	}	
	
	protected FeatureFetcher<C> featureFetcher;
	
	private void buildConceptFeatureVectors() {
		CursorProcessor<C> p = new CursorProcessor<C>() {
		
			public void process(DataCursor<C> c) throws Exception {
				
				C r;
				while ((r = c.next())!=null) {
					ConceptFeatures<C> features = featureFetcher.getFeatures(r);
					infoStore.storeConceptFeatures(features);
				}
				
				infoStore.flush();
			}
		
		};
		
		conceptStore.processConcepts(p);
	}

	public static void main(String[] argv) throws Exception {
		BuildConceptInfo app = new BuildConceptInfo();
		app.launch(argv);
	}
}