package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.text.MessageFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.index.TermEnum;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.TopDocs;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.WikiQueryParser.NamespacePolicy;
import org.wikimedia.lsearch.analyzers.WikiQueryParser.ParsingOptions;
import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.frontend.SearchDaemon;
import org.wikimedia.lsearch.frontend.SearchServer;
import org.wikimedia.lsearch.highlight.Highlight;
import org.wikimedia.lsearch.highlight.HighlightResult;
import org.wikimedia.lsearch.highlight.Snippet;
import org.wikimedia.lsearch.index.MessengerThread;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.prefix.PrefixIndexBuilder;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.related.Related;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.SuggestQuery;
import org.wikimedia.lsearch.spell.SuggestResult;
import org.wikimedia.lsearch.spell.SuggestSimilar;
import org.wikimedia.lsearch.util.Localization;

/**
 * Search engine implementation. The implementation is independent of frontend used to
 * communicate with client. A generic container of search results is returned. 
 * 
 * @author rainman
 *
 */
public class SearchEngine {
	static org.apache.log4j.Logger log = Logger.getLogger(SearchEngine.class);

	protected final int MAXLINES = 1000;
	protected final int MAXPREFIX = 50;
	protected final int MAXOFFSET = 10000;
	protected static GlobalConfiguration global = null;
	protected static Configuration config = null;
	protected static SearcherCache cache = null;
	/** dbname -> ns_string -> ns_index */
	protected static Hashtable<String,Hashtable<String,Integer>> dbNamespaces = new Hashtable<String,Hashtable<String,Integer>>();
	/** dbname -> ns_index -> ns_string */
	protected static Hashtable<String,Hashtable<Integer,String>> dbNamespaceNames = new Hashtable<String,Hashtable<Integer,String>>();
	
	public SearchEngine(){
		if(config == null)
			config = Configuration.open();
		if(global == null)
			global = GlobalConfiguration.getInstance();
		if(cache == null)
			cache = SearcherCache.getInstance();
		
		// timelimit = config.getInt("Search","timelimit",5000);
	}
	
	/** Main search method, call this from the search frontend */
	public SearchResults search(String dbname, String what, String searchterm, HashMap query, double version) {
		IndexId iid = IndexId.get(dbname);
		if (what.equals("search") || what.equals("explain")) {
			int offset = 0, limit = 20; boolean exactCase = false;
			int iwlimit = 10; int iwoffset = 0;
			boolean searchOnly = false;
			if (query.containsKey("offset"))
				offset = Math.max(Integer.parseInt((String)query.get("offset")), 0);
			if (query.containsKey("limit"))
				limit = Math.min(Integer.parseInt((String)query.get("limit")), MAXLINES);
			if (query.containsKey("iwoffset"))
				iwoffset = Math.max(Integer.parseInt((String)query.get("iwoffset")), 0);
			if (query.containsKey("iwlimit"))
				iwlimit = Math.min(Integer.parseInt((String)query.get("iwlimit")), MAXLINES);
			if (query.containsKey("case") && global.exactCaseIndex(iid.getDBname()) && ((String)query.get("case")).equalsIgnoreCase("exact"))
				exactCase = true;
			if(query.containsKey("searchonly"))
				searchOnly = Boolean.parseBoolean((String)query.get("searchonly"));
			if(version <= 2)
				searchOnly = true;
			NamespaceFilter namespaces = new NamespaceFilter((String)query.get("namespaces"));
			SearchResults res = search(iid, searchterm, offset, limit, iwoffset, iwlimit, namespaces, what.equals("explain"), exactCase, false, searchOnly);
			/*if(res!=null && res.isRetry()){
				int retries = 1;
				
				while(retries > 0 && res.isRetry()){
					res = search(iid, searchterm, offset, limit, iwoffset, iwlimit, namespaces, what.equals("explain"), exactCase, false, searchOnly);
					retries--;
				}
				if(res.isRetry())
					res.setErrorMsg("Internal error, too many internal retries.");
			} */			
			return res;
		} else if (what.equals("raw") || what.equals("rawexplain")) {
			int offset = 0, limit = 20; boolean exactCase = false;
			int iwlimit = 10; int iwoffset = 0;
			if (query.containsKey("offset"))
				offset = Math.max(Integer.parseInt((String)query.get("offset")), 0);
			if (query.containsKey("limit"))
				limit = Math.min(Integer.parseInt((String)query.get("limit")), MAXLINES);
			if (query.containsKey("iwoffset"))
				iwoffset = Math.max(Integer.parseInt((String)query.get("iwoffset")), 0);
			if (query.containsKey("iwlimit"))
				iwlimit = Math.min(Integer.parseInt((String)query.get("iwlimit")), MAXLINES);
			if (query.containsKey("case") && global.exactCaseIndex(iid.getDBname()) && ((String)query.get("case")).equalsIgnoreCase("exact"))
				exactCase = true;
			NamespaceFilter namespaces = new NamespaceFilter((String)query.get("namespaces"));
			return search(iid, searchterm, offset, limit, iwoffset, iwlimit, namespaces, what.equals("rawexplain"), exactCase, true, true);
		} else if (what.equals("prefix")){
			int limit = MAXPREFIX;
			if (query.containsKey("limit"))
				limit = Math.min(Integer.parseInt((String)query.get("limit")), MAXPREFIX);
			NamespaceFilter nsf = null;
			if(query.containsKey("namespaces"))
				nsf = new NamespaceFilter((String)query.get("namespaces"));
			else
				nsf = iid.getDefaultNamespace();
			SearchResults res = searchPrefix(iid, searchterm, limit, nsf);
			if(query.containsKey("format")){
				String format = (String)query.get("format");
				if(format.equalsIgnoreCase("json"))
					res.setFormat(SearchResults.Format.JSON);
				else if(format.equalsIgnoreCase("opensearch"))
					res.setFormat(SearchResults.Format.OPENSEARCH);
			}
			return res;
		} else if (what.equals("related")){
			int offset = 0, limit = 20; 
			if (query.containsKey("offset"))
				offset = Math.max(Integer.parseInt((String)query.get("offset")), 0);
			if (query.containsKey("limit"))
				limit = Math.min(Integer.parseInt((String)query.get("limit")), MAXLINES);
			return searchRelated(iid, searchterm, offset, limit);
		} else if(what.equals("similar")){
			NamespaceFilter nsf = null;
			int dist = searchterm.length()/2;
			int limit = 10;
			if (query.containsKey("dist"))
				dist = Math.max(Integer.parseInt((String)query.get("dist")), dist);
			if(query.containsKey("namespaces"))
				nsf = new NamespaceFilter((String)query.get("namespaces"));
			else
				nsf = iid.getDefaultNamespace();
			if (query.containsKey("limit"))
				limit = Math.min(Integer.parseInt((String)query.get("limit")), MAXLINES);
			return searchSimilar(iid,searchterm,nsf,dist,limit);
		} else if(what.equals("suggest")){
			NamespaceFilter nsf = null;
			if(query.containsKey("namespaces"))
				nsf = new NamespaceFilter((String)query.get("namespaces"));
			else
				nsf = iid.getDefaultNamespace();
			return searchSuggest(iid,searchterm,nsf);

		} else {
			SearchResults res = new SearchResults();
			res.setErrorMsg("Unrecognized search type. Try one of: " +
			              "search, explain, raw, rawexplain, prefix, related.");
			log.warn("Unknown request type [" + what + "].");
			return res;
		}
	}
	
