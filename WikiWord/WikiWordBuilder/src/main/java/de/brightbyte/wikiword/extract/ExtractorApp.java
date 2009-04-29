package de.brightbyte.wikiword.extract;

import java.io.OutputStream;
import java.io.Writer;

import de.brightbyte.io.ConsoleIO;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.output.DataOutput;

/**
 * This is the base class for entry points to WikiWord.
 */
public abstract class ExtractorApp<S extends DataOutput> extends CliApp {
	
	protected S output;
	
	public ExtractorApp() { 
		super();
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		//FIXME: encoding!
		//FIXME: output file

		args.declareHelp("<wiki>", "the wiki's domain or short name");
		args.declare("wiki", null, true, String.class, "sets the wiki name");
	}
	
	protected abstract S createOutput();

	protected Writer getOutputWriter() {
		//FIXME: encoding!
		return ConsoleIO.writer; //TODO: get from command line!
	}
	
	protected OutputStream getOutputStream() {
		return System.out; //TODO: get from command line!
	}

	@Override
	protected void prepareApp() throws Exception {
		super.prepareApp();
		
		output = createOutput();
	}

	/*
	@Override
	protected void execute() throws Exception {
				run();
	}*/


}
