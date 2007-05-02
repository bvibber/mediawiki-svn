/*
 * Created on Feb 11, 2007
 *
 */
package org.wikimedia.lsearch.index;

import java.io.File;
import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Date;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.List;
import java.util.Set;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.beans.ReportId;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;

/**
 * Indexer.  
 * 
 * @author rainman
 *
 */
public class IndexThread extends Thread {
	static org.apache.log4j.Logger log = Logger.getLogger(IndexThread.class);
	// these determin the state after current operation is finished 
	protected boolean quit;
	protected boolean suspended;
	protected boolean flushNow;
	protected boolean makeSnapshotNow;
	
	// flush if any of these are exceeded
	protected int maxQueueCount;
	protected int maxQueueTimeout;
	protected long snapshotInterval;
	
	/** hashtable of updates while they are being processed */ 
	protected Hashtable<String,Hashtable<String,IndexUpdateRecord>> workUpdates;
	/** time of last updates flush */
	protected long lastFlush;
	protected WikiIndexModifier indexModifier;	
	protected static GlobalConfiguration global;
	protected static Object threadLock = new Object();
	/** This is where pages are queued for processing 
	 * 	Structure: dbname -> hashtable(ns:title -> indexUpdateRecord) 
	 */
	protected static Hashtable<String,Hashtable<String,IndexUpdateRecord>> queuedUpdates = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();
	
	/** Local reports: reportd Id -> list (reports from distributed indexes) */ 
	protected static Hashtable<ReportId,List<IndexReportCard>> reports = new Hashtable<ReportId,List<IndexReportCard>>();

	/** dbrole -> ns:title -> ReportId <br/>
	 * Records the latest reportid (if new updates arrives 
	 * while old is till processed by some remote indexer) */
	protected static Hashtable<String,Hashtable<String,ReportId>> pendingUpdates = new Hashtable<String,Hashtable<String,ReportId>>();
	
	/** Thread that enqueues updates to distributed indexers in a batch */
	protected static MessengerThread messenger = null;
	
	/**
	 * Default constructor
	 * 
	 */
	public IndexThread(){
		quit = false;
		suspended = false;
		flushNow = false;
		makeSnapshotNow = false;
		lastFlush = System.currentTimeMillis();		
		
		Configuration config = Configuration.open();
		maxQueueCount = config.getInt( "Index", "maxqueuecount", 5000 ); // old default = 500 docs
		maxQueueTimeout = config.getInt( "Index", "maxqueuetimeout", 12 )*1000; // old default = 3600ms
		snapshotInterval = (long) (config.getDouble("Index","snapshotinterval",5)*60*1000); // default 5 minutes
		
		global = GlobalConfiguration.getInstance();
		indexModifier = new WikiIndexModifier();
		messenger = MessengerThread.getInstance();
	}
	
	/**
	 * Runs the thread (always call via start())
	 * 
	 */
	public void run() {
		log.debug("Starting IndexThread...");
		long lastSnapshot = System.currentTimeMillis();
		while(!quit){			
			checkReports();
			applyUpdates();
			// make snapshot?
			if(makeSnapshotNow || (System.currentTimeMillis() - lastSnapshot) > snapshotInterval){
				flushNow = true;
				applyUpdates();
				makeSnapshot();
				lastSnapshot = System.currentTimeMillis();
				makeSnapshotNow = false;
			}
			try {
				Thread.sleep(1000);
			} catch (InterruptedException e) {
				log.warn("IndexThread sleep interrupted with message: "+e.getMessage());
			}
		}
		if(queuedUpdatesExist())
			applyUpdates();
		WikiIndexModifier.closeAllModifiers();
	}
	
	/**
	 * For split indexes, a reporting system is needed to manage
	 * all parts of the split index as one logical index. I.e.
	 * if an index update needs to be made, it's sent to all 
	 * parts of the index, and those need to report back if the
	 * update operation has succeeded of failed - the article will
	 * be updated on the index part where it's found. However, if
	 * it's not found it still needs to be added to the index. 
	 * 
	 * Also a system for keeping track of current updates is needed
	 * if during the report-back phrase of update new update arrives. 
	 * 
	 * @param rc
	 */
	public static void enqueuReport(IndexReportCard rc){
		synchronized(threadLock){
			List<IndexReportCard> cards = reports.get(rc.getId());
			if(cards == null){
				log.warn("Unexpected report "+rc);
				return;
			}
			cards.add(rc);
		}
	}
	
