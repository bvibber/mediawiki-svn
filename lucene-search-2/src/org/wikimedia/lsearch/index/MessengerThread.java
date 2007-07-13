package org.wikimedia.lsearch.index;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Hashtable;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;

/**
 * Thread that enqueues the index updates on remote indexers in a batch.
 * 
 * @author rainman
 *
 */
public class MessengerThread extends Thread {
	static org.apache.log4j.Logger log = Logger.getLogger(MessengerThread.class);
	public final long sleepInterval = 1000;
	
	protected Hashtable<String,ArrayList<IndexUpdateRecord>>queue = new Hashtable<String,ArrayList<IndexUpdateRecord>>();
	protected Object lock = new Object();
	protected static MessengerThread instance = null;
	
	public void enqueueRemotely(String host, IndexUpdateRecord record){
		synchronized (lock) {
			ArrayList<IndexUpdateRecord> recs = queue.get(host);
			if(recs == null){
				recs = new ArrayList<IndexUpdateRecord>();
				queue.put(host,recs);
			}
			recs.add(record);
		}
	}
	
	public void enqueueRemotely(String host, Collection<IndexUpdateRecord> records){
		synchronized (lock) {
			ArrayList<IndexUpdateRecord> recs = queue.get(host);
			if(recs == null){
				recs = new ArrayList<IndexUpdateRecord>();
				queue.put(host,recs);
			}
			recs.addAll(records);
		}
	}
	
	public void sendAll(){
		Hashtable<String,ArrayList<IndexUpdateRecord>> wq;
		synchronized(lock){
			if(queue.size() == 0){
				log.debug("No remote queue.");
				return;
			}
			wq = queue;
			queue = new Hashtable<String,ArrayList<IndexUpdateRecord>>();
		}		
		RMIMessengerClient messenger = new RMIMessengerClient();
		for(Entry<String,ArrayList<IndexUpdateRecord>> hostrec : wq.entrySet()){
			String host = hostrec.getKey();
			IndexUpdateRecord[] val = hostrec.getValue().toArray(new IndexUpdateRecord[]{});
			log.debug("Sending "+val.length+" index update records to host "+host);
			try {
				messenger.enqueueUpdateRecords(val,host);
			} catch (Exception e) {
				// error sending, return to queue
				enqueueRemotely(host,hostrec.getValue());
			}
		}
	}
	
	@Override
	public void run() {
		while(true){
			sendAll();
			try {
				Thread.sleep(sleepInterval);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}

	}

	public static synchronized MessengerThread getInstance(){
		if(instance == null){
			instance = new MessengerThread();
			instance.start();
		}
		
		return instance;
	}
	
	protected MessengerThread(){
	}
}
