package de.brightbyte.wikiword;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;

import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.DatabaseWikiWordStore;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.WikiWordLocalStore;
import de.brightbyte.wikiword.store.WikiWordStore;

/**
 * This is the base class for entry points to WikiWord.
 */
public abstract class StoreBackedApp<S extends WikiWordConceptStoreBase> extends CliApp {

	protected List<WikiWordStore> stores = new ArrayList<WikiWordStore>();
	protected S conceptStore;
	//protected DatasetIdentifier dataset;
	//protected DataSource dataSource;
	
	protected boolean allowLocalStore;
	protected boolean allowGlobalStore;
	
	public StoreBackedApp(boolean allowGlobal, boolean allowLocal) {
		super();
		
		allowGlobalStore = allowGlobal;
		allowLocalStore  = allowLocal;
	}
	
	
	protected void registerStore(WikiWordStore store) {
		if (!stores.contains(store)) stores.add(store);
	}
	
	protected Collection<WikiWordStore> getStores() {
		return stores;
	}
	
	public DatasetIdentifier getStoreDataset() {
		if (conceptStore==null) return null;
		return conceptStore.getDatasetIdentifier();
	}
	
	
	public boolean isDatasetLocal() {
		if (conceptStore!=null && conceptStore instanceof WikiWordLocalStore) return true;
		
		DatasetIdentifier dataset = getStoreDataset();
		if (dataset==null) dataset = getConfiguredDataset();
		
		if (dataset!=null && dataset instanceof Corpus) return true;
		return false;
	}
	
	public Corpus getCorpus() {
		DatasetIdentifier dataset = getStoreDataset();
		if (dataset==null) dataset = getConfiguredDataset();
		
		if (dataset==null) return null;
		if (!(dataset instanceof Corpus)) {
			if (conceptStore instanceof WikiWordLocalStore) {
				dataset = ((WikiWordLocalStore)conceptStore).getCorpus();
			}
			else {
				throw new IllegalStateException("this application uses a non-corpus dataset!");
			}
		}
		return (Corpus)dataset;
	}

	public DatasetIdentifier getConfiguredDataset() {
		DatasetIdentifier dataset;

		String c = getConfiguredCollectionName();
		String n = getConfiguredDatasetName();

		if (!allowGlobalStore) {
			if (!allowLocalStore) throw new RuntimeException("bad setup: nither global nor local stores allowed for this app!");
			dataset = Corpus.forName(c, n, tweaks);
		}
		else {
			dataset = DatasetIdentifier.forName(c, n, tweaks);
		}
		
		return dataset;
	}

	protected void openStores() throws PersistenceException {
		for (WikiWordStore store: stores) {
			store.open();
		}
	}

	protected void closeStores(boolean flush) throws PersistenceException {
		for (WikiWordStore store: stores) {
			store.close(flush);
		}
	}
	
	protected void dumpTableStats() throws PersistenceException {
		for (WikiWordStore store: stores) {
			store.dumpTableStats(getLogOutput());
		}
	}
	
	protected abstract void createStores() throws IOException, PersistenceException;

	protected void launchExecute() throws Exception {
		if (conceptStore==null) createStores();
		
		if (conceptStore==null) throw new RuntimeException("createStores() failed to initialize conceptStore");
		if (!stores.contains(conceptStore)) throw new RuntimeException("createStores() failed to call registerStore(conceptStore)");

		exitCode = 23;

		prepareApp();
		
		try {
			openStores();

			if (args.isSet("dbtest")) {
				section("-- db test --------------------------------------------------");
				Object o = args.getOption("dbtest", null); 
                String t;
                if (o instanceof String) {
                	t = (String)o;
                }
                else {
                	t = "\u00c4 \u00d6 \u00dc - \ud800\udf30";
                }

                String s = ((DatabaseSchema)((DatabaseWikiWordStore)conceptStore).getDatabaseAccess()).echo(t, args.getStringOption("dbcharset", "binary"));

                if (s.equals(t)) {
                        info("DB TEST OK: "+t);
                }
                else {
                		closeStores(true);
                        warn("DB TEST FAILED: "+t+" != "+s+" !");
                        exit(5);
                        return;
                }
			}
	
			execute();

			closeStores(true);
          
            section("-- DONE --------------------------------------------------");
			exitCode = 0;
			
		}
		catch (Throwable ex) {
			if (keepAlive) { //NOTE: if keepAlive is true, we are running as a slave. in that case, propagate any exceptions.
				if (ex instanceof Exception) throw (Exception)ex;
				else if (ex instanceof Error) throw (Error)ex;
				else new  Error("unexpected exception in app scope", ex);
			}
			
			error("uncaught throwable", ex);
			closeStores(false);
		}
		finally {
			finalizeApp();
			exit(exitCode);
		}		
	}
	
	protected void terminate() {
		info("terminating application");
	
		//XXX: note: flushing first would be nice, but 
		//     can deadlock, especially when called from a worker of the database's background flush queue!
		//     would need to detect that.
		//FIXME: just detect workewr thread before trying to join it
		/*
		try {
			closeStores(true);
		} catch (Exception ex) {
			error("failed to flush stores", ex);
			try {
				closeStores(false);
			} catch (Exception exx) {
				error("failed to close stores", exx);
			}
		} */
		
		try {
			closeStores(false);
		} catch (Exception exx) {
			error("failed to close stores", exx);
		}
		
		super.terminate();
	}
	
}
