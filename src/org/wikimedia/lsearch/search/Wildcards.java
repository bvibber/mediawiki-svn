package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.rmi.RemoteException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.DisjunctionMaxQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.WildcardTermEnum;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.util.StringUtils;

/**
 * Wildcard-search related functions
 * @author rainman
 *
 */
public class Wildcards {
	protected static Logger log = Logger.getLogger(Wildcards.class);
	public static final int MAX_TERMS = 1024;
	public static final int MAX_PATTERNS_PER_QUERY = 3;
	protected static SearcherCache searcherCache = null;
	protected enum WildcardType { PREFIX, SUFFIX, INVALID };
	
	/** wildcard pattern -> terms */
	protected HashMap<String,HashSet<String>> wildcardCache = new HashMap<String,HashSet<String>>();
	/** iid -> host */
	protected HashMap<String,String> hosts = new HashMap<String,String>();
	
	protected RMIMessengerClient client = null;
	protected boolean exactCase; 
	
	public Wildcards(IndexId iid, String host, boolean exactCase){
		hosts.put(iid.toString(),host);
		this.exactCase = exactCase;
	}
	
	public Wildcards(HashMap<String,String> iidHostMapping, boolean exactCase){
		hosts.putAll(iidHostMapping);
		this.exactCase = exactCase;
	}
	
	/** 
	 * Make a DisjunctionMaxQuery of expanded wildcard
	 * 
	 * @param wildcard
	 * @param field
	 * @return null if there is no match, or on error
	 */
	public Query makeQuery(String wildcard, String field){
		if(client == null)
			client = new RMIMessengerClient();
		
		HashSet<String> terms = wildcardCache.get(wildcard);
		if(terms == null){
			if(wildcardCache.size() >= MAX_PATTERNS_PER_QUERY){
				return null; // limit number of wildcard queries
			}
			terms = new HashSet<String>();		
			for(Entry<String,String> e : hosts.entrySet()){
				try {
					terms.addAll(client.getTerms(e.getValue(),e.getKey(),wildcard,exactCase));
				} catch (RemoteException e1) {
					e1.printStackTrace();
					log.warn("Cannot get terms for "+wildcard+" on host "+e.getValue()+" for "+e.getKey());
				}
			}
			wildcardCache.put(wildcard,terms);
			log.info("Using "+terms.size()+" terms for pattern="+wildcard);
		}
		
		if(terms.size() == 0)
			return null; // no match or error
				
		return makeQuery(terms,field);
	}
	
	/** Construct DijunctionMaxQuery from terms */
	protected Query makeQuery(HashSet<String> terms, String field){
		if(terms.size() > MAX_TERMS){
			HashSet<String> temp = new HashSet<String>();
			int count = 0;
			for(String t : terms){
				if(count >= MAX_TERMS)
					break;
				temp.add(t);
				count++;
			}
			terms = temp;
		}
		DisjunctionMaxQuery q = new DisjunctionMaxQuery(0);
		for(String t : terms){
			q.add(new TermQuery(new Term(field,t)));
		}
		return q;		
	}
	
	public boolean hasWildcards(){
		return wildcardCache.size() > 0;
	}
	
	protected static int first(int a, int b){
		if(a < 0)
			return b;
		if(b < 0)
			return a;
		return Math.min(a,b);
	}
	
	protected static int last(int a, int b){
		if(a < 0)
			return b;
		if(b < 0)
			return a;
		return Math.max(a,b);
	}
	
	protected static WildcardType getType(String wildcard){
		if(wildcard == null || wildcard.equals(""))
			return WildcardType.INVALID;
		boolean pre = wildcard.startsWith("*") || wildcard.startsWith("?");
		boolean suff = wildcard.endsWith("*") || wildcard.endsWith("?"); 
		int preInx = first(wildcard.indexOf("*"),wildcard.indexOf("?"));
		int sufInx = last(wildcard.lastIndexOf("*"),wildcard.lastIndexOf("?"));
		if(pre && !suff)
			return WildcardType.PREFIX;
		else if(suff && !pre)
			return WildcardType.SUFFIX;
		else if(preInx != -1 && sufInx != -1){
			if(preInx > (wildcard.length()-sufInx-1)) // more letter at the beginning of the word
				return WildcardType.PREFIX;
			else
				return WildcardType.SUFFIX;
		} else 
			return WildcardType.INVALID;		
	}
	
	public static ArrayList<String> getLocalTerms(IndexId iid, String wildcard, boolean exactCase) throws IOException {
		if(searcherCache == null)
			searcherCache = SearcherCache.getInstance();
		ArrayList<String> ret = new ArrayList<String>();
		// check type of wildcard
		WildcardType type = getType(wildcard);
		if(type == WildcardType.INVALID)
			return ret;
		// check searcher
		IndexSearcherMul searcher = searcherCache.getLocalSearcher(iid); 
		if(searcher == null)
			throw new IOException(iid+" not a local index, or index not available");
		
		// get field
		IndexReader reader = searcher.getIndexReader();		
		String field = null;
		Term wildcardTerm = null;
		FieldNameFactory fields = new FieldNameFactory(exactCase);
		if(type == WildcardType.PREFIX){
			field = fields.title();
			wildcardTerm = new Term(field,wildcard);
		} else{
			field = fields.reverse_title();
			wildcardTerm = new Term(field,StringUtils.reverseString(wildcard));
		}
		
		// get terms
		Term t;
		WildcardTermEnum te = new WildcardTermEnum(reader,wildcardTerm);
		while((t = te.term()) != null){
			if(type == WildcardType.SUFFIX)
				ret.add(StringUtils.reverseString(t.text()));
			else
				ret.add(t.text());
			
			if(!te.next())
				break;
			if(ret.size() >= MAX_TERMS)
				break;
		}
		
		return ret;
	}

}
