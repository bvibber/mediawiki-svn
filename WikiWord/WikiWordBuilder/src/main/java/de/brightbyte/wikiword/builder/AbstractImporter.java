package de.brightbyte.wikiword.builder;

import java.io.IOException;
import java.text.MessageFormat;
import java.util.Date;
import java.util.Map;
import java.util.Random;

import de.brightbyte.application.Agenda;
import de.brightbyte.application.Arguments;
import de.brightbyte.data.ChunkyBitSet;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.io.LogOutput;
import de.brightbyte.io.Output;
import de.brightbyte.job.Progress;
import de.brightbyte.job.ProgressRateTracker;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextSniffer;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public abstract class AbstractImporter implements WikiWordImporter {
	
	protected static class Tracker extends ProgressRateTracker {
		protected long counter = 0;
		protected String name;
		
		public Tracker(String name) {
			this.name = name;
		}
		
		protected void step() {
			step(1);
		}
		
		protected void step(int c) {
			counter+= c;
		}
		
		protected void chunk() {
			super.progress(new Progress.Event(Progress.PROGRESS, null, name, 1, position+counter, null));
			counter = 0;
		}
		
		@Override
		public String toString() {
			return MessageFormat.format("{0}: {1,number,0} ({2,number,0.0}/sec, currently {3,number,0.0}/sec)", name, position, getAverageRate(), getCurrentRate());
		}
	}
	
	private int progressInterval = 1000;
	private int safepointInterval = 30 * 1000;
	
	private WikiWordStoreBuilder store;
	protected WikiTextAnalyzer analyzer;
	
	private Tracker pageTracker;
	private Tracker bulkTracker;
	private int progressTicks = 0;
	private int safepointTicks = 0;
	private int safepointNumber = 0;
	
	private boolean first = true;
	protected TweakSet tweaks;
	
	private String skipTo = null;
	private int skipToId = -1;
	private int skip = 0;
	private int limit = -1;
	private int lastRcId = 0;
	
	private Random skipRandom;
	private int skipRandomBias = 0;
	
	private ChunkyBitSet stoplist= null;
	
	protected LeveledOutput out;
	
	protected boolean forceTitleCase = false; //NOTE: per default, trust title case supplied by import driver!

	public AbstractImporter(WikiTextAnalyzer analyzer, WikiWordStoreBuilder store, TweakSet tweaks) {
		if (analyzer==null) throw new NullPointerException();
		if (store==null) throw new NullPointerException();
		
		this.analyzer = analyzer;
		this.store = store;
		this.tweaks = tweaks;
		
		progressInterval = tweaks.getTweak("importer.progressInterval", progressInterval);
		safepointInterval = tweaks.getTweak("importer.safepointInterval", safepointInterval);
		
		if (tweaks.getTweak("importer.catchDupes", false)) stoplist = new ChunkyBitSet();
		
		out = new LogOutput();
	}
	
	public void setLogLevel(int level) {
		if (out == null) return;
		if (!(out instanceof LogOutput)) return;
		
		((LogOutput)out).setLogLevel(level);
	}

	public void setLogOutput(Output out) {
		if (!(out instanceof LeveledOutput)) out = new LogOutput(out);
		this.out = (LeveledOutput)out;
	}
	
	public void reset() {
		pageTracker = new Tracker("pages");
		bulkTracker = new Tracker("chars");
		progressTicks = 0;
		safepointTicks = 0;
	}
	
	public void trackerChunk() {
		pageTracker.chunk();
		bulkTracker.chunk();
		
		out.info("--- "+new Date()+" ---");
		out.info("- "+pageTracker);
		out.info("- "+bulkTracker);
	}
	
	/*
	public boolean isSafepoint(Agenda.Record rec) {
		return rec.task.startsWith("analysis-safepoint#") && rec.parameters.get("lastRcId")!=null;
	}
	*/
	
	public void handlePage(int id, int namespace, String title, String text, Date timestamp) throws IOException, PersistenceException {
		if (stoplist!=null) {
			if (stoplist.set(id, true)) {
				out.warn("WARNING: ignored dupe: #"+id+" "+namespace+":"+title);
				return;
			}
		}
		
		boolean count = true;
		if (skipTo!=null) { //NOTE: bypasses skip count!
			count = false;
			if (skipTo.equals(title)) skipTo = null;
		}
		
		if (skipToId>0) { //NOTE: bypasses skip count!
			count = false;
			if (skipToId == id) skipToId = -1;
		}
		
		if (count && skip>0) skip--; //NOTE: take affect only after skipTo/skipToId.
		 
		if (skipTo==null && skipToId <= 0 && skip<=0 && limit!=0) {
			if (skipRandom==null || skipRandom.nextInt(skipRandomBias) == 0) {
				if (first) {
					trackerChunk();
					first = false;
				}
				
				boolean doit = true;
				
				if (safepointTicks==0) {					
					String state = "lastRcId_="+lastRcId+",nextPageId_="+id;
					if (!beginTask("handlePage", "analysis-safepoint#"+safepointNumber, state)) {
						out.info("=== SKIPPING BLOCK FOR SAFEPOINT#"+safepointNumber+" ===");
						skip = safepointInterval; //FIXME: make sure we are not off-by-one here! 
						doit = false;
					}
					else {
						if (getAgenda().isTaskDirty()) {
							Agenda.Record rec = getAgenda().getCurrentRecord();

							int delAfter = (Integer)rec.parameters.get("lastRcId_");
							out.info("=== DIRTY BLOCK FOR SAFEPOINT#"+safepointNumber+", Deleting entries starting after id: #"+delAfter+" ===");
							store.deleteDataAfter(delAfter, false); //FIXME: make sure we are not off by one!
						}
						
						out.info("=== BEGINNING BLOCK FOR SAFEPOINT#"+safepointNumber+": "+getAgenda().getCurrentRecord().parameters+" ===");
					}
				}
				
				if (doit) {
					pageTracker.step();
					bulkTracker.step(text.length());
					
					int rcId = importPage(namespace, title, text, timestamp);
					if (rcId>0) lastRcId = rcId;
	
					progressTicks++;
					if (progressTicks>progressInterval) {
						trackerChunk();
						progressTicks = 0;
					}
					
					if (limit>0) limit --; //TODO: abort quickly when limit reaches 0. no point in parsing more xml.
				}
			}
		}
		
		safepointTicks++;
		if (safepointTicks>=safepointInterval) {
			flushSafepoint("handlePage");
			safepointTicks = 0;
			safepointNumber++;
		}
	}
	
	protected void flushSafepoint(String context) throws PersistenceException {
		Agenda.Record rec = getAgenda().getCurrentRecord();
		if (!"handlePage".equals(rec.context)) throw new IllegalStateException("bad agenda record context when attempting to store safepoint: expected '"+context+"', found '"+rec.context+"'");
		if (!rec.task.startsWith("analysis-safepoint#")) throw new IllegalStateException("bad agenda record task when attempting to store safepoint: expected prefix 'analysis-safepoint#', found '"+rec.task+"'");
		
		if (rec.state==Agenda.State.STARTED) {
			out.info("=== FLUSHING SAFEPOINT#"+safepointNumber+" ===");
			store.flush();
			endTask(context, "analysis-safepoint#"+safepointNumber);
		}
	}
	
	protected abstract int importPage(int namespace, String title, String text, Date timestamp) throws PersistenceException;

	public int getProgressInterval() {
		return progressInterval;
	}

	public void setProgressInterval(int progressInterval) {
		this.progressInterval = progressInterval;
	}

	public int getSafepointInterval() {
		return safepointInterval;
	}

	public void setSafepointInterval(int safepointInterval) {
		this.safepointInterval = safepointInterval;
	}

	public static void declareOptions(Arguments args) {
		args.declare("from", "f", true, String.class, "ignores all pages in the dump before (but excluding) the one with the given title");
		args.declare("after", "a", true, String.class, "ignores all pages in the dump until (and including) the one with the given title");
		args.declare("limit", "l", true, String.class, "maximum number of pages to process");
		args.declare("skip", "k", true, String.class, "number number of pages to skip before starting to process. " +
				"if --from or --after are given, this number is counted from the position the given title occurrs at. " +
				"Otherwise, it is counted from the beginning of the dump");
		
		args.declare("random", null, true, Integer.class, "random skip coefficient - " +
				"set this to nnn to only process every nnn'th random page.");
		args.declare("randomseed", null, true, Integer.class, "sets the seed for the random number generator used with --random; " +
				"can be used to reproduce random sets of pages.");

		args.declare("catchdupes", null, false, Boolean.class, "catch and ignore duplicates (uses more memory)");
		
		//args.declare("nodef", null, true, String.class, "do not extract and store definitions (improves speed)");
		//args.declare("nolinks", null, true, String.class, "do not store links between pages (improves speed)");
		//args.declare("noterms", null, true, String.class, "do not store term usage");
		//args.declare("wikitext", null, true, String.class, "store full raw wikitext");
		//args.declare("plaintext", null, true, String.class, "store full stripped text");
	}

	public void configure(Arguments args) {		
		if (args.isSet("from")) {
			this.skipTo = args.getStringOption("from", null);
		}
		else if (args.isSet("after")) {
			this.skipTo = args.getStringOption("after", null);
			this.skip = 1;
		}

		if (skipTo!=null) skipTo = WikiTextAnalyzer.replaceUnderscoreBySpace(analyzer.normalizeTitle(skipTo)).toString();
		this.limit = args.getIntOption("limit", -1);
		this.skip = args.getIntOption("skip", 0);
		
		this.skipRandomBias = args.getIntOption("random", 0);
		if (this.skipRandomBias>0) {
			this.skipRandom = new Random();

			if (args.isSet("randomseed")) {
				this.skipRandom.setSeed( args.getIntOption("randomseed", 0) );   
			}
		}
		
		if (args.isSet("catchdupes")) {
			if (this.stoplist==null) this.stoplist = new ChunkyBitSet();
		}
	}

	public void initialize(NamespaceSet namespaces, boolean titleCase) {
		analyzer.initialize(namespaces, titleCase);
	}

	public void prepare() throws PersistenceException {
		//store.prepare(purge, dropAll);
	}

	public void afterPages() throws PersistenceException {
		trackerChunk();		
		flushSafepoint("handlePage");
	}
	
	public void finish() throws PersistenceException {
		store.flush();
	}

	/*
	public boolean shouldRunAnalyze() throws PersistenceException {
		return shouldRun("analyze", "id=0,name=null");
	}
	*/
	
	@Deprecated
	public boolean beginPrimitiveTask(String context, String task) throws PersistenceException {
		return store.getAgenda().beginPrimitiveTask(context, task);
	}

	@Deprecated
	public boolean beginPrimitiveTask(String context, String task, String params) throws PersistenceException {
		return store.getAgenda().beginPrimitiveTask(task, params);
	}

	@Deprecated
	public boolean beginPrimitiveTask(String context, String task, Map<String, Object> params) throws PersistenceException {
		return store.getAgenda().beginPrimitiveTask(context, task, params);
	}

	public boolean beginTask(String context, String task) throws PersistenceException {
		return store.getAgenda().beginTask(context, task);
	}

	public boolean beginTask(String context, String task, String params) throws PersistenceException {
		return store.getAgenda().beginTask(context, task, params);
	}

	public boolean beginTask(String context, String task, Map<String, Object> params) throws PersistenceException {
		return store.getAgenda().beginTask(context, task, params);
	}
	
	public Agenda getAgenda() throws PersistenceException {
		return store.getAgenda();
	}
	
	public void endTask(String context, String task) throws PersistenceException {
		store.getAgenda().endTask(context, task);
	}

	public int getSkip() {
		return skip;
	}

	public void setSkip(int skip) {
		this.skip = skip;
	}

	public Random getSkipRandom() {
		return skipRandom;
	}

	public void setSkipRandom(Random skipRandom) {
		this.skipRandom = skipRandom;
	}

	public int getSkipRandomBias() {
		return skipRandomBias;
	}

	public void setSkipRandomBias(int skipRandomBias) {
		this.skipRandomBias = skipRandomBias;
	}

	public String getSkipTo() {
		return skipTo;
	}

	public void setSkipTo(String skipTo) {
		this.skipTo = skipTo;
	}

	public int getSkipToId() {
		return skipToId;
	}

	public void setSkipToId(int skipToId) {
		this.skipToId = skipToId;
	}

	public boolean getForceTitleCase() {
		return forceTitleCase;
	}

	public void setForceTitleCase(boolean forceTitleCase) {
		this.forceTitleCase = forceTitleCase;
	}

	
	public void warn(int rcId, String problem, String details, Exception ex) {
		String msg = problem+": "+details;
		if (rcId>0) msg += " (in resource #"+rcId+")";
		
		out.warn(msg);
		
		if (ex!=null) details += "\n" + ex;

		try {
			store.storeWarning(rcId, problem, details);
		} catch (PersistenceException e) {
			out.error("failed to store warning!", e);
		}
	}
	
	protected boolean checkTerm(int rcId, String text, String descr, int ctxId) throws PersistenceException {
		int c = text.length();
		String problem = null;
		
		if (c<analyzer.getMinTermLength()) {
			problem = "term too short";
		}

		if (c>analyzer.getMaxTermLength()) {
			problem = "term too long";
		}
		
		if (problem!=null) {
			descr = MessageFormat.format(descr, ctxId);
			String details = descr + ": " + text;
			
			warn(rcId, problem, details, null);
			return true;
		}
		
		return false;
	}

	protected boolean checkName(int rcId, String name, String descr, int ctxId) throws PersistenceException {
		int c = name.length();
		String problem = null;
		
		if (c<analyzer.getMinTermLength()) {
			problem = "name too short";
		}

		if (c>analyzer.getMaxTermLength()) {
			problem = "name too long";
		}
		
		if (problem==null && analyzer.isBadTitle(name)) {
			problem = "bad name";
		}
		
		if (problem!=null) {
			descr = MessageFormat.format(descr, ctxId);
			String details = descr + ": " + name;
			
			warn(rcId, problem, details, null);
			return true;
		}
		
		return checkSmellsLikeWiki(rcId, name, descr, ctxId);
	}
	
	protected WikiTextSniffer sniffer = new WikiTextSniffer(); //TODO: optional!
	
	protected boolean checkSmellsLikeWiki(int rcId, String text, String descr, int ctxId) throws PersistenceException {
		if (sniffer==null) return false;
		
		String w = sniffer.sniffWikiTextLocation(text);
		if (w==null) return false;
		
		descr = MessageFormat.format(descr, ctxId);
		String problem = "smells like wiki text";
		String details = descr + ": " + w;
		
		warn(rcId, problem, details, null);
		return true;
	}
	
}