	/** See enqueuReport(IndexReportCard) */
	public static void enqueuReports(IndexReportCard[] rcs){
		synchronized(threadLock){
			for(IndexReportCard rc : rcs){
				List<IndexReportCard> cards = reports.get(rc.getId());
				if(cards == null){
					log.warn("Unexpected report "+rc);
					continue;
				}
				cards.add(rc);
			}
		}
	}
	
	/** Check if complete reports have been recieved and process them */
	protected void checkReports(){
		if(reports.size() != 0 ){
			Hashtable<ReportId,List<IndexReportCard>> reportsLocal;
			synchronized (threadLock) {
				 reportsLocal = (Hashtable<ReportId, List<IndexReportCard>>) reports.clone();
			}
			for(Entry<ReportId,List<IndexReportCard>> entry : reportsLocal.entrySet()){				
				ReportId reportId = entry.getKey();
				IndexId iid = reportId.getIndexId();
				if(iid.isSplit()){
					int splitFactor = iid.getSplitFactor();
					if(entry.getValue().size() == splitFactor){
						// got all reports, process						
						// for now only process update reports
						synchronized(threadLock){
							if(!reportId.equals(pendingUpdates.get(iid.toString()).get(reportId.getKey()))){
								log.info(reportId+" has a newer update!");
								reports.remove(entry.getKey());
								continue;
							} else{
								pendingUpdates.get(iid.toString()).remove(reportId.getKey());
							}
						}
						if(reportId.record.doAdd() && reportId.record.doDelete()){
							boolean succAdd=false, succDel=false;
							for(IndexReportCard card : entry.getValue()){
								if(card.isSuccAdd())
									succAdd = true;
								if(card.isSuccDelete())
									succDel = true;
							}
							if(succAdd && succDel){ // all ok
								log.debug("Good report for "+reportId);
							} else if((!succAdd && succDel) || (succAdd && !succDel))
								log.warn("Inconsistent report for "+reportId);
							else if(!succAdd && !succDel){
								// enqueue addition
								int random = (int)Math.floor(Math.random()*splitFactor) + 1;
								IndexId iidp = iid.getPart(random);
								IndexUpdateRecord record = (IndexUpdateRecord) reportId.record.clone();			
								// enqueue on a randomly choosen index part
								record.setIndexId(iidp);
								record.setAlwaysAdd(true);
								record.setReportBack(false);
								record.setAction(IndexUpdateRecord.Action.ADD);
								enqueueRemotely(iidp.getIndexHost(),record);
							}
						}
						// processed, remove from queue
						reports.remove(entry.getKey());
					}
				}
				
			}
		}
	}
	
	protected void deleteDirRecursive(File file){
		if(!file.exists())
			return;
		else if(file.isDirectory()){
			File[] files = file.listFiles();
			for(File f: files)
				deleteDirRecursive(f);
			file.delete();
			log.debug("Deleted old snapshot at "+file);
		} else{
			file.delete();			
		}
	}
	
