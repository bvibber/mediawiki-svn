package de.brightbyte.wikiword.builder;

import java.util.Map;

import de.brightbyte.application.Agenda;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.processor.AbstractPageProcessor;
import de.brightbyte.wikiword.store.builder.IncrementalStoreBuilder;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public abstract class AbstractImporter extends AbstractPageProcessor implements WikiWordImporter {
	private WikiWordStoreBuilder store;
	private int safepointInterval = 30 * 1000;
	private int safepointTicks = 0;
	private int safepointNumber = 0;
	

	public AbstractImporter(WikiTextAnalyzer analyzer, WikiWordStoreBuilder store, TweakSet tweaks) {
		super(analyzer, tweaks);

		if (store==null) throw new NullPointerException();

		safepointInterval = tweaks.getTweak("importer.safepointInterval", safepointInterval);
		
		this.store = store;
	}
	
	@Override
	public void reset() {
		super.reset();
		safepointTicks = 0;
	}
	
	public int getSafepointInterval() {
		return safepointInterval;
	}

	public void setSafepointInterval(int safepointInterval) {
		this.safepointInterval = safepointInterval;
	}

	public void beforePages() throws PersistenceException {
		super.beforePages();
		store.prepareImport();
	}
	
	public void afterPages() throws PersistenceException {
		super.afterPages();
		flushSafepoint("handlePage");
		store.finalizeImport();
		memoryTrackerChunk();
	}
	
	protected void flush() throws PersistenceException {
		store.flush();
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
		memoryTrackerChunk();
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
	
	protected void storeWarning(int rcId, String problem, String details) {
		try {
			store.storeWarning(rcId, problem, details);
		} catch (PersistenceException e) {
			out.error("failed to store warning!", e);
		}
	}
	
	
	protected void flushSafepoint(String context) throws PersistenceException {
		Agenda.Record rec = getAgenda().getCurrentRecord();
		if (!"handlePage".equals(rec.context)) throw new IllegalStateException("bad agenda record context when attempting to store safepoint: expected '"+context+"', found '"+rec.context+"'");
		if (!rec.task.startsWith("analysis-safepoint#")) throw new IllegalStateException("bad agenda record task when attempting to store safepoint: expected prefix 'analysis-safepoint#', found '"+rec.task+"'");
		
		if (rec.state==Agenda.State.STARTED) {
			out.info("=== FLUSHING SAFEPOINT#"+safepointNumber+" ===");
			flush();
			endTask(context, "analysis-safepoint#"+safepointNumber);
		}
	}
	
	protected boolean prepareStep(int id) throws PersistenceException{
		boolean doit = true;
		
		if (safepointTicks==0) {					
			String state = "lastRcId_="+lastRcId+",nextPageId_="+id;
			if (!beginTask("handlePage", "analysis-safepoint#"+safepointNumber, state)) {
				out.info("=== SKIPPING BLOCK FOR SAFEPOINT#"+safepointNumber+" ===");
				skip = safepointInterval; //FIXME: make sure we are not off-by-one here! 
				doit = false;
			}
			else {
				if (getAgenda().isTaskDirty() && store instanceof IncrementalStoreBuilder) {
					Agenda.Record rec = getAgenda().getCurrentRecord();

					int delAfter = (Integer)rec.parameters.get("lastRcId_");
					out.info("=== DIRTY BLOCK FOR SAFEPOINT#"+safepointNumber+", Deleting entries starting after id: #"+delAfter+" ===");
					
					deleteDataAfter(delAfter); 
				}
				
				out.info("=== BEGINNING BLOCK FOR SAFEPOINT#"+safepointNumber+": "+getAgenda().getCurrentRecord().parameters+" ===");
			}
		}
		
		return doit;
	}
	
	protected void deleteDataAfter(int delAfter) throws PersistenceException {
		((IncrementalStoreBuilder)store).prepareMassProcessing(); 
		((IncrementalStoreBuilder)store).deleteDataAfter(delAfter, false); 
		((IncrementalStoreBuilder)store).prepareMassInsert(); 
	}
	
	protected void concludeStep() throws PersistenceException{
		safepointTicks++;
		if (safepointTicks>=safepointInterval) {
			flushSafepoint("handlePage");
			safepointTicks = 0;
			safepointNumber++;
		}
	}	
	
}
