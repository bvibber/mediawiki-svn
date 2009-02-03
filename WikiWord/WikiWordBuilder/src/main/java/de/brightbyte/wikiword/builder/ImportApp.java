package de.brightbyte.wikiword.builder;

import static de.brightbyte.util.LogLevels.LOG_FINE;
import static de.brightbyte.util.LogLevels.LOG_INFO;

import java.io.IOException;

import de.brightbyte.application.Agenda;
import de.brightbyte.application.Agenda.Monitor;
import de.brightbyte.io.Prompt;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.WikiWordStore;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;
import de.brightbyte.wikiword.store.builder.DatabaseConceptStoreBuilders;
import de.brightbyte.wikiword.store.builder.WikiWordConceptStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

/**
 * This is the base class for entry points to WikiWord.
 */
public abstract class ImportApp<S extends WikiWordConceptStoreBuilder<? extends WikiWordConcept>> extends CliApp<S> {
	
	protected static enum Operation {
		FRESH,
		CONTINUE,
		APPEND
	}
	
	private boolean useAgenda;
	private String agendaTask;
	protected Agenda agenda;
	
	protected Operation operation = null;
	private Monitor agendaMonitor;	
	
	public ImportApp(String agendaTask, boolean allowGlobal, boolean allowLocal) { //TODO: agenda-params!
		super(allowGlobal, allowLocal);
		this.agendaTask = agendaTask;
		this.useAgenda = agendaTask != null;
	}
		
	@SuppressWarnings("unchecked")
	@Override
	protected WikiWordStoreFactory<S> createConceptStoreFactory() throws IOException, PersistenceException {
		return new DatabaseConceptStoreBuilders.Factory(getConfiguredDataSource(), getConfiguredDataset(), tweaks, true, true);
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<wiki>", "the wiki's domain or short name");
		args.declare("dbcheck", null, false, Boolean.class, "check referential integrity of database");
		args.declare("dbstats", null, false, Boolean.class, "calculate and dumps database table statistics");
		args.declare("noimport", null, false, Boolean.class, "do not import pages");
		args.declare("wiki", null, true, String.class, "sets the wiki name");
		args.declare("dummy", null, false, Boolean.class, "use a dummy store (benchmarking mode). In this case, <db-info-file> is ignored");
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

	protected void initAgenda(Agenda agenda) throws PersistenceException {
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
		
		//String phase = fresh ? null : args.getStringOption("phase", null);
		//if (phase != null) ctinue = true;
		Prompt p = new Prompt();
		
		if (agenda.wasFinished(agendaTask)) {
			p.println("### the last run FINISHED.");

			if (operation==Operation.FRESH) {
				p.println("### performing FRESH import!");
			}
			else if (operation==Operation.APPEND) {
				p.println("### performing APPENDING import!");
			}
			else if (operation==Operation.CONTINUE) {
				p.println("### noting to CONTINUE.");
				System.exit(0);
			}
			else {
				String s = p.prompt("### type \"fresh\" to start a fresh run, or \"append\" to append.", "");
				
				if (s==null) {
					p.println("### UNEXPECTED EOF.");
					System.exit(1);
					return;
				}

				s = s.trim();
				
				if (s.equals("fresh")) {
					operation = Operation.FRESH;
				}
				else if (s.equals("append")) {
					operation = Operation.APPEND;
				}
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
			((WikiWordStoreBuilder)store).initialize(purge, dropWarnings); //XXX: ugly cast!
		}		
	}

	protected void optimizeStores() throws PersistenceException {
		for (WikiWordStore store: stores) {
			((WikiWordStoreBuilder)store).optimize(); //XXX: ugly cast!
		}		
	}

	protected void checkStores() throws PersistenceException {
		for (WikiWordStore store: stores) {
			((WikiWordStoreBuilder)store).checkConsistency(); //XXX: ugly cast!
		}		
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
				initAgenda(agenda);
			}

			if (operation == Operation.FRESH) {
				section("-- purge --------------------------------------------------");
				initializeStores(true, getDropWarnings()); //FIXME: don't purge warning always... but when?!
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

	public int getExitCode() {
		return exitCode;
	}

	public void setOperation(Operation op) {
		operation = op;
	}
}
