package de.brightbyte.wikiword.extract;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public abstract class StreamProcessorApp<I, O, S extends WikiWordConceptStoreBase> extends StreamOutputApp<O, S> {

	protected DataCursor<? extends I> cursor;
	
	protected boolean usingStdin;
	protected InputFileHelper inputHelper;	
	
	public StreamProcessorApp(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
	}
	
	protected String getInputPath(int paramIndex) {
		if (inputPath==null) {
			if (args.getParameterCount()>paramIndex) {
				inputPath = args.getParameter(paramIndex);
			}
		}
		return inputPath;
	}

	protected String inputPath;
	private InputStream inputStream;
	private Reader inputReader;
	
	
	protected Reader getInputReader(int paramIndex) throws IOException {
		if (inputReader==null) {
			String path = getInputPath(paramIndex);
			if (path==null || path.equals("-")) {
				inputReader = ConsoleIO.newReader();
				usingStdin = true;
			} else {
				InputStream in = getInputStream(paramIndex);
				inputReader = new InputStreamReader(in, getOutputFileEncoding());
				usingStdin = (in == System.in);
			}
		}
		
		return inputReader;
	}
	
	protected InputStream getInputStream(int paramIndex) throws IOException {
		if (inputStream==null) {
			String path = getInputPath(paramIndex);
			if (path==null || path.equals("-")) {
				inputStream = System.in;
				usingStdin = true;
			} else {
				if (inputHelper==null) inputHelper = new InputFileHelper(tweaks);
				inputStream = inputHelper.open(path);
				info("Reading input from "+path);
				usingStdin = false;
			}
		}
		
		return inputStream;
	}
	
	@Override
	public void run() throws Exception {
		init();
		open(1); // 0 = corpus, 1 = input, 2 = output
		
		runTransfer(cursor);
		
		close();
	}

	protected void open(int paramOffset) throws PersistenceException {
		cursor = openCursor(paramOffset);
		sink = openSink(paramOffset+1);
	}

	protected abstract DataCursor<? extends I> openCursor(int paramIndex) throws PersistenceException;

	public void runTransfer(DataCursor<? extends I> cursor) throws Exception {
		I rec;
		while ((rec = cursor.next()) != null) {
			//TODO: progress tracker
			process(rec);
		}
	}

	protected abstract void process(I rec) throws Exception;
	
}
