package de.brightbyte.wikiword.processor;

import java.io.File;
import java.io.IOException;
import java.text.MessageFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Date;
import java.util.Random;

import de.brightbyte.application.Arguments;
import de.brightbyte.data.ChunkyBitSet;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.io.LogOutput;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.SystemUtils;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.RevisionInfo;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.AnalyzerUtils;
import de.brightbyte.wikiword.analyzer.WikiPage;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextSniffer;

public abstract class AbstractPageProcessor implements WikiWordPageProcessor {
	
	private int progressInterval = 1000;
	
	protected WikiTextAnalyzer analyzer;
	
	private ChunkedProgressRateTracker pageTracker;
	private ChunkedProgressRateTracker bulkTracker;
	private MemoryTracker memoryTracker;
	
	private int progressTicks = 0;
	
	private boolean first = true;
	protected TweakSet tweaks;
	
	protected String skipTo = null;
	protected int skipToId = -1;
	protected int skip = 0;
	protected int limit = -1;
	protected int lastRcId = 0;
	
	private Random skipRandom;
	private int skipRandomBias = 0;
	
	private ChunkyBitSet stoplist= null;
	
	protected LeveledOutput out;
	
	protected boolean forceTitleCase = false; //NOTE: per default, trust title case supplied by import driver!
	private Collection<WikiPageFilter> filters;
	protected String fileecoding = SystemUtils.getPropertySafely("file.encoding", "utf-8");

	public AbstractPageProcessor(WikiTextAnalyzer analyzer, TweakSet tweaks) {
		if (analyzer==null) throw new NullPointerException();

		this.analyzer = analyzer;
		this.tweaks = tweaks;
		
		progressInterval = tweaks.getTweak("importer.progressInterval", progressInterval);
		
		if (tweaks.getTweak("importer.catchDupes", false)) stoplist = new ChunkyBitSet();
		
		out = new LogOutput();
	}
	
	public void loadTitleList(File f, String enc) throws PersistenceException {
		  if (enc==null) enc = fileecoding;
		
		   out.info("loading page title from "+f);
			TitleSetFilter filter = new  TitleSetFilter(f, enc);
			addFilter(filter);
	}
	
	public void addFilter(WikiPageFilter filter) {
			if (filter==null) return;
			if (filters==null) filters = new ArrayList<WikiPageFilter>();
			filters.add(filter);
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
		pageTracker = new ChunkedProgressRateTracker("pages");
		bulkTracker = new ChunkedProgressRateTracker("chars");
		memoryTracker = new MemoryTracker();
		progressTicks = 0;
	}
	
	public void trackerChunk() {
		pageTracker.chunk();
		bulkTracker.chunk();
		memoryTracker.chunk();
		
		out.info("--- "+new Date()+" ---");
		out.info("- "+pageTracker);
		out.info("- "+bulkTracker);
		out.info("- "+memoryTracker);
	}

	public void memoryTrackerChunk() {
		memoryTracker.chunk();
		
		out.info("--- "+new Date()+" ---");
		out.info("- "+memoryTracker);
	}
	
	/*
	public boolean isSafepoint(Agenda.Record rec) {
		return rec.task.startsWith("analysis-safepoint#") && rec.parameters.get("lastRcId")!=null;
	}
	*/
	
