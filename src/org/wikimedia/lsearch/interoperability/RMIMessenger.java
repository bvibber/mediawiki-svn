package org.wikimedia.lsearch.interoperability;

import java.io.IOException;
import java.rmi.Remote;
import java.rmi.RemoteException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.Query;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.highlight.Highlight;
import org.wikimedia.lsearch.highlight.HighlightResult;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.search.HighlightPack;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.NamespaceFilterWrapper;
import org.wikimedia.lsearch.search.SuffixFilterWrapper;
import org.wikimedia.lsearch.search.SuffixNamespaceWrapper;
import org.wikimedia.lsearch.search.SearcherCache.SearcherPoolStatus;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.SuggestQuery;
import org.wikimedia.lsearch.spell.SuggestResult;

/** Facilitates communication between both indexers and searcher */
public interface RMIMessenger extends Remote {
	/** 
	 * Call this method from a remote host to notify this
	 * host that the remote index has changed
	 * @param host  host name as in global configuration file
	 * @param dbrole  string representation of correspoing index id
	 * @throws RemoteException
	 */
	public void indexUpdated(String host, String dbrole) throws RemoteException;
	
	/** 
	 * Returns the timestamp of index (the one generated at indexer host)
	 * @param dbroles  string represenations of index id
	 * @return 
	 * @throws RemoteException
	 */
	public long[] getIndexTimestamp(String[] dbroles) throws RemoteException;
	
	/**
	 * Get status of a local searcher pool
	 * 
	 * @param dbrole
	 * @return
	 * @throws RemoteException
	 */
	public SearcherPoolStatus getSearcherPoolStatus(String dbrole) throws RemoteException;
	
	/**
	 * Enqueue on local indexer an index update record. Used for distributed
	 * indexing of split indexes. 
	 * @param record
	 * @throws RemoteException
	 */
	public void enqueueUpdateRecords(IndexUpdateRecord[] records) throws RemoteException;
	
	/**
	 * RMI frontend for enqueue operation on indexer. For incremental updates. 
	 *  
	 * @param record
	 * @throws RemoteException
	 * @return pageids of additional records to fetch
	 */
	public HashSet<String> enqueueFrontend(IndexUpdateRecord[] records) throws RemoteException;
	
	
	/**
	 * For mainsplit indexes, sometimes only a part of index needs to be searched, 
	 * via this method the query is forwarded to mainpart/restpart of the index. 
	 * 
	 * @param dbrole
	 * @param query
	 * @param offset
	 * @param limit
	 * @throws RemoteException
	 */
	public HighlightPack searchPart(String dbrole, String searchterm, Query query, NamespaceFilterWrapper filter, int offset, int limit, boolean explain) throws RemoteException;
	
	/**
	 * Returns index queue size. Needed for incremental updater, so it doesn't overload the indexer. 
	 * 
	 * @return
	 * @throws RemoteException
	 */
	public int getIndexerQueueSize() throws RemoteException;
	
	/** 
	 * Incremental updater tools. 
	 * Request that indexer flushes dbname, and enqueues notification if the
	 * flush was sucessful. Call function isSuccessfulFlush() to find out
	 * flush status.
	 * 
	 * @param dbname
	 * @return
	 * @throws RemoteException
	 */
	public boolean requestFlushAndNotify(String dbname) throws RemoteException;

	/**
	 * Incremental updater tools.
	 * Pair to function requestFlushAndNotify(). Will return null if flush status
	 * is unknown, and true/false if it's successful/failed
	 * 
	 * @param dbname
	 * @return
	 * @throws RemoteException
	 */
	public Boolean isSuccessfulFlush(String dbname) throws RemoteException;
	
	/**
	 * Wildcard matcher,
	 * Request all terms from title and reverse_title that match wildcard pattern   
	 * 
	 * @param dbrole - part of index, e.g. enwiki.nspart1
	 * @param wildcard - wildcard pattern with * and ?
	 * @param exactCase - if pattern is exact capitalization
	 * @return
	 * @throws RemoteException
	 */
	public ArrayList<String> getTerms(String dbrole, String wildcard, boolean exactCase) throws RemoteException;
	
