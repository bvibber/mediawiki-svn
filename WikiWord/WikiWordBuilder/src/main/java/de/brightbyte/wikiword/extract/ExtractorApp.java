package de.brightbyte.wikiword.extract;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.io.Writer;

import de.brightbyte.io.ConsoleIO;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.output.DataOutput;

/**
 * This is the base class for entry points to WikiWord.
 */
public abstract class ExtractorApp<S extends DataOutput> extends CliApp {
	
	protected S output;
	protected InputFileHelper inputHelper;	
	
	public ExtractorApp() { 
		super();
	}
		
	@Override
	protected void declareOptions() {
		super.declareOptions();

		//FIXME: encoding!
		//FIXME: output file

		args.declareHelp("<wiki>", "the wiki's domain or short name");
		args.declareHelp("<outfile>", "the file to write to");
		args.declare("wiki", null, true, String.class, "sets the wiki name");
		args.declare("append", null, false, Boolean.class, "append to output file");
		args.declare("outputencoding", null, true, String.class, "sets the output encoding (defaults to UTF-8)");
	}
	
	protected abstract S createOutput() throws PersistenceException;
	
	protected File getOutputFile() {
		if (outputFile==null) {
			if (args.getParameterCount()>2) {
				outputFile = new File(args.getParameter(2));
			}
		}
		return outputFile;
	}

	protected String getOutputFileEncoding() {
		return args.getStringOption("outputencoding", "UTF-8");
	}

	protected File outputFile;
	protected Writer outputWriter;
	protected OutputStream outputStream;
	
	protected Writer getOutputWriter() throws FileNotFoundException, UnsupportedEncodingException {
		if (outputWriter==null) {
			File f = getOutputFile();
			if (f==null) outputWriter = ConsoleIO.writer;
			else outputWriter = new OutputStreamWriter(getOutputStream(), getOutputFileEncoding());
		}
		
		return outputWriter;
	}
	
	protected OutputStream getOutputStream() throws FileNotFoundException {
		if (outputStream==null) {
			File f = getOutputFile();
			if (f==null) outputStream = System.out;
			else {
				outputStream = new BufferedOutputStream(new FileOutputStream(f, args.isSet("append")));
				info("Writing output to "+f);
			}
		}
		
		return outputStream;
	}

	@Override
	protected void prepareApp() throws Exception {
		super.prepareApp();
		
		inputHelper = new InputFileHelper(
				tweaks.getTweak("dumpdriver.externalGunzip", tweaks.getTweak("input.externalGunzip", (String)null)),
				tweaks.getTweak("dumpdriver.externalBunzip", tweaks.getTweak("input.externalBunzip", (String)null)));
		
		output = createOutput();
	}

	/*
	@Override
	protected void execute() throws Exception {
				run();
	}*/


}
