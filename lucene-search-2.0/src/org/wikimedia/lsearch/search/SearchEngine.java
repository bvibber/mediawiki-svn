package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.net.URI;
import java.text.MessageFormat;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.TopDocs;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.GlobalConfiguration;
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
	protected static GlobalConfiguration global = null;
	
	public SearchEngine(){
		if(global == null)
			global = GlobalConfiguration.getInstance();
	}
	
	/** Main search method, call this from the search frontend */
	public SearchResults search(IndexId iid, String what, String searchterm, HashMap query) {
		
		if (what.equals("titlematch")) {
			// TODO: return searchTitles(searchterm);
		} else if (what.equals("search") || what.equals("explain")) {
			int offset = 0, limit = 100; boolean exactCase = false;
			if (query.containsKey("offset"))
				offset = Math.max(Integer.parseInt((String)query.get("offset")), 0);
			if (query.containsKey("limit"))
				limit = Math.min(Integer.parseInt((String)query.get("limit")), maxlines);
			if (query.containsKey("case") && global.exactCaseIndex(iid.getDBname()) && ((String)query.get("case")).equalsIgnoreCase("exact"))
				exactCase = true;
			NamespaceFilter namespaces = new NamespaceFilter((String)query.get("namespaces"));
			SearchResults res = search(iid, searchterm, offset, limit, namespaces, what.equals("explain"), exactCase);
			if(res!=null && res.isRetry()){
				int retries = 0;
				if(iid.isSplit() || iid.isNssplit()){
					retries = iid.getSplitFactor()-2;
				} else if(iid.isMainsplit())
					retries = 1;
				
				while(retries > 0 && res.isRetry()){
					res = search(iid, searchterm, offset, limit, namespaces, what.equals("explain"), exactCase);
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
			              "titlematch, titleprefix, search, explain, quit, raw.");
			log.warn("Unknown request type [" + what + "].");
			return res;
		}
		return null;
	}
	
	/** Search mainpart or restpart of the split index */
	public SearchResults searchPart(IndexId iid, String searchterm, Query q, NamespaceFilterWrapper filter, int offset, int limit, boolean explain){
		if( ! (iid.isMainsplit() || iid.isNssplit()))
			return null;
		try {			
			SearcherCache cache = SearcherCache.getInstance();
			IndexSearcherMul searcher;
			long searchStart = System.currentTimeMillis();

			searcher = cache.getLocalSearcher(iid);
			NamespaceFilterWrapper localfilter = filter;
			if(iid.isMainsplit() && iid.isMainPart())
				localfilter = null;
			else if(iid.isNssplit() && !iid.isLogical() && iid.getNamespaceSet().size()==1 && !iid.getNamespaceSet().contains("<default>"))
				localfilter = null;
			if(localfilter != null)
				log.info("Using local filter: "+localfilter);
			Hits hits = searcher.search(q,localfilter);
			return makeSearchResults(searcher,hits,offset,limit,iid,searchterm,q,searchStart,explain);		
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
	public SearchResults search(IndexId iid, String searchterm, int offset, int limit, NamespaceFilter nsDefault, boolean explain, boolean exactCase){
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid,exactCase);
		if(nsDefault == null || nsDefault.cardinality() == 0)
			nsDefault = new NamespaceFilter("0"); // default to main namespace
		FieldBuilder.BuilderSet bs = new FieldBuilder(global.getLanguage(iid.getDBname()),exactCase).getBuilder(exactCase);
		WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),nsDefault,analyzer,bs,WikiQueryParser.NamespacePolicy.IGNORE);
		HashSet<NamespaceFilter> fields = parser.getFieldNamespaces(searchterm);
		NamespaceFilterWrapper nsfw = null;
		Query q = null;
		SearchResults res = null;
		long searchStart = System.currentTimeMillis();
		Hashtable<String,NamespaceFilter> cachedFilters = GlobalConfiguration.getInstance().getNamespacePrefixes();
		boolean searchAll = false;
		
		// if search is over one field, try to use filters
		if(fields.size()==1){
			if(fields.contains(new NamespaceFilter())){
				nsfw = null;  // empty filter: "all" keyword
				searchAll = true;
			} else if(!fields.contains(nsDefault)){ 
				// use the specified prefix in the query (if it can be cached)
				NamespaceFilter f = fields.toArray(new NamespaceFilter[] {})[0];
				if(f.cardinality()==1 || NamespaceCache.isComposable(f))
					nsfw = new NamespaceFilterWrapper(f);
			// use default filter if it's cached or composable of cached entries
			}else if(cachedFilters.containsValue(nsDefault) || NamespaceCache.isComposable(nsDefault))
				nsfw = new NamespaceFilterWrapper(nsDefault);
		}
		
		try {
			if(nsfw == null){
				if(searchAll)
					q = parser.parseFourPass(searchterm,WikiQueryParser.NamespacePolicy.IGNORE,iid.getDBname());
				else
					q = parser.parseFourPass(searchterm,WikiQueryParser.NamespacePolicy.REWRITE,iid.getDBname());				
			}
			else{
				q = parser.parseFourPass(searchterm,WikiQueryParser.NamespacePolicy.IGNORE,iid.getDBname());
				log.info("Using NamespaceFilterWrapper "+nsfw);
			}
			
			WikiSearcher searcher = new WikiSearcher(iid);
			TopDocs hits=null;
			// see if we can search only part of the index
			if(nsfw!=null && (iid.isMainPart() || iid.isNssplit())){
				String part = null;
				for(NamespaceFilter f : nsfw.getFilter().decompose()){
					if(part == null)
						part = iid.getPartByNamespace(f.getNamespace()).toString();
					else{
						if(!part.equals(iid.getPartByNamespace(f.getNamespace()).toString())){
							part = null; // namespace filter wants to search more than one index parts
							break;
						}
					}					
				}				
				if(part!=null){
					IndexId piid = IndexId.get(part);
					String host = searcher.getHost(piid);
					if(host == null){
						res = new SearchResults();
						res.setErrorMsg("Error contacting searcher for "+part);
						log.error("Error contacting searcher for "+part);
						return res;
					}
					RMIMessengerClient messenger = new RMIMessengerClient();
					return messenger.searchPart(piid,searchterm,q,nsfw,offset,limit,explain,host);
				}
			}
			// normal search
			try{
				hits = searcher.search(q,nsfw,offset+limit);
				res = makeSearchResults(searcher,hits,offset,limit,iid,searchterm,q,searchStart,explain);
				return res;
			} catch(Exception e){
				e.printStackTrace();
				res = new SearchResults();
				res.retry();
				log.warn("Retry, temportal error for query: ["+q+"] on "+iid);
				return res;
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

	/** Our scores can span several orders of magnitude, transform them to be more relevant to the user */
	public float transformScore(double score){
		return (float) (Math.log10(1+score*99)/2);		
	}
	
	protected SearchResults makeSearchResults(SearchableMul s, TopDocs hits, int offset, int limit, IndexId iid, String searchterm, Query q, long searchStart, boolean explain) throws IOException{
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
			float score = transformScore(scores[j]/maxScore); 
			ResultSet rs = new ResultSet(score,namespace,title);
			if(explain)
				rs.setExplanation(((WikiSearcher)s).explain(q,docids[j]));
			res.addResult(rs);
			j++;
		}
		
		return res;
	}
	
	/** Make search results from Hits */
	protected SearchResults makeSearchResults(SearchableMul s, Hits hits, int offset, int limit, IndexId iid, String searchterm, Query q, long searchStart, boolean explain) throws IOException{
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
		float maxScore = 1;
		if(numhits>0)
			maxScore = hits.score(0);
		for(Document doc : docs){
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			float score = transformScore(scores[j]/maxScore); 
			ResultSet rs = new ResultSet(score,namespace,title);
			if(explain)
				rs.setExplanation(((IndexSearcherMul)s).explain(q,docids[j]));
			res.addResult(rs);
			j++;
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