	/**
	 * Highlight articles
	 * 
	 * @param hits - keys (ns:title) to articles to highlight
	 * @param dbrole - iid
	 * @param terms - terms used in query (including aliases)
	 * @param df - document frequencies for all terms
	 * @param maxDoc - max number of documents in the index (needed for idf calculation)
	 * @param words - main phrase words, gives extra score
	 * @param exactCase - if this is an exact case query
	 * @return resultset
	 */
	public Highlight.ResultSet highlight(ArrayList<String> hits, String dbrole, Term[] terms, int df[], int maxDoc, ArrayList<String> words, boolean exactCase, boolean sortByPhrases, boolean alwaysIncludeFirst) throws RemoteException;
	
	/**
	 * Search grouped titles, similar logic to that of searchPart()
	 * 
	 * @param dbrole
	 * @param searchterm
	 * @param query
	 * @param filter
	 * @param offset
	 * @param limit
	 * @return
	 */
	public SearchResults searchTitles(String dbrole, String searchterm, ArrayList<String> words, Query query, SuffixNamespaceWrapper filter, int offset, int limit, boolean explain, boolean searchByPhrases) throws RemoteException;
	
	/**
	 * "Did you mean.." engine
	 * 
	 * @param dbrole
	 * @param searchterm
	 * @param tokens
	 * @param phrases
	 * @param foundInContext
	 * @return
	 * @throws RemoteException
	 */
	public SuggestQuery suggest(String dbrole, String searchterm, ArrayList<Token> tokens, Suggest.ExtraInfo info, NamespaceFilter nsf) throws RemoteException;
	
	/**
	 * Suggest similar titles 
	 * 
	 * @param dbrole
	 * @param title
	 * @param nsf
	 * @return
	 * @throws IOException
	 */
	public ArrayList<String> similar(String dbrole, String title, NamespaceFilter nsf, int maxdist) throws RemoteException;

	/**
	 * Fetch words for fuzzy queries (e.g. query~)
	 * 
	 * @param dbrole
	 * @param word
	 * @param nsf
	 * @return
	 * @throws RemoteException
	 */
	public ArrayList<SuggestResult> getFuzzy(String dbrole, String word, NamespaceFilter nsf) throws RemoteException;
	
	/**
	 * Search a remote related index 
	 *   
	 * @param dbrole
	 * @param searchterm
	 * @param limit
	 * @param offset
	 * @return
	 * @throws RemoteException
	 */
	public SearchResults searchRelated(String dbrole, String searchterm, int offset, int limit) throws RemoteException;
	
	/**
	 * Searcher host asks other hosts if it should deloy the new index. 
	 * If not, waits a random time, then asks again.
	 * 
	 * This is needed to ensure that in a search group new indexes are
	 * deployed and warmud up one at the time (while the host goes out
	 * of rotation - so others can take up the load)
	 * 
	 * @param dbrole
	 * @return
	 * @throws RemoteException
	 */
	public boolean attemptIndexDeployment(String dbrole) throws RemoteException;
	
	/**
	 * Prefix-search wrapper
	 * 
	 * @param dbrole
	 * @param searchterm
	 * @param limit
	 * @return
	 * @throws RemoteException
	 */
	public SearchResults searchPrefix(String dbrole, String searchterm, int limit, NamespaceFilter nsf) throws RemoteException;
	
	/** 
	 * Request some indexes to be snapshoted
	 * 
	 * @param optimize
	 * @param pattern
	 * @param forPrecursor
	 */
	public void requestSnapshotAndNotify(boolean optimize, String pattern, boolean forPrecursor) throws RemoteException;
	
	/** 
	 * Check if snapshot with following params have been finished 
	 * 
	 * @param optimize
	 * @param pattern
	 * @param forPrecursor
	 * @return
	 */
	public boolean snapshotFinished(boolean optimize, String pattern, boolean forPrecursor) throws RemoteException;
}
