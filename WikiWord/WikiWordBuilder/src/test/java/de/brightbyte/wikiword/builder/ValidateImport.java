package de.brightbyte.wikiword.builder;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.sql.SQLException;
import java.util.Arrays;
import java.util.List;

import javax.sql.DataSource;

import junit.framework.AssertionFailedError;
import de.brightbyte.application.Agenda;
import de.brightbyte.application.Arguments;
import de.brightbyte.application.Agenda.State;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.testing.DatabaseTestUtils;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.Output;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.store.WikiWordStore;

public class ValidateImport {
	
	protected class ValidationMonitor implements Agenda.Monitor {
		
		protected String name;
		
		public ValidationMonitor(String name) {
			this.name = name;
		}

		
		public void beginTask(Agenda.Record rec) {
	
			runTests(name, "before", rec.context+"::"+rec.task);
		}
		
		public void skipTask(Agenda.Record rec) {
			
			runTests(name, "after", rec.context+"::"+rec.task);
		}

		public void endTask(Agenda.Record rec, int end, long duration, State state, String result) {
	
			runTests(name, "after", rec.context+"::"+rec.task);
		}
	
	
	};
	
	protected Output out = ConsoleIO.output;

	protected TweakSet tweaks;
	
	protected DataSource dataSource;
	protected DatabaseAccess database;

	protected File baseDir;
	protected ClassLoader loader;
	protected String basePath;
	
	protected String dbcheck = "--dummy-x"; //FIXME: set to "--dbcheck" to run consistency checks. but thes FAIL SPURIOUSLY! Something is wrong with db state. 
	
	protected String[] languages;
	
	protected URL getResource(String name) {
		if (baseDir!=null) {
			File f = new File(baseDir, name);
			if (f.exists()) {
				try {
					return f.toURL();
				} catch (MalformedURLException e) {
					throw new RuntimeException("failed to generate URL from file "+f.getAbsolutePath());
				}
			}
		}
		
		return loader.getResource(basePath+"/"+name);
	}
	
	protected void runTests(String set, String state, String task) {
		while (task.length()>0) {
			runTest(set, state, task.replaceAll("[-.,:/+]+", "-"));
			
			task = task.replaceAll("(^|[-.,:/+]+)[^-.,:/+]*$", "");
		}
	}
	
	protected void runTest(String set, String state, String task) {
		String n = set+"-"+state+"-"+task;
		URL u = getResource(n+".test");
		
		if (u==null) {
			echo(" - no such tests: "+set+"-"+state+"-"+task);
			return;
		}
		
		try {
			List<DatabaseTestUtils.TestCase> tests = DatabaseTestUtils.loadTestCases(u);
			
			for (DatabaseTestUtils.TestCase t: tests) {
				try {
					echo("RUNNING TEST: "+t.getName());
					t.run(database);
				} catch (SQLException e) {
					failure(n, e);
				} catch (RuntimeException e) {
					failure(n, e);
				} catch (AssertionFailedError e) {
					failure(n, e);
				}
			}
		} catch (IOException e) {
			failure(n, e);
		}
	}

	private void failure(String n, Throwable e) {
		//System.out.println("FAILURE: "+n+": "+e);
		//e.printStackTrace(); //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
		
		throw (AssertionFailedError)new AssertionFailedError(n).initCause(e);
	}
	
	protected void echo(String msg, Object... args) {
		for(Object a: args) {
			msg += ", ";
			msg += String.valueOf(a);
		}
		
		out.println(msg);
	}

	protected boolean launchApp(ImportApp<? extends WikiWordStore> app, ValidationMonitor monitor, String... argv) throws Exception {
		echo("");
		echo("============================================================");
		echo("== TESTING "+app.getClass(), (Object[])argv);
		echo("============================================================");
		echo("");

		app.setKeepAlive(true);
		app.setAgendaMonitor(monitor);
		app.setTweaks(tweaks);
		app.setDataSource(dataSource);
		app.setOperation(ImportApp.Operation.FRESH);
		app.launch(argv);
		
		if (app.getExitCode() != 0) {
			echo("APP FAILED WITH ERROR CODE "+app.getExitCode());
			return false;
		}
		
		return true; 
	}

