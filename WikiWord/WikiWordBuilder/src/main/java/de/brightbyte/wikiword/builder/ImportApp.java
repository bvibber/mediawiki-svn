package de.brightbyte.wikiword.builder;

import static de.brightbyte.util.LogLevels.LOG_FINE;
import static de.brightbyte.util.LogLevels.LOG_INFO;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import de.brightbyte.application.Agenda;
import de.brightbyte.application.Agenda.Monitor;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.Prompt;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.WikiWordStore;
import de.brightbyte.wikiword.store.builder.DatabaseConceptStoreBuilders;
import de.brightbyte.wikiword.store.builder.DatabaseWikiWordStoreBuilder;
import de.brightbyte.wikiword.store.builder.DebugLocalConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

/**
 * This is the base class for entry points to WikiWord.
 */
public abstract class ImportApp<S extends WikiWordConceptStoreBuilder<? extends WikiWordConcept>> extends StoreBackedApp<S> {
	
	protected static enum Operation {
		FRESH,
		CONTINUE,
		APPEND,
		ATTACH
	}
	
	protected List<WikiWordStoreBuilder> targetStores = new ArrayList<WikiWordStoreBuilder>();
	
	private boolean useAgenda;
	private String agendaTask;
	protected Agenda agenda;
	
	protected Operation operation = null;
	private Monitor agendaMonitor;
	protected  String[] baseTasks  = new String[] {};
	protected InputFileHelper inputHelper;
	
	public ImportApp(String agendaTask, boolean allowGlobal, boolean allowLocal) { //TODO: agenda-params!
		super(allowGlobal, allowLocal);
		this.agendaTask = agendaTask;
		this.useAgenda = agendaTask != null;
	}
		
	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<wiki>", "the wiki's domain or short name");
		args.declare("dbcheck", null, false, Boolean.class, "check referential integrity of database");
		args.declare("dbstats", null, false, Boolean.class, "calculate and dumps database table statistics");
		args.declare("noimport", null, false, Boolean.class, "do not import pages");
		args.declare("wiki", null, true, String.class, "sets the wiki name");
		//args.declare("buildstats", null, false, Boolean.class, "generate corpus statistics");
		//args.declare("noimport", null, false, Boolean.class, "do not import anything");
		args.declare("optimize", null, false, Boolean.class, "optimizes tables for later queries - this may take very long");
		
