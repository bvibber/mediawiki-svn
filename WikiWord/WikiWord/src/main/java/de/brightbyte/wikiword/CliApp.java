package de.brightbyte.wikiword;

import static de.brightbyte.util.LogLevels.LOG_INFO;

import java.io.File;
import java.io.IOException;
import java.io.PrintStream;
import java.util.List;
import java.util.Map;

import javax.sql.DataSource;

import de.brightbyte.application.Arguments;
import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.io.LogOutput;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.SystemUtils;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.rdf.WikiWordIdentifiers;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.DatabaseWikiWordStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore;
import de.brightbyte.wikiword.store.WikiWordLocalStore;
import de.brightbyte.wikiword.store.WikiWordStore;

/**
 * This is the base class for entry points to WikiWord.
 */
public abstract class CliApp<S extends WikiWordStore> {
	
	public static final String VERSION_INFO = "WikiWord by Daniel Kinzler, brightbyte.de, 2007-2008";
	public static final String LICENSE_INFO = "Developed at the University of Leipzig. Free Software, GNU GPL";
	
	protected Arguments args;
	
	protected LogOutput out;
	
	protected int logLevel;
	protected int exitCode;
	
	protected S store;
	protected DatasetIdentifier dataset;
	protected DataSource dataSource;
	
	protected TweakSet tweaks;
	protected WikiWordIdentifiers identifiers;
	protected boolean keepAlive;
	
	protected boolean allowLocalStore;
	protected boolean allowGlobalStore;

	public CliApp(boolean allowGlobal, boolean allowLocal) { //TODO: agenda-params?!
		args = new Arguments();
		out = new LogOutput();
		
		allowGlobalStore = allowGlobal;
		allowLocalStore  = allowLocal;
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
		if (dataset==null) return null;
		if (!(dataset instanceof Corpus)) {
			if (store instanceof WikiWordLocalStore) {
				dataset = ((WikiWordLocalStore)store).getCorpus();
			}
			else {
				throw new IllegalStateException("this application uses a non-corpus dataset!");
			}
		}
		return (Corpus)dataset;
	}
	
	public String getCollectionName() {
		if (dataset!=null) return dataset.getCollection(); 
		
		String s = args.getParameter(0);
		int idx = s.indexOf(':');
		
		if (idx<=0) {
			return guessCollectionName();
		}
		
		return s.substring(0, idx);
	}

	public String getDatasetName() {
		if (dataset!=null) return dataset.getName(); 
		
		String s = args.getParameter(0);
		int idx = s.indexOf(':');
		
		if (idx<0) {
			return s;
		}
		
		s = s.substring(idx+1);
		if (s.length()==0) s = guessDatasetName();
		
		return s;
	}
	
	public DatasetIdentifier getDataset() {
		if (dataset!=null) return dataset;

		String c = getCollectionName();
		String n = getDatasetName();

		if (!allowGlobalStore) {
			if (!allowLocalStore) throw new RuntimeException("bad setup: nither global nor local stores allowed for this app!");
			dataset = Corpus.forName(c, n, tweaks);
		}
		else {
			dataset = DatasetIdentifier.forName(c, n);
		}
		
		return dataset;
	}
	
	public boolean isDatasetLocal() {
		if (store!=null && store instanceof WikiWordLocalStore) return true;
		if (dataset!=null && dataset instanceof Corpus) return true;
		return getDataset() instanceof Corpus;
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
		applyArguments();
		
		if (args.isSet("h") || args.isSet("help")) {
			args.printHelp(ConsoleIO.output); //XXX: stream?!
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
		
		dataset = getDataset();
		identifiers = new WikiWordIdentifiers(tweaks);
		
		section("*** DATASET: "+dataset.getQName()+" ***");
		
		store = createStore();

		exitCode = 23;

		prepareApp();
		
		try {
			store.open();

			if (args.isSet("dbtest")) {
				section("-- db test --------------------------------------------------");
				Object o = args.getOption("dbtest", null); 
                String t;
                if (o instanceof String) {
                	t = (String)o;
                }
                else {
                	t = "\u00c4 \u00d6 \u00dc - \ud800\udf30";
                }

                String s = ((DatabaseSchema)((DatabaseWikiWordStore)store).getDatabaseAccess()).echo(t, args.getStringOption("dbcharset", "binary"));

                if (s.equals(t)) {
                        info("DB TEST OK: "+t);
                }
                else {
                		store.close(true);
                        warn("DB TEST FAILED: "+t+" != "+s+" !");
                        exit(5);
                        return;
                }
			}
	
			execute();

			store.close(true);
          
            section("-- DONE --------------------------------------------------");
			exitCode = 0;
			
		}
		catch (Throwable ex) {
			error("uncaught throwable", ex);
			store.close(false);
		}
		finally {
			exit(exitCode);
		}		
	}
	
	protected void prepareApp() throws Exception {
		// noop
	}

	protected void execute() throws Exception {
		run();
	}

	public void setDataSource(DataSource dataSource) {
		this.dataSource = dataSource;
	}
	
	protected S createStore() throws IOException, PersistenceException {
		S store;

		//TODO: make this more abstract. we may not be storing into a DB at all!
		if (dataSource==null) {
			File dbf = getDatabaseFile();
			dataSource = new DatabaseConnectionInfo(dbf);
		}
		
		store = (S)createStore(dataSource);
		
		dataset = ((DatabaseWikiWordStore)store).getDatasetIdentifier();
		return store;
	}

	@SuppressWarnings("unchecked")
	protected S createStore(DataSource db) throws PersistenceException {
		return (S)createConceptStore(db);
	}
	
	protected WikiWordConceptStore<? extends WikiWordConcept, ? extends WikiWordConceptReference<? extends WikiWordConcept>> createConceptStore(DataSource db) throws PersistenceException {
		DatasetIdentifier ds = getDataset();
		WikiWordConceptStore<? extends WikiWordConcept, ? extends WikiWordConceptReference<? extends WikiWordConcept>> store = DatabaseConceptStores.createConceptStore(db, ds, tweaks, allowLocalStore, allowGlobalStore);
		return store;
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

		//use explicit config
		String p = args.getStringOption(prefix, null);
		if (p!=null) f = new File(p);
		
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
		args.declare("dbtest", null, false, String.class, "perform an echo test on the database. " +
				"If a value is assigned, that value is used for the ping test.");
		args.declare("loglevel", "v", true, Integer.class, "sets log level (verbosity): 0 shows all messages, " +
				"800 shows only informational messages." +
				"See java.util.logging.Level for values.");
		args.declare("tweaks", "t", true, String.class, "tweak file to load tweaks from (must be in Properties file format)");
		args.declareHelp("tweak.xxx", "sets the tweak xxx. Overrides any tweaks given in the tweak file, " +
				"or as system properties");
	}

	protected void applyArguments() {
		//noop
	}


	public void setTweaks(TweakSet tweaks) {
		this.tweaks = tweaks;
	}
	
}
