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
 * Wildcard-search related functions & cache
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
	
	/** Get all the words that correspond to wildcard */
	public ArrayList<String> getWords(String wildcard){
		HashSet<String> terms = getCached(wildcard);
		ArrayList<String> words = new ArrayList<String>();
		words.addAll(terms);
		return words;
	}
	
	/** 
	 * Make a DisjunctionMaxQuery of expanded wildcard
	 * 
	 * @param wildcard
	 * @param field
	 * @return null if there is no match, or on error
	 */
	public Query makeQuery(String wildcard, String field){		
		HashSet<String> terms = getCached(wildcard);
		if(terms.size() == 0)
			return null; // no match or error
				
		return makeQueryFromTerms(terms,field);
	}
	
	/** Make terms array for phrases */
	public Term[] makeTerms(String wildcard, String field){
		HashSet<String> terms = getCached(wildcard);
		if(terms.size() == 0)
			return null; // no match or error
		
		trimTerms(terms);
		Term[] ret = new Term[terms.size()];
		int i = 0;
		for(String w : terms)
			ret[i++] = new Term(field,w);
		return ret;
		
	}
	
	protected HashSet<String> getCached(String wildcard){
		if(client == null)
			client = new RMIMessengerClient();

		HashSet<String> terms = wildcardCache.get(wildcard);
		if(terms == null){
			if(wildcardCache.size() >= MAX_PATTERNS_PER_QUERY){
				return new HashSet<String>(); // limit number of wildcard queries
			}
			terms = new HashSet<String>();		
			for(Entry<String,String> e : hosts.entrySet()){
				try {
					terms.addAll(client.getTerms(e.getValue(),e.getKey(),wildcard,exactCase));
				} catch (RemoteException e1) {
					e1.printStackTrace();
					log.warn("Cannot get terms for "+wildcard+" on host "+e.getValue()+" for "+e.getKey(),e1);
				}
			}
			wildcardCache.put(wildcard,terms);
			log.info("Using "+terms.size()+" terms for pattern="+wildcard);
		}
		return terms;
	}
	
	/** Construct DijunctionMaxQuery from terms */
	protected Query makeQueryFromTerms(HashSet<String> terms, String field){
		trimTerms(terms);

		DisjunctionMaxQuery q = new DisjunctionMaxQuery(0);
		for(String t : terms){
			q.add(new TermQuery(new Term(field,t)));
		}
		return q;		
	}
	
	private void trimTerms(HashSet<String> terms) {
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
		boolean pre = wildcard.endsWith("*") || wildcard.endsWith("?");
		boolean suff = wildcard.startsWith("*") || wildcard.startsWith("?");		
		int preInx = last(wildcard.lastIndexOf("*"),wildcard.lastIndexOf("?"));
		int sufInx = first(wildcard.indexOf("*"),wildcard.indexOf("?"));
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
	
	/** Get terms from a local searcher if available */
	public static ArrayList<String> getLocalTerms(IndexId iid, String wildcard, boolean exactCase) throws IOException {
		if(searcherCache == null)
			searcherCache = SearcherCache.getInstance();
		HashSet<String> ret = new HashSet<String>();
		// check type of wildcard
		WildcardType type = getType(wildcard);
		if(type == WildcardType.INVALID)
			return new ArrayList<String>();
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
		addTerms(ret,wildcardTerm,reader,type);
		
		// fetch additional terms from contents if we are not overflowing limits
		if(type == WildcardType.PREFIX && ret.size() < MAX_TERMS){
			field = fields.contents();
			wildcardTerm = new Term(field,wildcard);
			addTerms(ret,wildcardTerm,reader,type);
		}
		
		ArrayList<String> list = new ArrayList<String>();
		list.addAll(ret);
		return list;
	}
	
	/** Fetch terms matching a wildcard pattern into the target collection */
	protected static void addTerms(Collection<String> ret, Term wildcardTerm, IndexReader reader, WildcardType type) throws IOException{
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
	}

}