		if (useAgenda) {
			//args.declare("phase", null, true, String.class, "forces the import to continue at the given stage - use with care.");
			args.declare("fresh", null, false, Boolean.class, "restart with blank database");
			args.declare("continue", null, false, Boolean.class, "continue aborted run");
			args.declare("append", null, false, Boolean.class, "append more data to complete database");
		}
	}
	
	public void setAgendaMonitor(Agenda.Monitor monitor) {
		this.agendaMonitor = monitor;
	}

	protected void initAgenda(Agenda agenda, String... canAttachTo) throws PersistenceException {
		agenda.setLogger(
				logLevel<=LOG_INFO ? out : null,
				logLevel<=LOG_FINE ? out : null
						); 

		if (agendaMonitor!=null) {
			agenda.setMonitor(agendaMonitor);
		}
		
		if (args.isSet("fresh")) operation = Operation.FRESH;
		else if (args.isSet("continue")) operation = Operation.CONTINUE;
		else if (args.isSet("append")) operation = Operation.APPEND;
		else if (args.isSet("attach")) operation = Operation.ATTACH;
		
		//String phase = fresh ? null : args.getStringOption("phase", null);
		//if (phase != null) ctinue = true;
		Prompt p = new Prompt();
		
		if (operation==Operation.ATTACH && agenda.canRelyUpon(canAttachTo)) {
			p.println("### building upon previous task ("+agenda.getLastRootTask()+")");
		}
		else if (agenda.wasFinished(agendaTask)) {
			p.println("### the last run FINISHED.");

			if (operation==Operation.FRESH) {
				p.println("### performing FRESH import!");
			}
			else if (operation==Operation.APPEND) {
				p.println("### performing APPENDING import!");
			}
			else if (operation==Operation.ATTACH) {
				p.println("### performing ATTACHING import!");
			}
			else if (operation==Operation.CONTINUE) {
				p.println("### noting to CONTINUE.");
				System.exit(0);
			}
			else {
				String s = p.prompt("### type \"fresh\" to start a fresh run", "");
				
				if (s==null) {
					p.println("### UNEXPECTED EOF.");
					System.exit(1);
					return;
				}

				s = s.trim();
				
				if (s.equals("fresh")) {
					operation = Operation.FRESH;
				}
				/*else if (s.equals("append")) {
					operation = Operation.APPEND;
				}*/
				else {
					p.println("### aborted.");
					System.exit(0);
				}
			}
		}
		else if (agenda.canContinue(agendaTask)) {
			p.println("### the last run is INCOMPLETE and can be CONTINUED in phase \""+agenda.getLastRootRecord().task+"\".");

			if (operation==Operation.FRESH) {
				p.println("### performing FRESH import!");
			}
			else if (operation==Operation.CONTINUE) {
				p.println("### CONTINUING previous run.");
			}
			else {
				if (operation==Operation.APPEND) {
					p.println("### can not append to incomplete database!");
				} else if (operation==Operation.ATTACH) {
					p.println("### can not attach to incomplete database!");
				}
				
				String s = p.prompt("### type \"fresh\" to start a fresh run, or \"continue\" to continue the previous run\".", "");
				
				if (s==null) {
					p.println("### UNEXPECTED EOF.");
					System.exit(1);
					return;
				}

				s = s.trim();
		
				if (s.equals("fresh")) {
					p.println("### performing FRESH import!");
					operation = Operation.FRESH;
				}
				else if (s.equals("continue")) {
					p.println("### CONTINUING previous run!");
					operation = Operation.CONTINUE;
				}
				else {
					p.println("### aborted.");
					System.exit(0);
				}
			}
		}
		else {
			Agenda.Record root = agenda.getLastRootRecord();
			
			if (root!=null) {
				if (root.state == Agenda.State.STARTED) {
					p.println("### the last run is INCOMPLETE: "+root.task+".");
					
					if (operation==Operation.APPEND) {
						p.println("### can not append to incomplete database!");
						operation = null;
					} else  if (operation==Operation.ATTACH) {
						p.println("### can not attach to incomplete database!");
						operation = null;
					}

					if (operation == null) {
						String s = p.prompt("### type \"fresh\" to start a fresh run.", "");
					
						if (s==null) {
							p.println("### UNEXPECTED EOF.");
							System.exit(1);
							return;
						}
	
						s = s.trim();
				
						if (s.equals("fresh")) {
							operation = Operation.FRESH;
						}
						else {
							p.println("### aborted.");
							System.exit(0);
						}
					}
					
					if (operation==Operation.FRESH) {
						p.println("### performing FRESH import!");
					}
				}
				else if (operation==Operation.ATTACH) {
					p.println("### previous run complete, performing ATTACHING import!");
				}
				else {
					p.println("### the last run FINISHED: "+root.task);
					operation = Operation.FRESH;
				}
			} 
			else {
				if (operation==Operation.APPEND) {
					p.println("### no previous run, nothing to append to. Performing freshimport");
				}
				
				operation = Operation.FRESH;
			}
		}
		
		if (operation==null) {
			operation = Operation.CONTINUE;
		}
		
		if (operation==Operation.FRESH) {
			agenda.reset();
		}
		
		/*
		if (phase != null) {
			info("### FORCE continue at phase: "+phase);
		}
		*/
	}
	
	protected void initializeStores(boolean purge, boolean dropWarnings) throws PersistenceException {
		for (WikiWordStore store: stores) {
			if (store instanceof WikiWordStoreBuilder) {
				boolean isTarget = targetStores.contains(store);
				((WikiWordStoreBuilder)store).initialize(isTarget && purge, isTarget && dropWarnings); 
			}
		}		
	}

	protected void optimizeStores() throws PersistenceException {
		for (WikiWordStoreBuilder store: targetStores) {
			store.optimize(); 
		}		
	}

	protected void checkStores() throws PersistenceException {
		for (WikiWordStore store: targetStores) {
			store.checkConsistency(); 
		}		
	}

	protected void registerTargetStore(WikiWordStoreBuilder store) {
		super.registerStore(store);
		if (!targetStores.contains(store)) targetStores.add(store);
	}
	
	@SuppressWarnings("unchecked")
	@Override
	protected void createStores() throws PersistenceException, IOException {
		if (args.isSet("debug"))  conceptStore =  (S)new DebugLocalConceptStoreBuilder(getCorpus(), out); //HACK: ugly cast; //FIXME: will fail if global store expected
		else conceptStore = (S)DatabaseConceptStoreBuilders.createConceptStoreBuilder(getConfiguredDataSource(), getConfiguredDataset(), tweaks, null, true, true);
		
		registerStore(conceptStore);
		
		if (conceptStore instanceof DatabaseWikiWordStoreBuilder) {
			((DatabaseWikiWordStoreBuilder)conceptStore).setBackgroundErrorHandler(new FatalBackgroundErrorHandler<Runnable, Throwable, Error>());
		}
		
		boolean noimport = args.isSet("noimport");

		if (!noimport && useAgenda) {
				agenda = conceptStore.createAgenda();
		}
	}
	
	@Override
	protected void prepareApp() {
			inputHelper = new InputFileHelper(
					tweaks.getTweak("dumpdriver.externalGunzip", tweaks.getTweak("input.externalGunzip", (String)null)),
					tweaks.getTweak("dumpdriver.externalBunzip", tweaks.getTweak("input.externalBunzip", (String)null)));
	}
	
	@Override
	protected void execute() throws Exception {
		boolean noimport = args.isSet("noimport");
		boolean dbcheck = args.isSet("dbcheck");
		boolean dbstats = args.isSet("dbstats");
		boolean optimize = args.isSet("optimize");
		
		if (!noimport) {
			if (useAgenda) {
				agenda = conceptStore.getAgenda();
				initAgenda(agenda, baseTasks  = new String[] {});
			}

			if (operation == Operation.FRESH) {
				section("-- purge --------------------------------------------------");
				initializeStores(getPurgeData(), getDropWarnings()); 
			}
			else {
				initializeStores(false, getDropWarnings()); 
			}

			if (!useAgenda || agenda.beginTask("ImportApp.launch", agendaTask)) {
				run();
				if (useAgenda) agenda.endTask("ImportApp.launch", agendaTask);
			}
		}

        if (dbstats) {
            section("-- dbstats --------------------------------------------------");
            dumpTableStats();

            int w = conceptStore.getNumberOfWarnings();
            if (w==0) info("no warnings");
            else warn("WARNINGS: "+w);
        }

        if (dbcheck) {
            section("-- check --------------------------------------------------");
            checkStores();
            info("OK.");

            int w = conceptStore.getNumberOfWarnings();
            if (w==0) info("no warnings");
            else warn("WARNINGS: "+w);
        }

        if (optimize) {
            section("-- optimize --------------------------------------------------");
            optimizeStores();
        }
	}

	protected boolean getDropWarnings() {
		return false;
	}

	protected boolean getPurgeData() {
		return true;
	}

	public int getExitCode() {
		return exitCode;
	}

	public void setOperation(Operation op) {
		operation = op;
	}
}
