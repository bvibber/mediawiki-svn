package de.brightbyte.wikiword.integrator;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.PrintStream;
import java.io.Reader;

import bsh.ConsoleInterface;
import bsh.Interpreter;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.builder.InputFileHelper;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class ScriptedIntgratorApp extends CliApp {
	
	protected InputFileHelper inputHelper;
	
	private ConsoleInterface console = new ConsoleInterface() {
		private PrintStream out;
		private PrintStream err;
		private Reader in;
		
		{
			out = System.out;
			err = System.err;
			
			try  { 
				in = new InputStreamReader(System.in, ConsoleIO.getEncoding()); 
			} catch (IOException ex) {
				throw new RuntimeException(ex);
			}
		}
	
		public void println(Object x) {
			ConsoleIO.output.println(x);	
		}
	
		public void print(Object x) {
			getOut().print(String.valueOf(x));
		}
	
		public PrintStream getOut() {
			return out;
		}
	
		public Reader getIn() {
			return in;
		}
	
		public PrintStream getErr() {
			return  err;
		}
	
		public void error(Object x) {
			getErr().println(String.valueOf(x));
		}
	
	};
	
	protected InputFileHelper getInputHelper() {
		if (inputHelper==null)  {
			inputHelper = new InputFileHelper(tweaks);
		} 
		return inputHelper;
	}

	protected String getSourceFileName() {
		if (args.getParameterCount() < 2) return null;
		return args.getParameter(1);
	}
	
	@Override
	protected void run() throws Exception {
		Interpreter i;

		String n = getSourceFileName();
		if (n==null) {
			i = new Interpreter(console);
		} else {
			i = new Interpreter();  
		}
		
		i.set("integrator", this);                    
		i.set("tweaks", tweaks);                    
		i.set("inputHelper", getInputHelper());                    
		i.set("args", args);                    
		i.set("dataset", getConfiguredDataset());                    
		i.set("datasource", getConfiguredDataSource());                    

		i.eval("import java.util.*;");                    
		i.eval("import de.brightbyte.wikiword.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.data.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.processor.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.store.*;");
		
		i.eval("importCommands(\"de.brightbyte.wikiword.integrator\");");             

		if (n==null) {
				i.run();
		} else {
				InputStream in = getInputHelper().open(n);
				i.eval(new InputStreamReader(in, "UTF-8"));
		}
	}	

	public static void main(String[] argv) throws Exception {
		ScriptedIntgratorApp app = new ScriptedIntgratorApp();
		app.launch(argv);
	}
}