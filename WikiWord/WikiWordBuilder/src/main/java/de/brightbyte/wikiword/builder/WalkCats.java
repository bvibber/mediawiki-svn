package de.brightbyte.wikiword.builder;

import de.brightbyte.wikiword.store.builder.DatabaseWikiWordConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordConceptStoreBuilder;

public class WalkCats extends ImportApp {

	public WalkCats() {
		super(null, true, true);
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<wiki>", null);
		args.declareHelp("<wiki-or-thesaurus>", "name of the wiki/thesaurus to process");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the <wiki-or-dump> parameter)");
	}
	
	protected WikiWordConceptStoreBuilder<?> conceptStore;
	
	@Override
	protected void run() throws Exception {
		DatabaseWikiWordConceptStoreBuilder st = ((DatabaseWikiWordConceptStoreBuilder)this.conceptStore);
		
		section("-- deleting cycles --------------------------------------------------");
		long t = System.currentTimeMillis();
		long n = st.deleteBroaderCycles();
		t = System.currentTimeMillis() - t;
		
		info(n+" cycle broken in "+t/1000+" seconds");		
	}	
	
	public static void main(String[] argv) throws Exception {
		WalkCats app = new WalkCats();
		app.launch(argv);
	}
}