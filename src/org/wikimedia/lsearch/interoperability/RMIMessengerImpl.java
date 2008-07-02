package org.wikimedia.lsearch.interoperability;

import java.io.IOException;
import java.rmi.RemoteException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.frontend.IndexDaemon;
import org.wikimedia.lsearch.highlight.Highlight;
import org.wikimedia.lsearch.highlight.HighlightResult;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.search.HighlightPack;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.NamespaceFilterWrapper;
import org.wikimedia.lsearch.search.NetworkStatusThread;
import org.wikimedia.lsearch.search.SearchEngine;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.search.SuffixFilterWrapper;
import org.wikimedia.lsearch.search.SuffixNamespaceWrapper;
import org.wikimedia.lsearch.search.Wildcards;
import org.wikimedia.lsearch.search.SearcherCache.SearcherPoolStatus;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.SuggestQuery;
import org.wikimedia.lsearch.spell.SuggestResult;
import org.wikimedia.lsearch.spell.SuggestSimilar;

/** Local implementation for {@link RMIMessenger} */
public class RMIMessengerImpl implements RMIMessenger {
	protected static org.apache.log4j.Logger log = Logger.getLogger(RMIMessengerImpl.class);
	protected NetworkStatusThread networkStatus = null;
	protected IndexRegistry indexRegistry = null;
	protected IndexDaemon indexer = null;
	protected SearcherCache cache = null;
	protected IndexThread indexThread = null;
	
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
		log.debug("Received request enqueueUpdateRecords("+records.length+" records)");
		if(indexer == null)
			indexer = new IndexDaemon(); // start the indexer
		for(IndexUpdateRecord record : records)
			IndexThread.enqueueFromIndexer(record);
	}
	
	// inherit javadoc
	public HashSet<String> enqueueFrontend(IndexUpdateRecord[] records) throws RemoteException {
		log.debug("Received request enqueueUpdateRecords("+records.length+" records)");
		if(indexer == null)
			indexer = new IndexDaemon(); // start the indexer
		try {
			return IndexThread.enqueue(records);
		} catch (Exception e) {
			e.printStackTrace();
			throw new RemoteException("Exception during queue()",e);
		}		
	}

	// inherit javadoc
	public HighlightPack searchPart(String dbrole, String searchterm, Query query, NamespaceFilterWrapper filter, int offset, int limit, boolean explain) throws RemoteException {
		log.debug("Received request searchMainPart("+dbrole+","+query+","+offset+","+limit+")");
		return new SearchEngine().searchPart(IndexId.get(dbrole),searchterm,query,filter,offset,limit,explain);
	}
	
	public ArrayList<String> getTerms(String dbrole, String wildcard, boolean exactCase) throws RemoteException {
		try{
			return Wildcards.getLocalTerms(IndexId.get(dbrole),wildcard,exactCase);
		} catch(IOException e){
			throw new RemoteException("IOException on "+dbrole,e);
		}
	}
	
	// inherit javadoc
	public int getIndexerQueueSize() throws RemoteException {
		return IndexThread.getQueueSize();
	}
	
	// inherit javadoc
	public Boolean isSuccessfulFlush(String dbname) throws RemoteException {
		if(indexer == null)
			indexer = new IndexDaemon(); // start the indexer
		return IndexThread.isFlushedDB(dbname);
	}

	// inherit javadoc
	public boolean requestFlushAndNotify(String dbname) throws RemoteException {
		if(indexer == null)
			indexer = new IndexDaemon(); // start the indexer
		return IndexThread.flushAndNotify(dbname);
	}

	// inherit javadoc
	public Highlight.ResultSet highlight(ArrayList<String> hits, String dbrole, Term[] terms, int[] df, int maxDoc, ArrayList<String> words, boolean exactCase, boolean sortByPhrases, boolean alwaysIncludeFirst) throws RemoteException{
		IndexId iid = IndexId.get(dbrole);
		try{
			return Highlight.highlight(hits,iid,terms,df,maxDoc,words,StopWords.getPredefinedSet(iid),exactCase,null,sortByPhrases,alwaysIncludeFirst);
		} catch(IOException e){
			throw new RemoteException("IOException on "+dbrole,e);
		}
	}
	
	public SearchResults searchTitles(String dbrole, String searchterm, ArrayList<String> words, Query query, SuffixNamespaceWrapper filter, int offset, int limit, boolean explain, boolean sortByPhrases) throws RemoteException {
		IndexId iid = IndexId.get(dbrole);
		try{
			return new SearchEngine().searchTitles(iid,searchterm,words,query,filter,offset,limit,explain,sortByPhrases); 
		} catch(Exception e){
			e.printStackTrace();
			throw new RemoteException("Exception on "+dbrole,e);
		}
	}
	
	public SuggestQuery suggest(String dbrole, String searchterm, ArrayList<Token> tokens, Suggest.ExtraInfo info, NamespaceFilter nsf) throws RemoteException {
		IndexId iid = IndexId.get(dbrole);
		try{
			return new Suggest(iid).suggest(searchterm,tokens,info,nsf);
		} catch(Exception e){
			e.printStackTrace();
			throw new RemoteException("Exception on "+dbrole,e);
		}
	}
	
	public ArrayList<SuggestResult> getFuzzy(String dbrole, String word, NamespaceFilter nsf) throws RemoteException {
		IndexId iid = IndexId.get(dbrole);
		try {
			return new Suggest(iid).getFuzzy(word,nsf);
		} catch (IOException e) {
			e.printStackTrace();
			throw new RemoteException("Exception on "+dbrole,e);
		}
	}

	public SearchResults searchRelated(String dbrole, String searchterm, int offset, int limit) throws RemoteException {
		IndexId iid = IndexId.get(dbrole);
		try{
			return new SearchEngine().searchRelatedLocal(iid,searchterm,offset,limit);
		} catch(IOException e){
			e.printStackTrace();
			throw new RemoteException("Exception on "+dbrole,e);
		}
	}
	
	public boolean attemptIndexDeployment(String dbrole) throws RemoteException {
		if(cache == null)
			cache = SearcherCache.getInstance();
		
		return cache.hasLocalSearcher(IndexId.get(dbrole));
	}
	
	public SearchResults searchPrefix(String dbrole, String searchterm, int limit, NamespaceFilter nsf) throws RemoteException {
		if(cache == null)
			cache = SearcherCache.getInstance();
		
		IndexId iid = IndexId.get(dbrole);
		try {
			return new SearchEngine().searchPrefixLocal(iid,searchterm,limit,nsf,cache.getLocalSearcher(iid));
		} catch (IOException e) {
			throw new RemoteException("IO Error in searchPrefix()",e);
		}
	}
	
	public ArrayList<String> similar(String dbrole, String title, NamespaceFilter nsf, int maxdist) throws RemoteException {
		IndexId iid = IndexId.get(dbrole);
		try{
			SuggestSimilar similar = new SuggestSimilar(iid);
			return similar.getSimilarTitles(title,nsf,maxdist);
		} catch(IOException e){
			throw new RemoteException("IO Error in similar()",e);
		}
	}

	public SearcherPoolStatus getSearcherPoolStatus(String dbrole) throws RemoteException {
		if(cache == null)
			cache = SearcherCache.getInstance();
		try{
			return cache.getSearcherPoolStatus(IndexId.get(dbrole));
		} catch(IOException e){
			throw new RemoteException("IO error in getSearcherPoolStatus()",e);
		}
	}
	
	public void requestSnapshotAndNotify(boolean optimize, String pattern, boolean forPrecursor) {
		if(indexThread == null)
			indexThread = IndexThread.getInstance();
		indexThread.makeSnapshotsNow(optimize,pattern,forPrecursor);
	}
	
	public boolean snapshotFinished(boolean optimize, String pattern, boolean forPrecursor) {
		if(indexThread == null)
			indexThread = IndexThread.getInstance();
		return indexThread.snapshotFinished(optimize,pattern,forPrecursor);
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
