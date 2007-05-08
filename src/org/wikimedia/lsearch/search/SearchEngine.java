package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.net.URI;
import java.text.MessageFormat;
import java.util.HashMap;
import java.util.HashSet;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.TopDocs;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.frontend.SearchDaemon;
import org.wikimedia.lsearch.frontend.SearchServer;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.util.QueryStringMap;

/**
 * Search engine implementation. The implementation is independent of frontend used to
 * communicate with client. A generic container of search results is returned. 
 * 
 * @author rainman
 *
 */
public class SearchEngine {
	static org.apache.log4j.Logger log = Logger.getLogger(SearchEngine.class);

	protected final int maxlines = 1000;
	protected final int maxoffset = 10000;
	
	/** Main search method, call this from the search frontend */
	public SearchResults search(IndexId iid, String what, String searchterm, HashMap query) {
		
		if (what.equals("titlematch")) {
			// TODO: return searchTitles(searchterm);
		} else if (what.equals("search")) {
			int offset = 0, limit = 100;
			if (query.containsKey("offset"))
				offset = Math.max(Integer.parseInt((String)query.get("offset")), 0);
			if (query.containsKey("limit"))
				limit = Math.min(Integer.parseInt((String)query.get("limit")), maxlines);
			NamespaceFilter namespaces = new NamespaceFilter((String)query.get("namespaces"));
			SearchResults res = search(iid, searchterm, offset, limit, namespaces);
			if(res.isRetry()){
				int retries = 0;
				if(iid.isSplit()){
					retries = iid.getSplitFactor()-2;
				} else if(iid.isMainsplit())
					retries = 1;
				
				while(retries > 0 && res.isRetry()){
					res = search(iid, searchterm, offset, limit, namespaces);
					retries--;
				}
				if(res.isRetry())
					res.setErrorMsg("Internal error, too many internal retries.");
			}			
			return res;
		} else if (what.equals("raw")) {
			//TODO: return searchRaw(searchterm);
		} else {
			SearchResults res = new SearchResults();
			res.setErrorMsg("Unrecognized search type. Try one of: " +
			              "titlematch, titleprefix, search, quit, raw.");
			log.warn("Unknown request type [" + what + "].");
			return res;
		}
		return null;
	}
	
	/** Search mainpart or restpart of the split index */
	public SearchResults searchPart(IndexId iid, Query q, NamespaceFilterWrapper filter, int offset, int limit){
		if( ! iid.isMainsplit())
			return null;
		try {
			SearcherCache cache = SearcherCache.getInstance();
			IndexSearcherMul searcher;
			long searchStart = System.currentTimeMillis();

			searcher = cache.getLocalSearcher(iid);

			Hits hits = searcher.search(q,filter);
			return makeSearchResults(searcher,hits,offset,limit,iid,q.toString(),q,searchStart);		
		} catch (IOException e) {
			SearchResults res = new SearchResults();
			res.setErrorMsg("Internal error in SearchEngine: "+e.getMessage());
			log.error("Internal error in SearchEngine while trying to search main part: "+e.getMessage());
			return res;
		}
		
	}
	
	/**
	 * Search on iid, with query searchterm. View results from offset to offset+limit, using
	 * the default namespaces filter
	 */
	public SearchResults search(IndexId iid, String searchterm, int offset, int limit, NamespaceFilter nsDefault){
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid);
		if(nsDefault == null || nsDefault.cardinality() == 0)
			nsDefault = new NamespaceFilter("0"); // default to main namespace
		WikiQueryParser parser = new WikiQueryParser("contents",nsDefault,analyzer,WikiQueryParser.NamespacePolicy.IGNORE);
		HashSet<Integer> fields = parser.getFieldNamespaces(searchterm);
		NamespaceFilterWrapper nsfw = null;
		Query q = null;
		SearchResults res = null;
		long searchStart = System.currentTimeMillis();
		
		if(fields.size()==1){
			if(fields.contains(new Integer(Integer.MAX_VALUE)))
				nsfw = null;  // "all" keyword
			else // use filter if search on one namespace
				nsfw = new NamespaceFilterWrapper(new NamespaceFilter(fields));
		}
		else if(fields.size()==0 && nsDefault!=null && nsDefault.cardinality()==1)
			nsfw = new NamespaceFilterWrapper(nsDefault);
		