	/**
	 * Make a snapshot of all changed indexes
	 *
	 */
	protected void makeSnapshot() {
		final String sep = Configuration.PATH_SEP;
		HashSet<IndexId> indexes = WikiIndexModifier.closeAllModifiers();
		IndexRegistry registry = IndexRegistry.getInstance();
		
		DateFormat df = new SimpleDateFormat("yyyyMMddHHmmss");
		String timestamp = df.format(new Date(System.currentTimeMillis()));
		log.debug("Making snapshots...");
		// check filesystem timestamps (for those for which we are unsure if they are updated)
		for( IndexId iid : global.getMyIndex()){
			if(indexes.contains(iid))
				continue;
			File indexdir = new File(iid.getIndexPath());
			if(indexdir.exists()){
				LocalIndex li = registry.getLatestSnapshot(iid);
				// check if the index has been updated since last snapshot
				if(li == null || li.timestamp < indexdir.lastModified()){
					if(li == null)
						log.debug("No previous snapshot for "+iid);
					indexes.add(iid);
				}
			}
		}
		for( IndexId iid : indexes ){
			log.info("Making snapshot for "+iid);
			String index = iid.getIndexPath();
			String snapshotdir = iid.getSnapshotPath();
			String snapshot = snapshotdir+sep+timestamp;
			// cleanup the snapshot dir for this iid
			File spd = new File(snapshotdir);
			if(spd.exists() && spd.isDirectory()){
				File[] files = spd.listFiles();
				for(File f: files)
					deleteDirRecursive(f);
			}
			new File(snapshot).mkdirs();
			File ind =new File(index);
			for(File f: ind.listFiles()){
				// use a cp -lr command for each file in the index
				String command = "/bin/cp -lr "+index+sep+f.getName()+" "+snapshot+sep+f.getName();
				Process copy;
				try {
					log.debug("Running shell command: "+command);
					copy = Runtime.getRuntime().exec(command);
					copy.waitFor();
				} catch (Exception e) {
					log.error("Error making snapshot "+snapshot+": "+e.getMessage());
					continue;
				}
			}
			registry.refreshSnapshots(iid);
			log.info("Made snapshot "+snapshot);			
		}
	}
	
	/**
	 * @return if there are queued updates
	 */
	public static boolean queuedUpdatesExist(){
		synchronized(threadLock){
			return queuedUpdates.size() > 0;
		}
	}
	
	/**
	 * Call this method from an XMLRPC or HTTP frontend to 
	 * enqueue a page update
	 * @param record
	 */
	public static void enqueue(IndexUpdateRecord record) {
		synchronized(threadLock){
			IndexId iid = record.getIndexId();
			if(iid == null || !iid.isLogical() || !iid.isMyIndex()){
				log.error("Got update for database "+iid+", however this node does not accept updates for this DB");
				return;
			}

			if( iid.isSingle() ){
				enqueueLocally(record);			
			} else if( iid.isMainsplit() ){
				IndexId piid;
				if( record.getArticle().getNamespace().equals("0") )
					piid = iid.getMainPart();
				else
					piid = iid.getRestPart();
				// set recipient to new host
				record.setIndexId(piid);
				enqueueRemotely(piid.getIndexHost(),record);
			} else if( iid.isSplit() ){
				int number = iid.getSplitFactor();
				Article a = record.getArticle();
				ReportId reportId = new ReportId(a.getPageId(),
						System.currentTimeMillis(),
						iid.toString(),
						record);
				Hashtable<String,ReportId> dbpending = pendingUpdates.get(iid.toString());
				if(dbpending == null){
					dbpending = new Hashtable<String,ReportId>();
					pendingUpdates.put(iid.toString(),dbpending);
				}
				dbpending.put(reportId.getKey(),reportId); // overwrite old values (if any)
				if(record.doDelete()){
					if(record.doAdd()){		
						// expect report on this reportId
						reports.put(reportId,Collections.synchronizedList(new ArrayList<IndexReportCard>()));
						record.setReportBack(true);
						record.setAlwaysAdd(false);
						record.setReportHost(global.getLocalhost());
						record.setReportId(reportId);						
					}
					// pass to all hosts the update record
					for(int i=1; i<=number; i++){
						IndexId iidp = iid.getPart(i);
						IndexUpdateRecord recordPart = (IndexUpdateRecord) record.clone();
						recordPart.setIndexId(iidp);
						enqueueRemotely(iidp.getIndexHost(),recordPart);						
					}
				} else{					
					int random = (int)Math.floor(Math.random()*number) + 1;
					IndexId iidp = iid.getPart(random);			
					// enqueue on a randomly choosen index part
					record.setIndexId(iidp);
					enqueueRemotely(iidp.getIndexHost(),record);
				}
			}
		}
	}
	/**
	 * Put update record on remote queue
	 * @param host
	 * @param record
	 */
	protected static void enqueueRemotely(String host, IndexUpdateRecord record) {
		IndexId iid = record.getIndexId();
		if(host.equals("127.0.0.1") || host.equals("localhost") || iid.isMyIndex()){
			// this is the target machine, enqueue locally
			enqueueLocally(record);
			return;
		}
		log.debug("Enqueueing "+record+" remotely on host "+host);
		messenger.enqueueRemotely(host,record);
	}

