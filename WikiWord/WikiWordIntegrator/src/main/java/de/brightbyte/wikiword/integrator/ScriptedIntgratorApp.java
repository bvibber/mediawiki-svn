package de.brightbyte.wikiword.integrator;

import java.io.InputStream;
import java.io.InputStreamReader;

import bsh.Interpreter;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.builder.InputFileHelper;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class ScriptedIntgratorApp extends CliApp {
	
	protected InputFileHelper inputHelper;
	
	protected InputFileHelper getInputHelper() {
		if (inputHelper==null)  {
			inputHelper = new InputFileHelper(tweaks);
		} 
		return inputHelper;
	}

	protected String getSourceFileName() {
		if (args.getParameterCount() < 2) throw new IllegalArgumentException("missing second parameter (source file name)");
		return args.getParameter(1);
	}
	
	@Override
	protected void run() throws Exception {
		Interpreter i = new Interpreter();  
		i.set("integrator", this);                    
		i.set("tweaks", tweaks);                    
		i.set("inputHelper", getInputHelper());                    
		i.set("args", args);                    

		i.eval("import java.util.*;");                    
		i.eval("import de.brightbyte.wikiword.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.data.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.processor.*;");                    
		i.eval("import de.brightbyte.wikiword.integrator.store.*;");
		
		i.eval("importCommands(\"de.brightbyte.wikiword.integrator\");");             

		String n = getSourceFileName();
		InputStream in = getInputHelper().open(n);
		i.eval(new InputStreamReader(in, "UTF-8"));
	}	

	public static void main(String[] argv) throws Exception {
		ScriptedIntgratorApp app = new ScriptedIntgratorApp();
		app.launch(argv);
	}
}