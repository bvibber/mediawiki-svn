package de.brightbyte.wikiword;

import static de.brightbyte.util.LogLevels.LOG_INFO;

import java.io.File;
import java.io.IOException;
import java.io.PrintStream;
import java.util.List;
import java.util.Map;

import javax.sql.DataSource;

import de.brightbyte.application.Arguments;
import de.brightbyte.audit.DebugUtil;
import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.io.LogOutput;
import de.brightbyte.io.Output;
import de.brightbyte.util.ErrorHandler;
import de.brightbyte.util.LogLevels;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.SystemUtils;
import de.brightbyte.wikiword.rdf.WikiWordIdentifiers;

/**
 * This is the base class for entry points to WikiWord.
 */
public abstract class CliApp {
	
	public class FatalBackgroundErrorHandler<T, X extends Throwable, Y extends Throwable> implements
			ErrorHandler<T, X, Y> {
		
		public void handleError(T context, String message, X error) throws Y {
			String m = message;
			if (context!=null) m = m + " ("+context+")";
			
			out.error(m, error);
			
			terminate();
		}

	}

	public static final String VERSION_INFO = "WikiWord by Daniel Kinzler, brightbyte.de, 2007-2009";
	public static final String LICENSE_INFO = "Originally created at the University of Leipzig,\n\tdevelopment supported by Wikimedia Deutschland.\n\tFree Software, GNU LGPL. See COPYING for details.";
	
	protected Arguments args;
	
	protected LogOutput out;
	
	protected int logLevel;
	protected int exitCode;
	
	protected TweakSet tweaks;
	protected WikiWordIdentifiers identifiers;
	protected boolean keepAlive;
	
	public CliApp() { //TODO: agenda-params?!
		args = new Arguments();
		out = new LogOutput();
	}

	public boolean isKeepAlive() {
		return keepAlive;
	}

	public void setKeepAlive(boolean keepAlive) {
		this.keepAlive = keepAlive;
	}
	
	public void setLogLevel(int level) {
		this.logLevel = level;
		out.setLogLevel(level);
	}
	
	public void setLogOutput(Output out) {
		if (!(out instanceof LogOutput)) out = new LogOutput(out);
		this.out = (LogOutput)out;
	}
	
	public LeveledOutput getLogOutput() {
		return out;
	}
	
	public void debug(String msg) {
		out.debug(msg);
	}

	public void error(String msg, Throwable ex) {
		out.error(msg, ex);
	}

	public void info(String msg) {
		out.info(msg);
	}

	public void section(String msg) {
		info(msg);
	}

	public void trace(String msg) {
		out.trace(msg);
	}

	public void warn(String msg, Throwable ex) {
		out.warn(msg, ex);
	}

	public void warn(String msg) {
		out.warn(msg);
	}

	public void declareOption(String name, String abbrev, boolean greedy, Class type, String help) {
		args.declare(name, abbrev, greedy, type, help);
	}

	public void declareHelp(String name, String help) {
		args.declareHelp(name, help);
	}

	public int getIntOption(String name, int def) {
		return args.getIntOption(name, def);
	}

	public Object getOption(String name, Object def) {
		return args.getOption(name, def);
	}

	public Map<String, Object> getOptions() {
		return args.getOptions();
	}

	public String getParameter(int i) {
		return args.getParameter(i);
	}

	public int getParameterCount() {
		return args.getParameterCount();
	}

	public List<String> getParameters() {
		return args.getParameters();
	}

	public String getStringOption(String name, String def) {
		return args.getStringOption(name, def);
	}

	public boolean isSet(String name) {
		return args.isSet(name);
	}

	public Iterable<String> parameters() {
		return args.parameters();
	}

	public void printHelp(PrintStream out) {
		args.printHelp(out);
	}
	
	public Arguments getArguments() {
		return args;
	}
	
	public Corpus getCorpus() {
		DatasetIdentifier dataset = getConfiguredDataset();
		
		if (dataset==null) return null;
		
		if (!(dataset instanceof Corpus)) {
			return null;
		}
		
		return (Corpus)dataset;
	}
	
	protected String getDatasetArgument() {
		return args.getParameter(0);
	}
	
	protected String getConfiguredCollectionName() {
		String s = getDatasetArgument();
		int idx = s.indexOf(':');
		
		if (idx<=0) {
			return guessCollectionName();
		}
		
		return s.substring(0, idx);
	}

	public String getConfiguredDatasetName() {
		String s = getDatasetArgument();
		int idx = s.indexOf(':');
		
		if (idx<0) {
			return s;
		}
		
		s = s.substring(idx+1);
		if (s.length()==0) s = guessDatasetName();
		
		return s;
	}
	
	public DatasetIdentifier getConfiguredDataset() {
		DatasetIdentifier dataset;

		String c = getConfiguredCollectionName();
		String n = getConfiguredDatasetName();

		dataset = Corpus.forName(c, n, tweaks);
		
		return dataset;
	}
	
	public boolean isDatasetLocal() {
		return true;
	}
	
	public void exit(int code) {
		if (!keepAlive) System.exit(code);
	}
	