	/**
	 * Put update record on local update queue
	 * @param record
	 */
	static protected void enqueueLocally(IndexUpdateRecord record){
		synchronized (threadLock){
			IndexId iid = record.getIndexId();
			Hashtable<String,IndexUpdateRecord> dbUpdates = queuedUpdates.get(iid.toString());
			if (dbUpdates == null){
				dbUpdates = new Hashtable<String,IndexUpdateRecord>();
				queuedUpdates.put(iid.toString(), dbUpdates);
			}
			IndexUpdateRecord oldr = dbUpdates.get(record.getKey());
			// combine a previous delete with current add to form update
			if(oldr != null && oldr.doDelete() && record.doAdd())
				record.setAction(IndexUpdateRecord.Action.UPDATE);
			dbUpdates.put(record.getKey(),record);
		}
		
		log.debug("Locally queued item: "+record);
	}
	
	static public int getQueueSize(){
		synchronized(threadLock){
			int count = 0;
			for(String db : queuedUpdates.keySet()){
				count += queuedUpdates.get(db).size();
			}
			return count;
		}
	}
	
	/** 
	 * Fetches queued updates for processing, will clear
	 * the queuedUpdates hashtable
	 * 
	 * @return hashtable of db->key->updates
	 */
	public static Hashtable<String,Hashtable<String,IndexUpdateRecord>> fetchIndexUpdates(){
		synchronized(threadLock){
			if(queuedUpdates.size() == 0)
				return null;
			Hashtable<String,Hashtable<String,IndexUpdateRecord>> updates = queuedUpdates;
			queuedUpdates = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();
			return updates;
		}
	}
	
	/**
	 * Apply all queued updates 
	 */
	private void applyUpdates() {
		log.debug("Applying index updates...");
		try {						
			// check preconditions
			if (suspended) {
				log.debug("Index thread suspended.");
				return;
			}
			long delta = ( System.currentTimeMillis()- lastFlush);
			int queuedCount = getQueueSize();
			if(queuedCount == 0){
				log.debug("0 queued items. Nothing to do.");
				return;
			}
			
			// check if it's flush time
			if (!flushNow &&
				(delta < maxQueueTimeout &&
				queuedCount < maxQueueCount)) {
				log.info(queuedCount+" queued items waiting, "+delta+"ms since last flush...");
				return;
			}

			// fetch for update
			workUpdates = fetchIndexUpdates();
			if(workUpdates == null || workUpdates.size() == 0){				
				flushNow = false;
				lastFlush =  System.currentTimeMillis();		
				log.info("Queue processed by other thread");
				return;
			}			

			// update
			for ( String dbname: workUpdates.keySet() ){
				Hashtable<String,IndexUpdateRecord> dbUpdates = workUpdates.get( dbname );
				update(IndexId.get(dbname), dbUpdates.values());
			}
			flushNow = false;
			lastFlush =  System.currentTimeMillis();
		} catch (Exception e) {
			e.printStackTrace();
			log.error("Unexpected error in Index thread while applying updates: "+e.getMessage());
			return;
		}
	}

	/**
	 * @param dbname
	 * @param updates collection of IndexUpdateRecords
	 */
	private void update(IndexId iid, Collection<IndexUpdateRecord> updates) {
		indexModifier.updateDocuments(iid,updates);
	}
	
	public String getStatus() {
		int count = getQueueSize();
		long delta = (System.currentTimeMillis() - lastFlush);
		return "Updater "+ (suspended ? "IS NOT" : "IS" ) +
		" running; "+count+" item"+(count == 1 ? "" : "s" )+
		" queued. "+delta+"ms since last flush.";			
	}
	
	/**
	 * This function can be called only via other indexer, never end-user
	 * @param record
	 */
	public static void enqueueFromIndexer(IndexUpdateRecord record) {
		synchronized(threadLock){
			enqueueLocally(record);	
		}
	}

	public void stopThread() {
		suspended = true;		
	}

	public void startThread() {
		suspended = false;		
	}
	
	public void makeSnapshotsNow(){
		makeSnapshotNow = true;
	}

	public void flush() {
		log.info("Flush requested");
		flushNow = true;		
	}

	public void quit() {
		log.info("Quiting...");
		quit = true;		
	}
	
	
	
	
}
