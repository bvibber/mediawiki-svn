package de.brightbyte.wikiword.extract;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.io.Writer;

import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public abstract class StreamOutputApp<O, S extends WikiWordConceptStoreBase> extends StoreBackedApp<S> {

	protected DataSink<? super O> sink;
	
	protected boolean usingStdout;
	
	public StreamOutputApp(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
	}

	
	protected File getOutputFile(int paramIndex) {
		if (outputFile==null) {
			if (args.getParameterCount()>paramIndex) {
				String f = args.getParameter(paramIndex);
				if (!f.equals("-")) outputFile = new File(f);
			}
		}
		return outputFile;
	}

	protected String getOutputFileEncoding() {
		return args.getStringOption("output-encoding", "UTF-8");
	}

	protected void declareOptions() {
		super.declareOptions();
		
		args.declare("output-encoding", null, true, String.class, "Encoding to use for the poutput file");
	}
	
	protected File outputFile;
	protected Writer outputWriter;
	protected OutputStream outputStream;
	
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
	
	
	protected void open(int paramOffset) throws PersistenceException {
		sink = openSink(paramOffset);
	}

	protected abstract DataSink<? super O> openSink(int paramIndex) throws PersistenceException;

	protected void init() throws Exception {
		// noop
	}
	
	protected void close() throws PersistenceException {
		sink.close();
	}

	protected void commit(O rec) throws PersistenceException {
		sink.commit(rec);
	}
	
}