	public void launch(String[] argv) throws Exception {
		//WikiImporter.declareOptions(args);
		ConsoleIO.setEncoding("UTF-8");
		out.println("\t"+getClass().getName().replaceAll("^.*\\.", "")+" - "+VERSION_INFO);
		out.println("\t"+LICENSE_INFO);
		out.println("");
		
		declareOptions();
		
		args.parse(argv);

		if (!applyArguments()) {
			args.printHelp(ConsoleIO.output); 
			exit(1);
			return;
		} else if (args.isSet("h") || args.isSet("help")) {
			args.printHelp(ConsoleIO.output); 
			exit(0);
			return;
		}
		
		int ll = args.getIntOption("loglevel", LOG_INFO);
		setLogLevel(ll);
		
		//File dumpf = null;

		if (tweaks==null) {
			tweaks = new TweakSet();
			File f = getTweakFile();
			if (f!=null) tweaks.loadTweaks(f);
			tweaks.setTweaks(System.getProperties(), "wikiword.tweak."); //XXX: doc
		}
		
		tweaks.setTweaks(args, "tweak."); //XXX: doc
		
		ConsoleIO.setEncoding(tweaks.getTweak("console.encoding", "UTF-8"));
		
		//DriverManager.setLogStream(out);
		
		identifiers = new WikiWordIdentifiers(tweaks);
		
		DatasetIdentifier dataset = getConfiguredDataset();
		section("*** DATASET: "+dataset.getQName()+" ***");

		launchExecute();
	}
	
	protected void launchExecute() throws Exception {
		exitCode = 23;

		prepareApp();
		
		try {
			execute();

            section("-- DONE --------------------------------------------------");
			exitCode = 0;
		}
		catch (Throwable ex) {
			error("uncaught throwable", ex);
		}
		finally {
			finalizeApp();
			exit(exitCode);
		}		
	}
	
	protected void finalizeApp() throws Exception {
		// noop
	}

	protected void prepareApp() throws Exception {
		// noop
	}

	protected void execute() throws Exception {
		run();
	}

	protected DataSource getConfiguredDataSource() throws IOException, PersistenceException {
		File dbf = getDatabaseFile();
		return new DatabaseConnectionInfo(dbf);
	}
	
	protected File getDatabaseFile() {
		File f = findConfigFile(args, "db");

		if (f!=null) info("Using database connection specified in "+f);
		else warn("No file found defining database connection!\n\tUse the --db paramter or put the file in a default location.");
		
		return f;
	}
	
	protected File getTweakFile() {
		File f = findConfigFile(args, "tweaks");

		if (f!=null) info("Using configuration tweaks specified in "+f);
		else info("No file found defining configuration tweaks, using defaults.\n\tYou may want to specify the --tweaks paramter or put a tweaks file in a default location.");
		
		return f;
	}
	
	public static File findConfigFile(Arguments args, String prefix) {
		File f = null;
		File d = null;

		//use explicit config fiole
		String p = args.getStringOption(prefix, null);
		if (p!=null) f = new File(p);
		
		//use explicit config dir
		p = args.getStringOption("config-dir", null);
		if (p!=null) d = new File(p);
		
		//look in config dir
		if (f==null && d!=null) {
				if (f==null) {
					f = new File(d+"/"+prefix+".properties");
					if (!f.exists()) f = null;
				}
		}

		//look in home dir
		if (f==null) {
			String u = SystemUtils.getPropertySafely("user.home", null);
			if (u!=null) {
				f = new File(u+"/.wikiword/"+prefix+".properties");
				if (!f.exists()) f = null;

				if (f==null) {
					f = new File(u+"/.wikiword."+prefix+".properties");
					if (!f.exists()) f = null;
				}
			}
		}

		//look in /etc
		if (f==null) {
			f = new File("/etc/wikiword/"+prefix+".properties");
			if (!f.exists()) f = null;

			if (f==null) {
				f = new File("/etc/wikiword."+prefix+".properties");
				if (!f.exists()) f = null;
			}
		}
		
		//scan current dir to root
		//XXX: probably more confusing than helpful
		/*if (f==null) {
			File d = new File(".").getAbsoluteFile().getParentFile();
			
			while (f==null) {
				f = new File(d, prefix+".properties");
				if (!f.exists()) {
					f = null;
					File parent = d.getParentFile();
					if (parent==null) break;
					if (d.equals(parent)) break;
					if (parent.getPath().equals("")) break;
					
					d = parent;
				}
			}
		}*/
		
		return f;
	}

	protected abstract void run() throws Exception;

	protected String getTargetFileName() {
		return args.getParameterCount() < 2 ? null : args.getParameter(1);
	}

	protected String guessDatasetName() {
		String n = getTargetFileName(); 
		if (n==null) return null;
		return Corpus.guessCorpusName(n);
	}

	protected String guessCollectionName() {
		return null;
	}

	protected void declareOptions() {
		args.declareHelp("<collection:dataset>", "the collection and dataset to operate on. Must contain a \":\" as a separator. ");
		args.declareHelp("<file>", "the file to process.");
		args.declare("db", null, true, String.class, "the db info file to read database connection info from");
		args.declare("help", "h", false, Boolean.class, "shows this help page");
		args.declare("config-dir", "C", true, String.class, "base directory for config files");
		args.declare("dbtest", null, false, String.class, "perform an echo test on the database. " +
				"If a value is assigned, that value is used for the ping test.");
		args.declare("loglevel", "v", true, Integer.class, "sets log level (verbosity): 0 shows all messages, " +
				"800 shows only informational messages." +
				"See java.util.logging.Level for values.");
		args.declare("tweaks", "t", true, String.class, "tweak file to load tweaks from (must be in Properties file format)");
		args.declareHelp("tweak.xxx", "sets the tweak xxx. Overrides any tweaks given in the tweak file, " +
				"or as system properties");
	}

	protected boolean applyArguments() {
		return true;
	}

	public void setTweaks(TweakSet tweaks) {
		this.tweaks = tweaks;
	}
	
	public int getExitCode() {
		return exitCode;
	}
	
	protected void terminate() {
		info("exiting application");
		
		if (logLevel<=LogLevels.LOG_FINE) DebugUtil.dumpStackTrace("\tat ", out);
		
		exit(55);
	}
}
