package de.brightbyte.wikiword.extract;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.io.Writer;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public abstract class StreamProcessorApp<I, O, S extends WikiWordConceptStoreBase> extends StoreBackedApp<S> {

	protected DataCursor<? extends I> cursor;
	protected DataSink<? super O> sink;
	
	protected boolean usingStdin;
	protected boolean usingStdout;
	
	protected InputFileHelper inputHelper;	
	
	public StreamProcessorApp(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
	}

	
	protected File getOutputFile(int paramIndex) {
		if (outputFile==null) {
			if (args.getParameterCount()>=paramIndex) {
				String f = args.getParameter(paramIndex);
				if (!f.equals("-")) outputFile = new File(f);
			}
		}
		return outputFile;
	}

	protected String getInputPath(int paramIndex) {
		if (inputPath==null) {
			if (args.getParameterCount()>=paramIndex) {
				inputPath = args.getParameter(paramIndex);
			}
		}
		return inputPath;
	}

	protected String getOutputFileEncoding() {
		return args.getStringOption("outputencoding", "UTF-8");
	}

	protected String inputPath;
	protected File outputFile;
	protected Writer outputWriter;
	protected OutputStream outputStream;
	private InputStream inputStream;
	private Reader inputReader;
	
	protected Writer getOutputWriter(int paramIndex) throws FileNotFoundException, UnsupportedEncodingException {
		if (outputWriter==null) {
			File f = getOutputFile(paramIndex);
			if (f==null) {
				outputWriter = ConsoleIO.writer;
				usingStdout = true;
			} else {
				OutputStream out = getOutputStream(paramIndex);
				outputWriter = new OutputStreamWriter(out, getOutputFileEncoding());
				usingStdout = out == System.out;
			}
		}
		
		if (usingStdout && out.getOutput() == ConsoleIO.output) {
			out.setOutput(ConsoleIO.errorOutput);
		}
		
		return outputWriter;
	}
	
	protected OutputStream getOutputStream(int paramIndex) throws FileNotFoundException {
		if (outputStream==null) {
			File f = getOutputFile(paramIndex);
			if (f==null) {
				outputStream = System.out;
				usingStdout = true;
			} else {
				outputStream = new BufferedOutputStream(new FileOutputStream(f, args.isSet("append")));
				usingStdout = false;
				info("Writing output to "+f);
			}
		}
		
		return outputStream;
	}
	
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
	protected abstract DataSink<? super O> openSink(int paramIndex) throws PersistenceException;

	protected void init() throws Exception {
		// noop
	}
	protected void close() throws PersistenceException {
		sink.close();
	}

	public void runTransfer(DataCursor<? extends I> cursor) throws Exception {
		I rec;
		while ((rec = cursor.next()) != null) {
			//TODO: progress tracker
			process(rec);
		}
	}

	protected void commit(O rec) throws PersistenceException {
		sink.commit(rec);
	}
	
	protected abstract void process(I rec) throws Exception;
	
}