		try {
			if(nsfw == null){
				q = parser.parseTwoPass(searchterm,WikiQueryParser.NamespacePolicy.REWRITE);				
			}
			else{
				q = parser.parseTwoPass(searchterm,WikiQueryParser.NamespacePolicy.IGNORE);
				log.debug("Using NamespaceFilterWrapper "+nsfw);
			}
			
			WikiSearcher searcher = new WikiSearcher(iid);
			TopDocs hits=null;
			// mainpart special case
			if(nsfw!=null && iid.isMainsplit() && nsfw.getFilter().cardinality()==1 && nsfw.getFilter().contains(0)){
				String host = searcher.getMainPartHost();
				if(host == null){
					res = new SearchResults();
					res.setErrorMsg("Error contacting searcher for mainpart of the index.");
					log.error("Error contacting searcher for mainpart of the index.");
					return res;
				}
				RMIMessengerClient messenger = new RMIMessengerClient();
				return messenger.searchPart(iid.getMainPart(),q,null,offset,limit,host);
			// restpart special case
			} else if(nsfw!=null && iid.isMainsplit() && !nsfw.getFilter().contains(0)){
				String host = searcher.getRestPartHost();
				if(host == null){
					res = new SearchResults();
					res.setErrorMsg("Error contacting searcher for restpart of the index.");
					log.error("Error contacting searcher for restpart of the index.");
					return res;
				}
				RMIMessengerClient messenger = new RMIMessengerClient();
				return messenger.searchPart(iid.getRestPart(),q,nsfw,offset,limit,host);
			} else{ // normal search
				try{
					hits = searcher.search(q,nsfw,offset+limit);
					res = makeSearchResults(searcher,hits,offset,limit,iid,searchterm,q,searchStart);
					return res;
				} catch(Exception e){
					e.printStackTrace();
					res = new SearchResults();
					res.retry();
					log.warn("Retry, temportal error for query: ["+q+"] on "+iid);
					return res;
				}
			}			
		} catch(ParseException e){
			res = new SearchResults();
			res.setErrorMsg("Error parsing query: "+searchterm);
			log.error("Cannot parse query: "+searchterm+", error: "+e.getMessage());
			return res;
		} catch (Exception e) {
			res = new SearchResults();
			e.printStackTrace();
			res.setErrorMsg("Internal error in SearchEngine: "+e.getMessage());
			log.error("Internal error in SearchEngine trying to make WikiSearcher: "+e.getMessage());
			return res;
		}
	}

	protected SearchResults makeSearchResults(SearchableMul s, TopDocs hits, int offset, int limit, IndexId iid, String searchterm, Query q, long searchStart) throws IOException{
		SearchResults res = new SearchResults();
		int numhits = hits.totalHits;
		res.setSuccess(true);			
		res.setNumHits(numhits);
		logRequest(iid,"search",searchterm, q, numhits, searchStart, s);
		
		int size = min(limit+offset,maxoffset,numhits) - offset;
		int[] docids = new int[size]; 
		float[] scores = new float[size];
		// fetch documents
		for(int i=offset, j=0 ; i<limit+offset && i<maxoffset && i<numhits; i++, j++){
			docids[j] = hits.scoreDocs[i].doc;
			scores[j] = hits.scoreDocs[i].score;
		}
		// fetch documents
		Document[] docs = s.docs(docids);
		int j=0;
		float maxScore = hits.getMaxScore();
		for(Document doc : docs){
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			float score = scores[j++]/maxScore; 
			res.addResult(new ResultSet(score,namespace,title));
		}
		
		return res;
	}
	
	/** Make search results from Hits */
	protected SearchResults makeSearchResults(SearchableMul s, Hits hits, int offset, int limit, IndexId iid, String searchterm, Query q, long searchStart) throws IOException{
		SearchResults res = new SearchResults();
		int numhits = hits.length();
		res.setSuccess(true);			
		res.setNumHits(numhits);
		logRequest(iid,"search",searchterm, q, numhits, searchStart, s);
		
		int size = min(limit+offset,maxoffset,numhits) - offset;
		int[] docids = new int[size];
		float[] scores = new float[size];
		// fetch documents
		for(int i=offset, j=0 ; i<limit+offset && i<maxoffset && i<numhits; i++, j++){
			docids[j] = hits.id(i);
			scores[j] = hits.score(i);
		}
		// fetch documents
		Document[] docs = s.docs(docids);
		int j=0;
		for(Document doc : docs){
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			float score = scores[j++]; 
			res.addResult(new ResultSet(score,namespace,title));
		}
		
		return res;

	}
	
	protected int min(int i1, int i2, int i3){
		return Math.min(Math.min(i1,i2),i3);
	}
	
	protected void logRequest(IndexId iid, String what, String searchterm, Query query, int numhits, long start, Searchable searcher) {
		long delta = System.currentTimeMillis() - start;
		SearchServer.stats.add(true, delta, SearchDaemon.getOpenCount());
		log.info(MessageFormat.format("{0} {1}: query=[{2}] parsed=[{3}] hit=[{4}] in {5}ms using {6}",
			new Object[] {what, iid.toString(), searchterm, query.toString(), new Integer(numhits), new Long(delta), searcher.toString()}));
	}
}
