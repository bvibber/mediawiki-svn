package de.brightbyte.wikiword.builder;

import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public abstract class ImportDump<S extends WikiWordStoreBuilder> extends ImportApp<S> {

	public ImportDump(String agendaTask) {
		super(agendaTask, false, true);
	}

	protected URL dumpFile;

	@Override
	protected void applyArguments() {
		String d = getTargetFileName();
		
		if (args.isSet("url")) {
			try {
				dumpFile = new URL(d);
			} catch (MalformedURLException e) {
				throw new IllegalArgumentException("bad url: "+d, e);
			}
		}
		else {
			try {
				dumpFile = new File(d).toURI().toURL();
			} catch (MalformedURLException e) {
				throw new RuntimeException("failed to generate local file url for `"+d+"`");
			}
		}
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		ConceptImporter.declareOptions(args);
		
		args.declareHelp("<wiki>", null);
		args.declareHelp("<dump-file>", "the dump file to process. If --url is set, this is read as a full URL");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the name given by, or " +
			"guessed from, the <wiki> parameter)");
		args.declare("url", null, false, Boolean.class, "read the <dump-file> parameter as a full URL");
	}

	@Override
	protected void run() throws Exception {
		//LocalConceptStoreBuilder store = (LocalConceptStoreBuilder)this.store;
		
		/*
		if (phase!=null) {
			out.println("*** forcing phase: "+phase+" ***");
			store.getAgenda().forceLastTask(phase);
		}
		*/

		WikiTextAnalyzer analyzer = WikiTextAnalyzer.getWikiTextAnalyzer(getCorpus(), tweaks); 
		WikiWordImporter importer = newImporter(analyzer, store, tweaks);
		importer.setLogOutput(getLogOutput());
		importer.configure(args);
		
		/*
		if (!fresh && agenda.canContinue(agendaTask)) { 
			Agenda.Record logPoint = agenda.getLastRecord();
			out.println("Continuing from log point "+logPoint);
			
			int deleteAfter = -1;	
			int skipTo = -1;

			if (logPoint!=null && logPoint.state != Agenda.State.COMPLETE) {
				if (logPoint.task.equals(agendaTask) 
						|| logPoint.task.equals("prepare")
						|| logPoint.task.equals("analysis")) { //XXX: "analysis"? really?
					deleteAfter = 0;
				}
				else if (importer.isSafepoint(logPoint)) {
					deleteAfter = (Integer)logPoint.parameters.get("lastRcId");
					skipTo = (Integer)logPoint.parameters.get("nextPageId");
				}
			}
			
			if (deleteAfter>=0) {
				out.println("Deleting entries starting at id: #"+deleteAfter);
				store.deleteDataAfter(deleteAfter, true); 
			}			
			
			if (skipTo>=0) {
				importer.setSkipToId( skipTo );
				out.println("Skipping to page #"+skipTo);
			}
		}
		*/
		
		///////////////////////// main import run ////////////////////////////////////
		if (agenda.beginTask("ImportDump.run", "analysis")) {
			ImportDriver driver = new DumpImportDriver(dumpFile, getLogOutput(), tweaks);
			driver.runImport(importer);
			agenda.endTask("ImportDump.run", "analysis");
		}
		//////////////////////////////////////////////////////////////////////////////
		
		afterImport();
		
		if (args.isSet("showstats")) args.setOption("buildstats", true);
						
		section("-- dbstats --------------------------------------------------");
		store.dumpTableStats(getLogOutput()); 
		
		int w = store.getNumberOfWarnings(); //XXX: warnings per root-task!
		if (w==0) info("no warnings");
		else warn("******* NOTE: "+w+" warnings collected! *******");
	}

	protected void afterImport() throws PersistenceException {
		//noop
	}

	protected abstract WikiWordImporter newImporter(WikiTextAnalyzer analyzer, S store, TweakSet tweaks);

}
