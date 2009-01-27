package de.brightbyte.wikiword.builder;

import java.io.File;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.ConceptDescriptionStoreBuilder;
import de.brightbyte.wikiword.store.DatabaseConceptDescriptionStore;
import de.brightbyte.wikiword.store.StoreBuilder;

public class BuildConceptDescriptions extends ImportApp {

	public BuildConceptDescriptions() {
		super("BuildConceptDescriptions");
	}

	protected File getDatabaseFile() {
		return new File(args.getParameter(1));
	}

	protected void applyArguments() {
		//noop
	}
	
	protected String guessCorpusName() {
		return args.getParameter(0);
	}

	@Override
	protected void declareOptions() {
		args.declareHelp("<wiki-or-dump>", "name of the wiki/corpus to process");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the <wiki-or-dump> parameter)");
	}
	
	protected StoreBuilder createStore(DataSource db) throws PersistenceException {
		try {
			DatabaseConceptDescriptionStore store = new DatabaseConceptDescriptionStore(corpus, db, tweaks);
			return store;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	

	protected void run() throws Exception {
		out.println("-- purge --------------------------------------------------");
		if (agenda.shouldRun("purge")) store.purge();
		
		out.println("-- build cache --------------------------------------------------");
		((ConceptDescriptionStoreBuilder)this.store).buildConceptDescriptions();
	}	
	
	public static void main(String[] argv) throws Exception {
		BuildConceptDescriptions app = new BuildConceptDescriptions();
		app.launch(argv);
	}
}