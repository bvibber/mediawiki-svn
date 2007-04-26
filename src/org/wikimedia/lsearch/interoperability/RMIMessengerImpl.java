package org.wikimedia.lsearch.interoperability;

import java.rmi.RemoteException;
import java.util.Arrays;

import org.apache.log4j.Logger;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.frontend.IndexDaemon;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.search.NamespaceFilterWrapper;
import org.wikimedia.lsearch.search.NetworkStatusThread;
import org.wikimedia.lsearch.search.SearchEngine;

/** Local implementation for {@link RMIMessenger} */
public class RMIMessengerImpl implements RMIMessenger {
	protected static org.apache.log4j.Logger log = Logger.getLogger(RMIMessengerImpl.class);
	protected NetworkStatusThread networkStatus;
	protected IndexRegistry indexRegistry;
	protected IndexDaemon indexer;
	
	static protected RMIMessengerImpl instance = null;
	
	// inherit javadoc, get latesta snapshot timestamps
	public long[] getIndexTimestamp(String[] dbroles) throws RemoteException {
		log.debug("Received request: getIndexTimestamp("+Arrays.toString(dbroles)+")");
		if(indexRegistry == null)
			indexRegistry = IndexRegistry.getInstance();
		
		long[] timestamps = new long[dbroles.length];
		int i=0;
		for(String dbrole : dbroles){
			LocalIndex li = indexRegistry.getLatestSnapshot(IndexId.get(dbrole));
			if(li != null)
				timestamps[i++] = li.timestamp;
			else
				timestamps[i++] = 0;
		}
		log.debug(" <-/ replying: "+Arrays.toString(timestamps));
		return timestamps;
	}

	// inherit javadoc, notification of index update
	public void indexUpdated(String host, String dbrole) throws RemoteException {
		log.debug("Received request indexUpdated("+host+","+dbrole+")");
		if(networkStatus == null)
			networkStatus = NetworkStatusThread.getInstance();
		networkStatus.indexUpdated(GlobalConfiguration.getIndexId(dbrole), host);
	}
	
	// inherit javadoc
	public void enqueueUpdateRecords(IndexUpdateRecord[] records) throws RemoteException {
		log.debug("Received request enqueueUpdateRecords("+Arrays.toString(records)+")");
		if(indexer == null)
			indexer = new IndexDaemon(); // start the indexer
		for(IndexUpdateRecord record : records)
			IndexThread.enqueueFromIndexer(record);
	}
	
	// inherit javadoc
	public void reportBack(IndexReportCard[] cards) throws RemoteException {
		log.debug("Received request reportBack("+Arrays.toString(cards)+")");
		if(indexer == null)
			indexer = new IndexDaemon(); // start the indexer
		IndexThread.enqueuReports(cards);
	}

	// inherit javadoc
	public SearchResults searchPart(String dbrole, Query query, NamespaceFilterWrapper filter, int offset, int limit) throws RemoteException {
		log.debug("Received request searchMainPart("+dbrole+","+query+","+offset+","+limit+")");
		return new SearchEngine().searchPart(IndexId.get(dbrole),query,filter,offset,limit);
	}

	protected RMIMessengerImpl(){
		networkStatus = null;
		indexRegistry = null;
	}
	
	/** Get singleton instance */
	public static synchronized RMIMessengerImpl getInstance(){
		if(instance == null)
			instance = new RMIMessengerImpl();
		
		return instance;
	}

}