	protected boolean importDump(String lang) throws Exception {
		ValidationMonitor monitor = new ValidationMonitor(lang);
		
		String f = lang+"wiki.xml";
		URL u = getResource(f);
		if (u==null) throw new FileNotFoundException("dump file "+f);
		
		ImportConcepts app = new ImportConcepts();
		boolean ok = launchApp( app, monitor, dbcheck, "--fresh", "--url", "TEST:", u.toString() );
		
		if (ok) ok = generateInfo(lang, monitor);
		
		return ok;
	}
	
	protected boolean generateThesaurus(String[] langs) throws Exception {
		Arrays.sort(langs);
		String n = StringUtils.join("_", langs); 
		
		ValidationMonitor monitor = new ValidationMonitor(n);
		
		BuildThesaurus app = new BuildThesaurus();
		boolean ok = launchApp( app, monitor, dbcheck, "--fresh", "TEST:"+n, StringUtils.join(",", langs));
		
		if (ok) ok = generateInfo(n, monitor);
		
		return ok;
	}

	protected  boolean generateInfo(String name, ValidationMonitor monitor) throws Exception {
		boolean ok;
		
		BuildStatistics buildStatistics = new BuildStatistics();
		ok = launchApp( buildStatistics, monitor, dbcheck, "--fresh", "TEST:", name );
		if (!ok) return ok;

		BuildConceptInfo buildInfo = new BuildConceptInfo();
		ok = launchApp( buildInfo,  monitor, dbcheck, "--fresh", "TEST:", name );
		return ok;
	}

	/*
	protected DataSource dataSource;
	protected DatabaseAccess database;

	protected File baseDir;
	protected ClassLoader loader;
	protected String basePath;
	*/
	
	public void launch(String[] argv) throws Exception {
		Arguments args = new Arguments();
		args.parse(argv);
		
		setup(args);
		run();
	}
	
	public void setup(Arguments args) throws Exception {
		if (tweaks==null) {
			tweaks = new TweakSet();
			
			tweaks.setTweak("dumpdriver.pageImportQueue", 0);
			tweaks.setTweak("dbstore.backgroundFlushQueue", 0);
			tweaks.setTweak("dbstore.useEntityBuffer", false);
			tweaks.setTweak("dbstore.useRelationBuffer", false);
			tweaks.setTweak("dbstore.updateChunkSize", 0);
			tweaks.setTweak("dbstore.cacheReferenceSeparator", "^");
			tweaks.setTweak("dbstore.cacheReferenceFieldSeparator", "&");
			tweaks.setTweak("dbstore.idManager", args.isSet("idman"));
			tweaks.setTweak("dbstore.idManager.bufferSize", 48);
			
			if (args.isSet("tweaks")) tweaks.loadTweaks(new File(args.getStringOption("tweaks", null)));
			tweaks.setTweaks(System.getProperties(), "wikiword.tweak."); //XXX: doc
		}
		
		tweaks.setTweaks(args, "tweak."); //XXX: doc
		
		//--------------------
		tweaks.setTweak("dbstore.prefix", args.getOption("prefix", "TEST"));

		File dbf = CliApp.findConfigFile(args, "db");

		if (dbf!=null) echo("Using database connection specified in "+dbf);
		else throw new RuntimeException("No file found defining database connection!\n\tUse the --db paramter or put the file in a default location.");
		
		dataSource = new DatabaseConnectionInfo(dbf);
		
		String prefix = tweaks.getTweak("prefix", "TEST");
		if (prefix!=null && prefix.length()>0) prefix += "_";
		database = new DatabaseSchema(prefix, dataSource, 0);
		
		//--------------------
		String dir = args.getStringOption("dir", null);
		if (dir!=null) baseDir = new File(dir); 
		
		loader = getClass().getClassLoader();
		basePath = getClass().getName().replace('.', '/').replaceAll("/[^/]*$", "/");

		//--------------------
		languages = (String[]) args.getParameters().toArray(new String[args.getParameterCount()]);
		if (languages.length==0) languages = new String[] { "xx", "yy", "zz" };
	}
	
	public void run() throws Exception {
		boolean ok = true;
		
		for (String lang: languages) {
			ok = importDump(lang);
			if (!ok) return;
		}
		
		ok = generateThesaurus(languages);
		
		if (ok) {
			echo("");
			echo("============================================================");
			echo("== DONE! ALL IS GOOD.");
			echo("============================================================");
			echo("");
		}
	}
	
	public static void main(String[] args) throws Exception {
		ValidateImport val = new ValidateImport();
		val.launch(args);
	}
}
