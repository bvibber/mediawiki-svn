package de.brightbyte.wikiword.extract;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public abstract class StreamProcessorApp<I, O, S extends WikiWordConceptStoreBase> extends StoreBackedApp<S> {

	protected DataCursor<I> cursor;
	protected DataSink<O> sink;
	
	public StreamProcessorApp(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
	}

	@Override
	public void run() throws Exception {
		init();
		open();
		
		runTransfer(cursor);
		
		close();
	}

	protected void open() {
		cursor = openCursor();
		sink = openSink();
	}

	protected abstract DataCursor<I> openCursor();
	protected abstract DataSink<O> openSink();

	protected void init() throws Exception {
		// noop
	}
	protected void close() throws PersistenceException {
		sink.close();
	}

	public void runTransfer(DataCursor<I> cursor) throws Exception {
		I rec;
		while ((rec = cursor.next()) != null) {
			//TODO: progress tracker
			O res = process(rec);
			if (res!=null) sink.commit(res);
		}
	}

	protected abstract O process(I rec) throws Exception;
	
}
