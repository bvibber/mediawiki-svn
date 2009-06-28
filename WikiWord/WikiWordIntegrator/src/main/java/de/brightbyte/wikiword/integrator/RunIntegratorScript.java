package de.brightbyte.wikiword.integrator;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.PrintStream;
import java.io.Reader;
import java.util.ArrayList;
import java.util.Collection;
import java.util.regex.Pattern;

import bsh.ConsoleInterface;
import bsh.Interpreter;
import de.brightbyte.data.Functor;
import de.brightbyte.db.SqlScriptRunner;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.OutputPrintStream;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.builder.InputFileHelper;

public class RunIntegratorScript extends CliApp {
	
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
		
		PrintStream pout = new OutputPrintStream(out);
		i.setOut(pout);
		i.setErr(pout);
		
		i.set("integrator", this);                    
		i.set("tweaks", tweaks);                    
		i.set("inputHelper", getInputHelper());                    
		i.set("args", args);                    
		i.set("dataset", getConfiguredDataset());                    
		i.set("datasource", getConfiguredDataSource());                    
		i.set("scriptManglers", getSqlScriptManglers());                    
		i.set("tablePrefix", getConfiguredDataset().getDbPrefix());                    
		i.set("scriptBaseUrl", getInputHelper().getURL(n));                    

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

	protected Collection<Functor<String, String>> getSqlScriptManglers() {
		ArrayList<Functor<String, String>> list = new ArrayList<Functor<String, String>>();
		
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_prefix", getConfiguredDataset().getDbPrefix()) );
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_db", getConfiguredDatasetName()) );
		
		return list;
	}
	
	public static void main(String[] argv) throws Exception {
		RunIntegratorScript app = new RunIntegratorScript();
		app.launch(argv);
	}
}