	public SearchResults searchSuggest(IndexId iid, String searchterm, NamespaceFilter nsf){
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid);
		FieldBuilder.BuilderSet bs = new FieldBuilder(iid).getBuilder();
		HashSet<String> stopWords = StopWords.getPredefinedSet(iid);
		WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),nsf,analyzer,bs,NamespacePolicy.IGNORE,stopWords);
		NamespaceFilterWrapper nsfw = new NamespaceFilterWrapper(nsf);
		SearchResults res = new SearchResults();
		res.setSuccess(true);
		suggest(iid,searchterm,parser,res,0,nsfw);
		return res;
	}
		
	/** Suggest similar titles */
	public SearchResults searchSimilar(IndexId iid, String searchterm, NamespaceFilter nsf, int dist, int limit) {
		SearchResults res = new SearchResults();
		try{
			RMIMessengerClient messenger = new RMIMessengerClient();
			String host = cache.getRandomHost(iid.getTitleNgram());
			if(host == null){
				res.setErrorMsg("No available searchers");
				return res;
			}
			ArrayList<String> keys = messenger.similar(host,iid.toString(),searchterm,nsf,dist);
			for(int i=0;i<keys.size() && i<limit;i++)
				res.addResult(new ResultSet(keys.get(i),1));
			res.setNumHits(keys.size());
			res.setSuccess(true);
			res.addInfo("similar",formatHost(host));
		} catch(IOException e){
			e.printStackTrace();
			res.setErrorMsg("I/O processing the request : "+e.getMessage());
			log.error("I/O error in searchSimilar() : "+e.getMessage());
		}
		return res;
	}

	/** Convert namespace names into numbers, e.g. User:Rainman into 2:Rainman  */
	protected String getKey(String title, IndexId iid){
		title = title.replace('_',' ');
		int colon = title.indexOf(':');
		if(colon != -1 && colon != title.length()-1){
			String ns = title.substring(0,colon);
			Integer inx = dbNamespaces.get(iid.getDBname()).get(ns.toLowerCase());
			if(inx != null){
				return inx +":"+ title.substring(colon+1);
			}
		}
		
		return "0:" + title;		
	}
	/** Get a valid namespace prefix, e.g. User from User:Moin */ 
	protected String getNamespace(String title, IndexId iid){
		title = title.replace('_',' ');
		int colon = title.indexOf(':');
		if(colon > 0 && colon != title.length()-1){
			String ns = title.substring(0,colon);
			Integer inx = dbNamespaces.get(iid.getDBname()).get(ns.toLowerCase());
			if(inx != null)
				return ns;
		}
		return "";
	}
	
	protected SearchResults searchRelated(IndexId iid, String searchterm, int offset, int limit) {
		RMIMessengerClient messenger = new RMIMessengerClient();
		String host = cache.getRandomHost(iid.getRelated());
		if(host == null){
			SearchResults res = new SearchResults();
			res.setErrorMsg("No available searchers");
			return res;
		}
		return messenger.searchRelated(host,iid.toString(),searchterm,offset,limit);
		
	}
	
	/** Search on a local related index (called via RMI) */
	public SearchResults searchRelatedLocal(IndexId iid, String searchterm, int offset, int limit) throws IOException {
		readLocalization(iid);
		IndexId rel = iid.getRelated();
		SearcherCache cache = SearcherCache.getInstance();
		SearchResults res = new SearchResults();

		IndexSearcherMul searcher = cache.getLocalSearcher(rel);
		IndexReader reader = searcher.getIndexReader();
		String key = getKey(searchterm,iid);
		TermDocs td = reader.termDocs(new Term("key",key));
		if(td.next()){
			ArrayList<RelatedTitle> col = Related.convertToRelatedTitleList(new StringList(reader.document(td.doc()).get("related")).toCollection());
			res.setNumHits(col.size());
			res.setSuccess(true);
			for(int i=offset;i<offset+limit && i<col.size();i++){
				RelatedTitle rt = col.get(i);
				Title t = rt.getRelated();
				ResultSet rs = new ResultSet(rt.getScore(),t.getNamespaceAsString(),t.getTitle());
				res.addResult(rs);
			}
			res.addInfo("related",global.getLocalhost());
			// highlight stuff
			Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid);
			NamespaceFilter nsDefault = new NamespaceFilter(key.substring(0,key.indexOf(':')));
			FieldBuilder.BuilderSet bs = new FieldBuilder(iid).getBuilder();
			HashSet<String> stopWords = StopWords.getPredefinedSet(iid);				
			WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),nsDefault,analyzer,bs,NamespacePolicy.IGNORE,stopWords);
			Query q = parser.parse(key.substring(key.indexOf(':')+1),new WikiQueryParser.ParsingOptions(true));
			highlight(iid,q,parser.getWordsClean(),searcher,res,true,true);
		} else{
			res.addInfo("related",global.getLocalhost());
			res.setSuccess(true);
			res.setNumHits(0);
		}

		return res;
	}

	protected void readLocalization(IndexId iid){
		if(!dbNamespaces.containsKey(iid.getDBname())){
			synchronized(dbNamespaces){
				HashMap<String,Integer> m = Localization.getLocalizedNamespaces(iid.getLangCode(),iid.getDBname());
				Hashtable<String,Integer> map = new Hashtable<String,Integer>();
				if(m != null)
					map.putAll(m);
				dbNamespaces.put(iid.getDBname(),map);
			}
		}
		if(!dbNamespaceNames.containsKey(iid.getDBname())){
			synchronized (dbNamespaceNames) {
				dbNamespaceNames.put(iid.getDBname(),Localization.getLocalizedNamespaceNames(iid.getLangCode(),iid.getDBname()));
			}
		}
	}
	
	protected SearchResults searchPrefix(IndexId iid, String searchterm, int limit, NamespaceFilter nsf) {
		IndexId pre = iid.getPrefix();
		try{
			RMIMessengerClient messenger = new RMIMessengerClient();
			// find host 
			String host = cache.getRandomHost(pre);
			if(host == null){
				SearchResults res = new SearchResults();
				res.setErrorMsg("No available hosts for "+pre);
				return res;
			}
			return messenger.searchPrefix(host,pre.toString(),searchterm,limit,nsf);
		} catch(IOException e){
			e.printStackTrace();
			log.error("Error opening searcher in prefixSearch on "+pre+" : "+e.getMessage());
			SearchResults res = new SearchResults();
			res.setErrorMsg("I/O error on index "+pre);
			return res;
		}
	}
	
	static class PrefixMatch {
		String key;
		int score;
		String redirect=null;
		
		PrefixMatch(String serialized){
			int d1 = serialized.indexOf(' ');
			int d2 = serialized.indexOf(' ',d1+1);
			if(d1 == -1)
				d1 = serialized.length();
			else
				this.redirect = serialized.substring(d2+1).replace('_',' '); 
			this.key = serialized.substring(0,d1).replace('_',' ');
			this.score = Integer.parseInt(serialized.substring(d1+1,d2));
		}
		static class Comparator implements java.util.Comparator<PrefixMatch> {
			public int compare(PrefixMatch o1, PrefixMatch o2) {
				return o2.score-o1.score;
			}			
		}
		public String toString(){
			return key+" "+score+(!redirect.equals("")? " -> "+redirect : "");
		}
	}
	
	public SearchResults searchPrefixLocal(IndexId iid, String searchterm, int limit, NamespaceFilter nsf, IndexSearcherMul searcher) {
		readLocalization(iid);
		IndexId pre = iid.getPrefix();		
		SearchResults res = new SearchResults();
		long start = System.currentTimeMillis();
		try {
			FilterFactory filters = new FilterFactory(iid);
			//long start = System.currentTimeMillis();
			String prefixKey = getKey(searchterm.toLowerCase(),iid);
			prefixKey = filters.canonicalStringFilter(prefixKey);
			String prefixNamespace = prefixKey.substring(0,prefixKey.indexOf(':'));
			String namespace = getNamespace(searchterm,iid);
			Hashtable<Integer,String> nsNames = dbNamespaceNames.get(iid.getDBname());
			
			ArrayList<String> keys = new ArrayList<String>();
			if(prefixKey.startsWith("0:")){
				String title = prefixKey.substring(2);
				String alt = null;
				if(title.startsWith("\"") && title.length()>1)
					alt = title.substring(1); 
				for(Integer ns : nsf.getNamespacesOrdered()){
					keys.add(ns+":"+title);
					if(alt != null)
						keys.add(ns+":"+alt);
				}

			} else
				keys.add(prefixKey);
						
			ArrayList<PrefixMatch> results = new ArrayList<PrefixMatch>();
			IndexReader reader = searcher.getIndexReader();
			
			for(String key : keys){				
				TermDocs td = reader.termDocs(new Term("prefix",key));
				if(td.next()){
					// found entry with a prefix, return				
					StringList sl = new StringList(reader.document(td.doc()).get("articles"));
					int limitCount = 0;
					Iterator<String> it = sl.iterator();
					while(it.hasNext()){
						if(limitCount >= limit)
							break;
						results.add(new PrefixMatch(it.next()));						
						limitCount++;
					}					
				} else{
					// check if it's an unique prefix
					TermEnum te = reader.terms(new Term("key",key));
					if(te.term() != null){
						String r = te.term().text();
						if(r.startsWith(key)){
							TermDocs td1 = reader.termDocs(new Term("key",r));
							if(td1.next()){
								PrefixMatch m = new PrefixMatch(reader.document(td1.doc()).get("article"));
								results.add(m);

							}
						}
					}
				}
			}
			
			// make results
			
			if(keys.size() > 1) // if we did multiple fetch we need to resort things
				Collections.sort(results,new PrefixMatch.Comparator());
			
			HashSet<String> selected = new HashSet<String>();
			for(PrefixMatch m : results){
				if(selected.contains(m) || (m.redirect!=null && selected.contains(m.redirect)))
					continue;
				if(res.getResults().size() >= limit)
					break;
				ResultSet rs = new ResultSet(m.key,m.score);
				String ns = m.key.substring(0,m.key.indexOf(':'));
				if(ns.equals("0"))
					rs.setNamespaceTextual("");
				else if(prefixNamespace.equals(ns))
					rs.setNamespaceTextual(capitalizeFirst(namespace));
				else 
					rs.setNamespaceTextual(nsNames.get(Integer.parseInt(ns)));
					
				res.addResult(rs);
				selected.add(m.key);
				if(m.redirect != null && m.redirect.length()>0)
					selected.add(m.redirect);
			}
			res.setNumHits(res.getResults().size());
			res.setSuccess(true);
			res.addInfo("prefix",global.getLocalhost());
			
			// don't log but send stats
			sendStats(start-System.currentTimeMillis());
		} catch (IOException e) {
			e.printStackTrace();
			log.error("Internal error in prefixSearch on "+pre+" : "+e.getMessage());
			res.setErrorMsg("I/O error on index "+pre);
		}
		return res;
	}
	
	private String capitalizeFirst(String str){
		if(str == null || str.equals(""))
			return str;
		if(str.length()==1)
			return str.toUpperCase();
		else
			return Character.toUpperCase(str.charAt(0))+str.substring(1);
	}
	
	/** Search a single titles index part */
	public SearchResults searchTitles(IndexId iid, String searchterm, ArrayList<String> words, Query q, SuffixNamespaceWrapper filter, int offset, int limit, boolean explain, boolean sortByPhrases){
		if(!iid.isTitlesBySuffix())
			return null;
		try {			
			SearcherCache cache = SearcherCache.getInstance();
			IndexSearcherMul searcher;
			long searchStart = System.currentTimeMillis();
			searcher = cache.getLocalSearcher(iid);
			TopDocs hits = searcher.search(q,filter,offset+limit);
			// search
			SearchResults res = makeTitlesSearchResults(searcher,hits,offset,limit,iid,searchterm,q,searchStart,explain);
			// highlight
			highlightTitles(iid,q,words,searcher,res,sortByPhrases,false);
			return res;
		} catch (IOException e) {
			e.printStackTrace();
			SearchResults res = new SearchResults();
			res.setErrorMsg("Internal error in SearchEngine: "+e.getMessage());
			log.error("I/O error in searchTitles(): "+e.getMessage());
			return res;
		}
	}

	/** Search mainpart or restpart of the split index */
	public HighlightPack searchPart(IndexId iid, String searchterm, Query q, NamespaceFilterWrapper filter, int offset, int limit, boolean explain){
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
			TopDocs hits = searcher.search(q,localfilter,offset+limit);
			SearchResults res = makeSearchResults(searcher,hits,offset,limit,iid,searchterm,q,searchStart,explain);
			HighlightPack pack = new HighlightPack(res);
			// pack extra info needed for highlighting
			pack.terms = getTerms(q,"contents");
			pack.dfs = searcher.docFreqs(pack.terms);
			pack.maxDoc = searcher.maxDoc();
			return pack;
		} catch (IOException e) {
			e.printStackTrace();
			HighlightPack pack = new HighlightPack(new SearchResults());
			pack.res.setErrorMsg("Internal error in SearchEngine: "+e.getMessage());
			log.error("Internal error in SearchEngine while trying to search main part: "+e.getMessage());
			return pack;
		}
		
	}
	
	/**
	 * Search on iid, with query searchterm. View results from offset to offset+limit, using
	 * the default namespaces filter
	 */
	public SearchResults search(IndexId iid, String searchterm, int offset, int limit, int iwoffset, int iwlimit, 
			NamespaceFilter nsDefault, boolean explain, boolean exactCase, boolean raw, boolean searchOnly){
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid,exactCase);
		if(nsDefault == null || nsDefault.cardinality() == 0)
			nsDefault = new NamespaceFilter("0"); // default to main namespace
		FieldBuilder.BuilderSet bs = new FieldBuilder(iid,exactCase).getBuilder(exactCase);
		HashSet<String> stopWords = StopWords.getPredefinedSet(iid);
		WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),nsDefault,analyzer,bs,NamespacePolicy.IGNORE,stopWords);
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
				nsfw = new NamespaceFilterWrapper(new NamespaceFilter());  // empty filter: "all" keyword
				searchAll = true;
			} else if(!fields.contains(nsDefault)){ 
				// use the specified prefix in the query (if it can be cached)
				NamespaceFilter f = fields.toArray(new NamespaceFilter[] {})[0];
				if(f.cardinality()==1 || NamespaceCache.isComposable(f))
					nsfw = new NamespaceFilterWrapper(f);
			// use default filter if it's cached or composable of cached entries
			} else if(cachedFilters.containsValue(nsDefault) || NamespaceCache.isComposable(nsDefault))
				nsfw = new NamespaceFilterWrapper(nsDefault);
		}
		
		WikiSearcher searcher = null;
		try {
			//q = parseQuery(searchterm,parser,iid,raw,nsfw,searchAll);
			
			TopDocs hits=null;
			// see if we can search only part of the index
			if(nsfw!=null && !nsfw.getFilter().isAll() && (iid.isMainsplit() || iid.isNssplit())){
				HashSet<IndexId> parts = new HashSet<IndexId>();
				for(NamespaceFilter f : nsfw.getFilter().decompose()){
					parts.add(iid.getPartByNamespace(f.getNamespace()));										
				}				
				if(parts.size() == 1){
					IndexId piid = parts.iterator().next();
					if(!piid.isFurtherSubdivided()){
						// search on single index part
						String host = cache.getRandomHost(piid);
						if(host == null){
							res = new SearchResults();
							res.setErrorMsg("No available searcher for "+piid);
							log.error("No available searcher for "+piid);
							return res;
						}
						// query 
						Wildcards wildcards = new Wildcards(piid,host,exactCase);
						q = parseQuery(searchterm,parser,iid,raw,nsfw,searchAll,wildcards);
						
						RMIMessengerClient messenger = new RMIMessengerClient();						
						HighlightPack pack = messenger.searchPart(piid,searchterm,q,nsfw,offset,limit,explain,host);
						res = pack.res;
						res.addInfo("search",formatHost(host));
						if(!searchOnly){
							highlight(iid,q,parser.getWordsClean(),pack.terms,pack.dfs,pack.maxDoc,res,exactCase,null,parser.hasPhrases(),false);
							fetchTitles(res,searchterm,nsfw,iid,parser,offset,iwoffset,iwlimit,explain);
							suggest(iid,searchterm,parser,res,offset,nsfw);
						}
						return res;
					}
				} 
				// construct a searcher on required parts
				HashSet<IndexId> expanded = new HashSet<IndexId>();
				for(IndexId p : parts){
					if(p.isFurtherSubdivided())
						expanded.addAll(p.getPhysicalIndexIds());
					else
						expanded.add(p);
				}
				log.info("Making searcher for "+expanded);
				searcher = new WikiSearcher(expanded);
			
			}
			if(searcher == null)
				searcher = new WikiSearcher(iid);
			// normal search
			try{
				// query 
				Wildcards wildcards = new Wildcards(searcher.getAllHosts(),exactCase);
				q = parseQuery(searchterm,parser,iid,raw,nsfw,searchAll,wildcards);
								
				hits = searcher.search(q,nsfw,offset+limit);
				res = makeSearchResults(searcher,hits,offset,limit,iid,searchterm,q,searchStart,explain);
				res.addInfo("search",formatHosts(searcher.getAllHosts().values()));
				if(!searchOnly){
					highlight(iid,q,parser.getWordsClean(),searcher,parser.getHighlightTerms(),res,exactCase,parser.hasPhrases(),false);
					fetchTitles(res,searchterm,nsfw,iid,parser,offset,iwoffset,iwlimit,explain);
					suggest(iid,searchterm,parser,res,offset,nsfw);
				}
				return res;
			} catch(Exception e){				
				/* if(e.getMessage()!=null && e.getMessage().equals("time limit")){
					res = new SearchResults();
					res.setErrorMsg("Time limit of "+timelimit+"ms exceeded");
					log.warn("Execution time limit of "+timelimit+"ms exceeded.");
					return res;
				} */
				e.printStackTrace();
				res = new SearchResults();
				res.retry();
				log.warn("Retry, temportal error for query: ["+q+"] on "+iid+" : "+e.getMessage());
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
	
	public String formatHost(String host){
		if(RMIMessengerClient.isLocal(host))
			return global.getLocalhost();
		else
			return host;
	}
	
	public String formatHosts(Collection<String> hosts){
		StringBuilder sb = new StringBuilder();
		for(String h : hosts){
			if(sb.length() > 0)
				sb.append(",");
			sb.append(formatHost(h));
		}
		return sb.toString();
	}

	/** "Did you mean.." engine, use highlight results (if any) to refine suggestions, call after all other results are already obtained */
	protected void suggest(IndexId iid, String searchterm, WikiQueryParser parser, SearchResults res, int offset, NamespaceFilterWrapper nsfw) {
		if(offset == 0 && iid.hasSpell()){
			if(res.isFoundAllInTitle())
				return;
			if(nsfw == null)
				return; // query on many overlapping namespaces, won't try to spellcheck to not mess things up
			// suggest !
			res.setSuggest(null);
			ArrayList<Token> tokens = parser.tokenizeForSpellCheck(searchterm);
			if(tokens.size() == 0)
				return; // nothing to spellchek
			RMIMessengerClient messenger = new RMIMessengerClient();
			// find host 
			String host = cache.getRandomHost(iid.getSpell());
			if(host == null)
				return; // no available 
			Suggest.ExtraInfo info = new Suggest.ExtraInfo(res.getPhrases(),res.getFoundInContext(),res.getFoundInTitles(),res.getFirstHitRank(),res.isFoundAllInAltTitle());
			SuggestQuery sq = messenger.suggest(host,iid.toString(),searchterm,tokens,info,nsfw.getFilter());
			res.setSuggest(sq);	
			res.addInfo("suggest",formatHost(host));
		}
	}
		
	protected Query parseQuery(String searchterm, WikiQueryParser parser, IndexId iid, boolean raw, NamespaceFilterWrapper nsfw, boolean searchAll, Wildcards wildcards) throws ParseException {
		Query q = null;
		Fuzzy fuzzy = null;
		if(iid.hasSpell()){
			String host = cache.getRandomHost(iid.getSpell());
			if(host != null)
				fuzzy = new Fuzzy(iid,host);
		}
		if(raw){
			// do minimal parsing, make a raw query
			parser.setNamespacePolicy(WikiQueryParser.NamespacePolicy.LEAVE);
			q = parser.parseRaw(searchterm);
		} else if(nsfw == null){
			if(searchAll)
				q = parser.parse(searchterm,new ParsingOptions(NamespacePolicy.IGNORE,wildcards,fuzzy));
			else
				q = parser.parse(searchterm,new ParsingOptions(NamespacePolicy.REWRITE,wildcards,fuzzy));				
		} else{
			q = parser.parse(searchterm,new ParsingOptions(NamespacePolicy.IGNORE,wildcards,fuzzy));
			log.info("Using NamespaceFilterWrapper "+nsfw);
		}
		return q;
	}

	/** Our scores can span several orders of magnitude, transform them to be more relevant to the user */
	public float transformScore(double score){
		//return (float) (Math.log10(1+score*99)/2);
		return (float) score;
	}
	
	
	/** Fetch related interwiki titles 
	 * @throws IOException */
	protected void fetchTitles(SearchResults res, String searchterm, NamespaceFilterWrapper nsfw, IndexId iid, WikiQueryParser parser, int offset, int iwoffset, int iwlimit, boolean explain){
		if(!iid.hasTitlesIndex())
			return;
		if(offset != 0)
			return; // do titles search only for first page of normal-search results
		try{
			IndexId titles = iid.getTitlesIndex();
			IndexId main = titles.getDB();
			SuffixFilter sf = null;
			NamespaceFilter nsf = null;
			if(nsfw != null)
				nsf = nsfw.getFilter();

			ArrayList<String> words = parser.getWordsClean();
			Query q = parser.parseForTitles(searchterm);

			// this databases is in one part alone, we can optimize this case
			if(titles.getTitlesBySuffixCount()==1){
				IndexId target = null;

				// optimized case, we only need to search the other part of the index
				if(main.getSplitFactor()==2)
					target = (main.getPart(1) != titles)? main.getPart(1) : main.getPart(2); // get other part
					// not a split index, also search a single part
					else if(main.getSplitFactor()==1){
						target = titles;
						sf = new SuffixFilter(iid.getTitlesSuffix());
					}

				if(target != null){
					String host = cache.getRandomHost(target);
					if(host == null)
						return; // no available searchers
					RMIMessengerClient messenger = new RMIMessengerClient();
					SuffixNamespaceWrapper wrap = null;
					if(nsf != null || sf != null)
						wrap = new SuffixNamespaceWrapper(new SuffixNamespaceFilter(nsf,sf,iid,target));
					SearchResults r = messenger.searchTitles(host,target.toString(),searchterm,words,q,wrap,iwoffset,iwlimit,explain,parser.hasPhrases());
					if(r.isSuccess()){
						res.setTitles(r.getResults());
						res.setTitlesTotal(r.getNumHits());						
					} else
						log.error("Error getting grouped titles results from "+host+":"+r.getErrorMsg());
					res.addInfo("interwiki",formatHost(host));
					return;
				}			
			}

			// otherwise, we need to search all parts of the index
			long searchStart = System.currentTimeMillis();
			WikiSearcher searcher = new WikiSearcher(main);
			sf = new SuffixFilter(iid.getTitlesSuffix());
			SuffixNamespaceWrapper wrap = new SuffixNamespaceWrapper(new SuffixNamespaceFilter(nsf,sf,iid,main));

			log.info("Using titles filter: "+wrap);

			TopDocs hits = searcher.search(q,wrap,iwoffset+iwlimit);
			SearchResults r = makeTitlesSearchResults(searcher,hits,iwoffset,iwlimit,main,searchterm,q,searchStart,explain);
			highlightTitles(main,q,words,searcher,r,parser.hasWildcards(),false);

			if(r.isSuccess()){
				res.setTitles(r.getResults());				
				//if(r.isFoundAllInTitle())
				//	res.setFoundAllInTitle(true);
				//res.addToFirstHitRank(r.getNumHits());
			} else
				log.error("Error getting grouped titles search results: "+r.getErrorMsg());
			res.addInfo("interwiki",formatHosts(searcher.getAllHosts().values()));
			
		} catch(Exception e){
			e.printStackTrace();
			log.error("Error fetching grouped titles: "+e.getMessage());
		}
	}
	
	protected SearchResults makeSearchResults(SearchableMul s, TopDocs hits, int offset, int limit, IndexId iid, String searchterm, Query q, long searchStart, boolean explain) throws IOException{
		SearchResults res = new SearchResults();
		int numhits = hits.totalHits;
		res.setSuccess(true);			
		res.setNumHits(numhits);
		logRequest(iid,"search",searchterm, q, numhits, searchStart, s);
		
		int size = min(limit+offset,MAXOFFSET,numhits) - offset;
		int[] docids = new int[size]; 
		float[] scores = new float[size];
		// fetch documents
		for(int i=offset, j=0 ; i<limit+offset && i<MAXOFFSET && i<numhits; i++, j++){
			docids[j] = hits.scoreDocs[i].doc;
			scores[j] = hits.scoreDocs[i].score;
		}
		// fetch documents
		Document[] docs = s.docs(docids);
		int j=0;
		//float maxScore = hits.getMaxScore();
		float maxScore = 1;
		for(Document doc : docs){
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			float score = transformScore(scores[j]/maxScore); 
			ResultSet rs = new ResultSet(score,namespace,title);
			if(explain)
				rs.setExplanation(((Searcher)s).explain(q,docids[j]));
			res.addResult(rs);
			j++;
		}
		
		return res;
	}
	
	protected SearchResults makeTitlesSearchResults(SearchableMul s, TopDocs hits, int offset, int limit, IndexId iid, String searchterm, Query q, long searchStart, boolean explain) throws IOException{
		SearchResults res = new SearchResults();
		int numhits = hits.totalHits;
		res.setSuccess(true);			
		res.setNumHits(numhits);
		logRequest(iid,"search",searchterm, q, numhits, searchStart, s);
		
		int size = min(limit+offset,MAXOFFSET,numhits) - offset;
		int[] docids = new int[size]; 
		float[] scores = new float[size];
		// fetch documents
		for(int i=offset, j=0 ; i<limit+offset && i<MAXOFFSET && i<numhits; i++, j++){
			docids[j] = hits.scoreDocs[i].doc;
			scores[j] = hits.scoreDocs[i].score;
		}
		// fetch documents
		Document[] docs = s.docs(docids);
		int j=0;
		//float maxScore = hits.getMaxScore();
		float maxScore = 1;
		for(Document doc : docs){
			String namespace = doc.get("namespace");
			String title = doc.get("title");
			String suffix = doc.get("suffix");
			String interwiki = iid.getInterwikiBySuffix(suffix);
			float score = transformScore(scores[j]/maxScore); 
			ResultSet rs = new ResultSet(score,namespace,title,suffix,interwiki);
			IndexId target = iid.getIndexIdforSuffix(suffix);
			readLocalization(target);
			rs.setNamespaceTextual(dbNamespaceNames.get(target.getDBname()).get(Integer.parseInt(namespace)));
			if(explain)
				rs.setExplanation(((Searcher)s).explain(q,docids[j]));
			res.addResult(rs);
			j++;
		}
		
		return res;
	}
	
	protected Term[] getTerms(Query q, String field){
		String fieldExact = field+"_exact";
		HashSet<Term> termSet = new HashSet<Term>();
		q.extractTerms(termSet);
		HashSet<Term> forbidden = new HashSet<Term>();
		WikiQueryParser.extractForbiddenInto(q,forbidden);
		Iterator<Term> it = termSet.iterator();
		while(it.hasNext()){
			Term t = it.next();
			String fieldName = t.field(); 
			if(!(fieldName.equals(field) || fieldName.equals(fieldExact)) || forbidden.contains(t))
				it.remove();
		}
		return termSet.toArray(new Term[] {});
	}
	
	/** Highlight search results, and set the property in ResultSet */
	protected void highlight(IndexId iid, Query q, ArrayList<String> words, WikiSearcher searcher, Term[] terms, SearchResults res, boolean exactCase, boolean sortByPhrases, boolean alwaysIncludeFirst) throws IOException{
		if(terms == null)
			return;
		int[] df = searcher.docFreqs(terms); 
		int maxDoc = searcher.maxDoc();
		highlight(iid,q,words,terms,df,maxDoc,res,exactCase,null,sortByPhrases,alwaysIncludeFirst);
	}
	
	/** Highlight search results, and set the property in ResultSet */
	protected void highlight(IndexId iid, Query q, ArrayList<String> words, IndexSearcherMul searcher, SearchResults res, boolean sortByPhrases, boolean alwaysIncludeFirst) throws IOException{
		Term[] terms = getTerms(q,"contents");
		if(terms == null)
			return;
		int[] df = searcher.docFreqs(terms); 
		int maxDoc = searcher.maxDoc();
		highlight(iid,q,words,terms,df,maxDoc,res,false,null,sortByPhrases,alwaysIncludeFirst);
	}
	
	/** Highlight search results from titles index */
	protected void highlightTitles(IndexId iid, Query q, ArrayList<String> words, IndexSearcherMul searcher, SearchResults res, boolean sortByPhrases, boolean alwaysIncludeFirst) throws IOException{
		Term[] terms = getTerms(q,"alttitle");
		if(terms == null)
			return;
		int[] df = searcher.docFreqs(terms); 
		int maxDoc = searcher.maxDoc();
		highlight(iid,q,words,terms,df,maxDoc,res,false,searcher.getIndexReader(),sortByPhrases,alwaysIncludeFirst);
		resolveInterwikiNamespaces(res,iid);
	}
	
	/** Highlight search results from titles index using a wikisearcher */
	protected void highlightTitles(IndexId iid, Query q, ArrayList<String> words, WikiSearcher searcher, SearchResults res, boolean sortByPhrases, boolean alwaysIncludeFirst) throws IOException{
		Term[] terms = getTerms(q,"alttitle");
		if(terms == null)
			return;
		int[] df = searcher.docFreqs(terms); 
		int maxDoc = searcher.maxDoc();
		highlight(iid,q,words,terms,df,maxDoc,res,false,null,sortByPhrases,alwaysIncludeFirst);
		resolveInterwikiNamespaces(res,iid);
	}
	
	/** Highlight article (don't call directly, use one of the interfaces above instead) */
	protected void highlight(IndexId iid, Query q, ArrayList<String> words, Term[] terms, int[] df, int maxDoc, SearchResults res, boolean exactCase, IndexReader reader, boolean sortByPhrases, boolean alwaysIncludeFirst) throws IOException{
		// iid -> array of keys
		HashMap<IndexId,ArrayList<String>> map = new HashMap<IndexId,ArrayList<String>>();
		iid = iid.getHighlight();
		// key -> result
		HashMap<String,ResultSet> keys = new HashMap<String,ResultSet>();
		for(ResultSet r : res.getResults()){
			IndexId piid = iid.getPartByNamespace(r.namespace);
			ArrayList<String> hits = map.get(piid);
			if(hits == null){
				hits = new ArrayList<String>();
				map.put(piid,hits);
			}
			hits.add(r.getKey());
			keys.put(r.getKey(),r);
		}
		// highlight!
		HashSet<String> stopWords = StopWords.getPredefinedSet(iid);
		HashMap<String,HighlightResult> results = new HashMap<String,HighlightResult>();
		RMIMessengerClient messenger = new RMIMessengerClient();
		HashSet<String> hosts = new HashSet<String>();

		for(Entry<IndexId,ArrayList<String>> e : map.entrySet()){
			IndexId piid = e.getKey();
			for(IndexId hiid : piid.getPhysicalIndexIds()){
				Highlight.ResultSet rs = null;
				if(reader != null){ 
					// we got a local reader, use it
					// FIXME: this works only if is exactly one physical index
					rs = Highlight.highlight(e.getValue(),hiid,terms,df,maxDoc,words,stopWords,exactCase,reader,sortByPhrases,alwaysIncludeFirst);
					hosts.add(global.getLocalhost());
				} else{ 
					// remote call
					String host = cache.getRandomHost(hiid);
					if(host == null)
						continue; // no available hosts
					rs = messenger.highlight(host,e.getValue(),hiid.toString(),terms,df,maxDoc,words,exactCase,sortByPhrases,alwaysIncludeFirst);
					hosts.add(host);
				}
				results.putAll(rs.highlighted);
				res.getPhrases().addAll(rs.phrases);
				res.getFoundInContext().addAll(rs.foundInContext);
				if(rs.foundAllInTitle && words.size()>1)
					res.setFoundAllInTitle(true);
				if(rs.foundAllInAltTitle && words.size()>1)
					res.setFoundAllInAltTitle(true);
				res.getFoundInTitles().addAll(rs.foundInTitles);
			}
		}
		res.addToFirstHitRank(res.getNumHits());
		// set highlight property
		for(Entry<String,HighlightResult> e : results.entrySet()){
			keys.get(e.getKey()).setHighlight(e.getValue());
		}
		res.addInfo("highlight",formatHosts(hosts));
	}
	
	/** 
	 * Ugly hack to resolve 100:Something namespaces into proper prefixed namespaces
	 * This is needed since MediaWiki cannot handle interwiki namespace numerals  
	 */
	protected void resolveInterwikiNamespaces(SearchResults res, IndexId titles){
		for(ResultSet r : res.getResults()){
			HighlightResult h = r.getHighlight();
			Snippet redirect = h.getRedirect();
			if(redirect != null){
				String key = redirect.getOriginalText();
				String ns = key.substring(0,key.indexOf(':'));
				String title = key.substring(key.indexOf(':')+1);
				if(ns.equals(r.getNamespace())) // same as title
					setPrefixedTitle(redirect,r.getNamespaceTextual(),title);
				else{
					// find the target index based on suffix, use that to find ns name 
					IndexId targ = null;
					for(IndexId part : titles.getPhysicalIndexIds()){
						targ = part.getIndexIdforSuffix(r.getSuffix());
						if(targ != null)
							break;
					}
					if(targ != null){
						readLocalization(targ);
						setPrefixedTitle(redirect,dbNamespaceNames.get(targ.getDBname()).get(Integer.parseInt(ns)),title);
					} else{
						log.warn("Cannot resolve interwiki namespace for suffix="+r.getSuffix()+", ns="+ns+", on "+titles);
						// this is prolly wrong, but makes a more graceful failure
						setPrefixedTitle(redirect,r.getNamespaceTextual(),title);
					}
				}
			}
		}
	}
	
	private void setPrefixedTitle(Snippet redirect, String ns, String title){
		if(ns.equals(""))
			redirect.setOriginalText(title);
		else
			redirect.setOriginalText(ns+":"+title);
	}
	
	protected int min(int i1, int i2, int i3){
		return Math.min(Math.min(i1,i2),i3);
	}
	
	protected void sendStats(long delta){
		boolean succ = delta < 10000; // we queries taking more than 10s as bad
		if(SearchServer.stats != null)
			SearchServer.stats.add(succ, delta, SearchDaemon.getOpenCount());
	}
	
	protected void logRequest(IndexId iid, String what, String searchterm, Query query, int numhits, long start, Searchable searcher) {
		long delta = System.currentTimeMillis() - start;
		sendStats(delta);
		log.info(MessageFormat.format("{0} {1}: query=[{2}] parsed=[{3}] hit=[{4}] in {5}ms using {6}",
			new Object[] {what, iid.toString(), searchterm, query==null? "" : query.toString(), new Integer(numhits), new Long(delta), searcher.toString()}));
	}
}
