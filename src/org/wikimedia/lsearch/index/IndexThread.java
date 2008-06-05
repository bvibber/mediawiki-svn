/*
 * Created on Feb 11, 2007
 *
 */
package org.wikimedia.lsearch.index;

import java.io.File;
import java.io.IOException;
import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.List;
import java.util.Set;
import java.util.Map.Entry;
import java.util.concurrent.locks.Lock;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.beans.ReportId;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexUpdateRecord.Action;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.util.Command;
import org.wikimedia.lsearch.util.FSUtils;
import org.wikimedia.lsearch.util.ProgressReport;
import org.wikimedia.lsearch.util.StringUtils;

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
	
	/** time of last updates flush */
	protected long lastFlush;
	protected WikiIndexModifier indexModifier;	
	protected static GlobalConfiguration global;
	/** this lock is used when threads access static members */
	protected static Object staticLock = new Object();
	/** 
	 * This is where pages are queued for processing. 
	 * dbrole -> hashtable(ns:title -> indexUpdateRecord) 
	 */
	protected static Hashtable<String,Hashtable<String,IndexUpdateRecord>> queuedUpdates = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();	
	/** 
	 * Updates to links index and various precursors
	 * dbrole -> ns:title -> update record 
	 */
	protected static Hashtable<String,Hashtable<String,IndexUpdateRecord>> linksUpdates = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();
	
	/** set of dbs to be flushed */
	protected static Set<String> needFlushDBs = Collections.synchronizedSet(new HashSet<String>());
	/** dbs that have been flushed in last flush cycle, dbrole -> flushed_succ */
	protected static Hashtable<String,Boolean> flushedDBs = new Hashtable<String,Boolean>(); 
	protected Set<String> workFlushes;

	/** Thread that enqueues updates to distributed indexers in a batch */
	protected static MessengerThread messenger = null;
	
	protected ArrayList<Pattern> snapshotPatterns = new ArrayList<Pattern>();
	
	static class Pattern {
		String pattern; // pattern to be mached
		boolean forPrecursors; // match precursors only
		boolean not = false; // *not* matching this pattern
		boolean optimize = true;
		
		public Pattern(boolean optimize, String pattern, boolean forPrecursors){
			this(pattern,forPrecursors,false);
			this.optimize = optimize;
		}
		public Pattern(String pattern, boolean forPrecursors, boolean not){
			this.pattern = pattern;
			this.forPrecursors = forPrecursors;
			this.not = not;
		}
	}
	
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
			applyUpdates();
			// make snapshot?
			if(makeSnapshotNow || (System.currentTimeMillis() - lastSnapshot) > snapshotInterval){
				flushNow = true;
				applyUpdates();				
				makeSnapshots();
				lastSnapshot = System.currentTimeMillis();				
			}
			try {
				Thread.sleep(1000);
			} catch (InterruptedException e) {
				log.warn("IndexThread sleep interrupted with message: "+e.getMessage());
			}
		}
		if(queuedUpdatesExist())
			applyUpdates();
		WikiIndexModifier.getModifiedIndexes();
	}
	
	/**
	 * Make snapshots of all changed indexes
	 *
	 */
	protected void makeSnapshots() {
		ArrayList<IndexId> indexes = new ArrayList<IndexId>();
		IndexRegistry registry = IndexRegistry.getInstance();
		
		ArrayList<Pattern> pat = new ArrayList<Pattern>();
		synchronized (snapshotPatterns) {
			for(Pattern p : snapshotPatterns){ // convert wildcards into regexp				 
				pat.add(new Pattern(StringUtils.wildcardToRegexp(p.pattern),p.forPrecursors,p.pattern.startsWith("^")));
			}
			snapshotPatterns.clear();
			makeSnapshotNow = false;
		}
		log.info("Making snapshots...");
		
		// check if other indexes exist, if so, make snapshots
		for( IndexId iid : global.getMyIndex()){
			if(iid.isLogical() || indexes.contains(iid))
				continue;
			File indexdir = new File(iid.getIndexPath());
			if(indexdir.exists())
				indexes.add(iid);
		}
		// nicely alphabetically sort
		Collections.sort(indexes, new Comparator<IndexId>() {
			public int compare(IndexId o1, IndexId o2) {
				return o1.toString().compareTo(o2.toString());
			}
		});
		HashSet<IndexId> badOptimization = new HashSet<IndexId>();
		// optimize all
		for( IndexId iid : indexes ){
			Lock lock = null;
			try{
				if(iid.isLogical())
					continue;
				if(matchesPattern(pat,iid)){
					// enforce outer transaction lock to connect optimization & snapshot
					lock = iid.getTransactionLock(IndexId.Transaction.INDEX);
					lock.lock();
					optimizeIndex(iid);
					makeIndexSnapshot(iid,iid.getIndexPath());
					lock.unlock();
					lock = null;
				}
			} catch(IOException e){
				e.printStackTrace();
				log.error("Error optimizing index "+iid);
				badOptimization.add(iid);
			} finally {
				if(lock != null)
					lock.unlock();
			}
		}
		// snapshot all
		for( IndexId iid : indexes ){
			if(iid.isLogical() || badOptimization.contains(iid))
				continue;
			if(matchesPattern(pat,iid)){

				registry.refreshSnapshots(iid);				
			}
		}
	}
	
	private boolean matchesPattern(ArrayList<Pattern> pat, IndexId iid) {
		String string = iid.toString();
		for(Pattern p : pat){
			if((iid.isPrecursor() && !p.forPrecursors) ||(!iid.isPrecursor() && p.forPrecursors))
				continue;
			boolean match = p.pattern.equals("")? true : string.matches(p.pattern); 
			if((match && !p.not) || (!match && p.not))
				return true;
		}
		return false;
	}

	public static void makeIndexSnapshot(IndexId iid, String indexPath){
		final String sep = Configuration.PATH_SEP;
		DateFormat df = new SimpleDateFormat("yyyyMMddHHmmss");
		String timestamp = df.format(new Date(System.currentTimeMillis()));
		if(iid.isLogical())
			return;
		boolean delSnapshots = Configuration.open().getBoolean("Index","delsnapshots") && !iid.isRelated();
		log.info("Making snapshot for "+iid);
		String snapshotdir = iid.getSnapshotPath();
		String snapshot = snapshotdir+sep+timestamp;
		LocalIndex li = IndexRegistry.getInstance().getLatestSnapshot(iid);
		// cleanup the snapshot dir for this iid
		File spd = new File(snapshotdir);
		if(spd.exists() && spd.isDirectory()){
			File[] files = spd.listFiles();
			for(File f: files){
				if(f.getAbsolutePath().equals(li.path) && !delSnapshots)
					continue; // leave last snapshot
				FSUtils.deleteRecursive(f);
			}
		}
		new File(snapshot).mkdirs();
		File ind =new File(indexPath);
		for(File f: ind.listFiles()){
			// use a cp -lr command for each file in the index
			try {
				FSUtils.createHardLinkRecursive(indexPath+sep+f.getName(),snapshot+sep+f.getName(),true);
			} catch (IOException e) {
				e.printStackTrace();
				log.error("Error making snapshot "+snapshot+": "+e.getMessage());
				return;
			}
		}
		IndexRegistry.getInstance().refreshSnapshots(iid);
		log.info("Made snapshot "+snapshot);		
	}
	
	/** Optimizes index if needed 
	 * @throws IOException 
	 * @throws IOException */
	protected static void optimizeIndex(IndexId iid) throws IOException{
		optimizeIndex(iid,iid.getIndexPath(),IndexId.Transaction.INDEX);
	}
	public static void optimizeIndex(IndexId iid, String path, IndexId.Transaction transType) throws IOException{
		if(iid.isLogical()) 
			return;
		if(iid.getBooleanParam("optimize",true)){
			try {
				Transaction trans = new Transaction(iid,transType);
				trans.begin();
				IndexReader reader = IndexReader.open(path);
				if(!reader.isOptimized()){
					reader.close();
					log.info("Optimizing "+iid);
					long start = System.currentTimeMillis();
					IndexWriter writer = new IndexWriter(path,new SimpleAnalyzer(),false);
					writer.optimize();
					writer.close();					
					long delta = System.currentTimeMillis() - start;
					log.info("Optimized "+iid+" in "+ProgressReport.formatTime(delta));
				} else
					reader.close();
				trans.commit();
			} catch (IOException e) {
				log.error("Could not optimize index at "+path+" : "+e.getMessage());
				throw e;
			}
		}
	}
	
	/**
	 * @return if there are queued updates
	 */
	public static boolean queuedUpdatesExist(){
		synchronized(staticLock){
			return queuedUpdates.size() > 0;
		}
	}
	
	/** Enqueue a number of records, and get pageids of records that additionaly needs to be sent */
	public static HashSet<String> enqueue(IndexUpdateRecord[] records) throws Exception {
		HashSet<String> add = new HashSet<String>();
		if(records.length > 0){
			IndexId iid = records[0].getIndexId(); // we asume all are on same iid
			// get exclusive lock to make sure nothing funny is going on with the index
			Lock lock = iid.getLinks().getTransactionLock(IndexId.Transaction.INDEX);
			lock.lock();
			try{
				// FIXME: there should be some kind of failed previous transaction check here
				// works for now because we first do updates, but could easily break in future
				Links links = Links.openForBatchModifiation(iid);
				// update links
				links.batchUpdate(records);
				WikiIndexModifier.fetchLinksInfo(iid,records,links);
				// get additional
				add.addAll(WikiIndexModifier.fetchAdditional(iid,records,links));			
				links.close();

				for(IndexUpdateRecord r : records){
					enqueue(r);
				}	
			} finally{
				lock.unlock();
			}
		}

		return add;
	}
	
	/**
	 * Enqueue a single update
	 * @param record
	 */
	protected static void enqueue(IndexUpdateRecord record) throws Exception {
		synchronized(staticLock){
			IndexId iid = record.getIndexId();
			if(iid == null || !(iid.isLogical() || iid.isSingle()) || !iid.isMyIndex()){
				log.error("Got update for database "+iid+", however this node does not accept updates for this DB");
				return;
			}
			
			// always do link update irregardless of index architecture 
			enqueueLink(record);

			if( iid.isSingle() ){
				enqueueLocally(record);
			} else if( iid.isMainsplit() || iid.isNssplit()){
				IndexId piid;
				Article ar = record.getArticle();
				// always delete everywhere since we might not have namespace info
				if(record.doDelete()){
					for(String dbrole : iid.getSplitParts()){
						IndexUpdateRecord recp = (IndexUpdateRecord) record.clone();
						recp.setIndexId(IndexId.get(dbrole));
						recp.setAction(Action.DELETE);
						enqueueRemotely(recp);
					}										
				} 
				if(record.doAdd()){
					piid = iid.getPartByNamespace(ar.getNamespace());					
					// set recipient to new host
					record.setIndexId(piid);
					record.setAction(Action.ADD);
					enqueueRemotely(record);
				}
			} else if( iid.isSplit() ){
				throw new RuntimeException("FIXME: Indexing for split indexes is broken, use nssplit architecture instead");
			}
			return;
		}
	}

	/** Make a link update for this record and enqueue it */
	protected static void enqueueLink(IndexUpdateRecord record){
		IndexId iid = record.getIndexId();
		IndexUpdateRecord recl = (IndexUpdateRecord) record.clone();
		recl.setIndexId(iid.getDB());
		recl.setLinkUpdate(true);
		enqueueRemotely(recl);
	}
	/**
	 * Put update record on remote queue
	 * @param host
	 * @param record
	 */
	protected static void enqueueRemotely(IndexUpdateRecord record) {
		IndexId iid = record.getIndexId();
		String host = iid.getIndexHost();
		if(iid.isMyIndex() || RMIMessengerClient.isLocal(host)){
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
		synchronized (staticLock){
			IndexId iid = record.getIndexId();
			// get relavant mapping
			Hashtable<String,Hashtable<String,IndexUpdateRecord>> dest = 
				(record.isLinkUpdate())? linksUpdates : queuedUpdates;
			
			Hashtable<String,IndexUpdateRecord> dbUpdates = dest.get(iid.toString());
			if (dbUpdates == null){
				dbUpdates = new Hashtable<String,IndexUpdateRecord>();
				dest.put(iid.toString(), dbUpdates);
			}
			IndexUpdateRecord oldr = dbUpdates.get(record.getIndexKey());
			// combine a previous delete with current add to form update
			if(oldr != null && oldr.doDelete() && record.doAdd())
				record.setAction(IndexUpdateRecord.Action.UPDATE);
			dbUpdates.put(record.getIndexKey(),record);
		}
		
		log.debug("Locally queued item: "+record);
	}
	
	static public int getQueueSize(){
		synchronized(staticLock){
			int count = 0;
			for(String db : queuedUpdates.keySet()){
				count += queuedUpdates.get(db).size();
			}
			return count;
		}
	}
	
	protected static class WorkSet {
		Hashtable<String,Hashtable<String,IndexUpdateRecord>> index;
		Hashtable<String,Hashtable<String,IndexUpdateRecord>> link;
		public WorkSet(Hashtable<String, Hashtable<String, IndexUpdateRecord>> indexUpdates, Hashtable<String, Hashtable<String, IndexUpdateRecord>> linkUpdates) {
			this.index = indexUpdates;
			this.link = linkUpdates;
		}		
	}
	
	/** 
	 * Fetches queued updates for processing, will clear
	 * the queuedUpdates hashtable. Use filter to fetch 
	 * only certain dbs.
	 * 
	 * @param filter - set of dbnames
	 * @return hashtable of db->key->updates
	 */
	public static WorkSet fetchIndexUpdates(Set<String> filter){
		synchronized(staticLock){
			if(queuedUpdates.size() == 0)
				return null;
			Hashtable<String,Hashtable<String,IndexUpdateRecord>> updates;
			Hashtable<String,Hashtable<String,IndexUpdateRecord>> links;
			// filter out only certain dbs 
			if(filter != null){
				// index updates
				updates = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();
				HashSet<String> dbroles = new HashSet<String>();
				dbroles.addAll(queuedUpdates.keySet());
				for(String dbrole : dbroles){
					IndexId iid = IndexId.get(dbrole);
					if(filter.contains(iid.getDBname()) || filter.contains(iid.toString())){
						updates.put(dbrole,queuedUpdates.remove(dbrole));
					}
				}	
				// link updates
				links = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();
				HashSet<String> linkDBs = new HashSet<String>();
				linkDBs.addAll(linksUpdates.keySet());
				for(String dbname : linkDBs){
					IndexId iid = IndexId.get(dbname);
					if(filter.contains(iid.getDBname())){
						links.put(dbname,linksUpdates.remove(dbname));
					}
				}
				return new WorkSet( updates, links );
			} else{
				updates = queuedUpdates;
				links = linksUpdates;
				queuedUpdates = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();
				linksUpdates = new Hashtable<String,Hashtable<String,IndexUpdateRecord>>();
				return new WorkSet( updates, links ) ;
			}
		}
	}
	
	/**
	 * Apply all queued updates 
	 */
	private void applyUpdates() {
		log.debug("Applying index updates...");
		WorkSet updates;
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
				// make all dbs flushed
				synchronized(staticLock){
					for(String dbname : needFlushDBs){
						flushedDBs.put(dbname,true);
					}
					needFlushDBs.clear();
				}
				return;
			}
			
			// check if it's flush time
			if (!(flushNow || needFlushDBs.size()!=0) &&
				(delta < maxQueueTimeout &&
				queuedCount < maxQueueCount)) {
				log.info(queuedCount+" queued items waiting, "+delta+"ms since last flush...");
				return;
			}

			// if flush&notify is requestd, get the flush requests
			workFlushes = null;
			synchronized(staticLock){
				if(needFlushDBs.size() != 0){
					workFlushes = needFlushDBs;
					needFlushDBs = Collections.synchronizedSet(new HashSet<String>());
				}
			}
			// fetch for update
			updates = fetchIndexUpdates(workFlushes);
			if(updates.index == null || updates.index.size() == 0){				
				flushNow = false;
				lastFlush =  System.currentTimeMillis();		
				log.info("Queue processed by other thread");
				return;
			}			

			// update
			for ( String dbrole : updates.index.keySet() ){
				IndexId iid = IndexId.get(dbrole);
				IndexId db = iid.getDB();
				boolean succ = true;
				if(updates.link.containsKey(db.toString())) // always update links first
					succ = updateLinks( iid.getDB(), updates.link.remove(db.toString()).values() );
				if(succ)
					update( iid, updates.index.get(dbrole).values() );
			}
			if(workFlushes != null){
				// figure out from index parts if the update was successful
				synchronized(staticLock){	
					for(String dbname : workFlushes){
						IndexId iid = IndexId.get(dbname);
						boolean succ = true;
						Boolean val;
						for(String r : iid.getPhysicalIndexes()){
							if((val = flushedDBs.remove(r)) != null)
								if(val == false)
									succ = false;
						}
						flushedDBs.put(dbname,succ);							
					}
				}			
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
		boolean succ = indexModifier.updateDocuments(iid,updates);
		if(workFlushes != null){
			synchronized(staticLock){
				flushedDBs.put(iid.toString(),succ);
			}
		}
	}
	
	/**
	 * Update links and precursor indexes, always call before update()
	 * @return success
	 */
	private boolean updateLinks(IndexId iid, Collection<IndexUpdateRecord> updates) {
		return indexModifier.updateLinksAndPrecursors(iid,updates);
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
		synchronized(staticLock){
			enqueueLocally(record);	
		}
	}

	public void stopThread() {
		suspended = true;		
	}

	public void startThread() {
		suspended = false;		
	}
	
	public void makeSnapshotsNow(boolean optimize, String pattern, boolean forPrecursors){
		synchronized(snapshotPatterns){
			snapshotPatterns.add(new Pattern(optimize,pattern,forPrecursors));
			makeSnapshotNow = true;
		}
	}

	public void flush() {
		log.info("Flush requested");
		flushNow = true;		
	}

	public void quit() {
		log.info("Quiting...");
		quit = true;		
	}
	
	/**
	 * Flush updates for given dbname (all parts), and put the dbname
	 * into notification table. Returns true if request is valid.  
	 * 
	 * @param dbname
	 */
	public static boolean flushAndNotify(String dbname) {
		log.info("Flush and notify requested on "+dbname);
		IndexId iid = IndexId.get(dbname);
		HashSet<IndexId> myindex = global.getMyIndex();
		for(String r : iid.getPhysicalIndexes()){
			if( ! myindex.contains(IndexId.get(r)) ){
				log.warn("Invalid flush/notify request. Can be made only for dbs that are entirely indexes by this host");
				return false;
			}
		}
		synchronized(staticLock){
			needFlushDBs.add(dbname);
		}
		return true;
	}
	
	/** 
	 * Checks if db is flushed after notification has been queued
	 * (via flushAndNotify()).
	 * Returns null if flushed state is unknown, true/false if
	 * it's successfull/failed. Flag is cleared after bool value
	 * has been returned. 
	 * 
	 * @param dbname
	 */
	public static Boolean isFlushedDB(String dbname){
		synchronized(staticLock){
			return flushedDBs.remove(dbname);
		}
	}
	
}