	public void handlePage(RevisionInfo revision, String text) throws IOException, PersistenceException {
		int id = revision.getPageId();
		String title = revision.getPageTitle();
		
		if (stoplist!=null) {
			if (stoplist.set(id, true)) {
				out.warn("WARNING: ignored dupe: #"+revision.getPageId()+" "+revision.getPageTitle());
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
				
				if (prepareStep(id)) {
					pageTracker.step();
					bulkTracker.step(text.length());
					memoryTracker.step();
					
					int rcId = importPage(revision, text);
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
		
		concludeStep();
	}
	
	protected boolean prepareStep(int id) throws PersistenceException{
			//noop
			return true;
	}
	
	protected void concludeStep() throws PersistenceException{
		//noop
	}
	
	protected abstract void flush() throws PersistenceException;

	protected boolean isRelevant(WikiPage analyzerPage) {
		ResourceType ptype = analyzerPage.getResourceType();
		CharSequence text = analyzerPage.getText();
		int namespace = analyzerPage.getNamespace();
		CharSequence title = analyzerPage.getTitle();

		if (text.length()==0) {
			out.warn("blank page "+title); 
			return false;
		}
		
		if (ptype==ResourceType.OTHER || ptype==ResourceType.UNKNOWN) {
			out.trace("bad page "+title+" in namespace "+namespace+" with type "+ptype); 
			return false;
		}
		
		if (filters!=null) {
			for (WikiPageFilter filter: filters) {
				if (!filter.matches(analyzerPage)) {
					out.trace("page "+title+" matches filter "+filter.getName()); 
					return false;
				}
			}
		}
		
		return true;
	}	
	
	protected final int importPage(RevisionInfo revision, String text) throws PersistenceException {
		    WikiPage page = analyzer.makePage(revision.getNamespace(), revision.getPageTitle(), text, forceTitleCase);

		    //TODO: check if page is stored. if up to date, skip. if older, update. if missing, create. optionally force update.
		    
			if (!isRelevant(page)) {
				out.trace("ignored page "+revision.getPageTitle()+" in namespace "+revision.getNamespace()); 
				return -1;
			}
		    
			return importPage(page, revision); 
	}

	protected abstract int importPage(WikiPage page, RevisionInfo revision) throws PersistenceException;
	
	public int getProgressInterval() {
		return progressInterval;
	}

	public void setProgressInterval(int progressInterval) {
		this.progressInterval = progressInterval;
	}

	public static void declareOptions(Arguments args) {
		args.declare("from", "f", true, String.class, "ignores all pages in the input before (but excluding) the one with the given title");
		args.declare("after", "a", true, String.class, "ignores all pages in the input until (and including) the one with the given title");
		args.declare("limit", "l", true, String.class, "maximum number of pages to process");
		args.declare("skip", "k", true, String.class, "number number of pages to skip before starting to process. " +
				"if --from or --after are given, this number is counted from the position the given title occurrs at. " +
				"Otherwise, it is counted from the beginning of the dump");
		
		args.declare("random", null, true, Integer.class, "random skip coefficient - " +
				"set this to nnn to only process every nnn'th random page.");
		args.declare("randomseed", null, true, Integer.class, "sets the seed for the random number generator used with --random; " +
				"can be used to reproduce random sets of pages.");

		args.declare("catchdupes", null, false, Boolean.class, "catch and ignore duplicates (uses more memory)");

		args.declare("titlelist", null, true, String.class, "file containing a list if page titles to filter by, one per line");
		args.declare("fileencoding", null, true, String.class, "encoding to use when reading files");

		//args.declare("nodef", null, true, String.class, "do not extract and store definitions (improves speed)");
		//args.declare("nolinks", null, true, String.class, "do not store links between pages (improves speed)");
		//args.declare("noterms", null, true, String.class, "do not store term usage");
		//args.declare("wikitext", null, true, String.class, "store full raw wikitext");
		//args.declare("plaintext", null, true, String.class, "store full stripped text");
	}

	public void configure(Arguments args) throws Exception {		
		if (args.isSet("from")) {
			this.skipTo = args.getStringOption("from", null);
		}
		else if (args.isSet("after")) {
			this.skipTo = args.getStringOption("after", null);
			this.skip = 1;
		}

		if (skipTo!=null) skipTo = AnalyzerUtils.replaceUnderscoreBySpace(analyzer.normalizeTitle(skipTo)).toString();
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
		
		fileecoding = args.getStringOption("fileencoding", "utf-8");

		if (args.isSet("titlelist")) {
			String f = args.getStringOption("titlelist", null);
			loadTitleList(new File(f), fileecoding);
		}
	}

	public void initialize(NamespaceSet namespaces, boolean titleCase) {
		analyzer.initialize(namespaces, titleCase);
	}

	public void prepare() throws PersistenceException {
		//noop
	}

	public void beforePages() throws PersistenceException {
		//noop		
	}
	
	public void afterPages() throws PersistenceException {
		trackerChunk();		
	}
	
	public void finish() throws PersistenceException {
		flush();
	}

	/*
	public boolean shouldRunAnalyze() throws PersistenceException {
		return shouldRun("analyze", "id=0,name=null");
	}
	*/
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

		storeWarning(rcId, problem, details);
	}
	
	protected void storeWarning(int rcId, String problem, String details) {
		// noop
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
