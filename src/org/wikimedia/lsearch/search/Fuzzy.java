package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.rmi.RemoteException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.DisjunctionMaxQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.SuggestResult;
import org.wikimedia.lsearch.spell.dist.EditDistance;

/**
 * Generates fuzzy queries and maintains cache of fuzzy terms.
 * 
 * @author rainman
 *
 */
public class Fuzzy {
	static Logger log = Logger.getLogger(Fuzzy.class); 
	public static final int MAX_PER_QUERY = 8;
	protected IndexId iid;
	protected String host;
	protected RMIMessengerClient client=null;
	/** key (word:nsf) -> word -> boost (f(edit_dist)) */
	protected HashMap<String,HashMap<String,Float>> cache = new HashMap<String,HashMap<String,Float>>(); 
	
	public Fuzzy(IndexId iid, String host) {
		this.iid = iid.getSpell();
		this.host = host;
	}
	
	/** key used to cache fuzzy results */
	protected String cacheKey(String word, NamespaceFilter nsf){
		if(nsf == null)
			return word+":";
		else
			return word+":"+nsf;
	}
	
	public ArrayList<String> getWords(String word, NamespaceFilter nsf){
		HashMap<String,Float> terms = getCached(word,nsf);
		ArrayList<String> words = new ArrayList<String>();
		words.addAll(terms.keySet());
		return words;

	}
	
	public ArrayList<Float> getBoosts(String word, NamespaceFilter nsf, Term[] tt){
		ArrayList<Float> boost = new ArrayList<Float>();
		HashMap<String,Float> terms = getCached(word,nsf);
		for(Term t : tt)
			boost.add(terms.get(t.text()));
		return boost;
	}
	
	public ArrayList<Float> getBoosts(String word, NamespaceFilter nsf, ArrayList<String> words){
		ArrayList<Float> boost = new ArrayList<Float>();
		HashMap<String,Float> terms = getCached(word,nsf);
		for(String w : words)
			boost.add(terms.get(w));
		return boost;
	}

	/** Front-end function: makes a fuzzy query for word in some namespace subset */
	public Query makeQuery(String word, String field, NamespaceFilter nsf) {
		if(client == null)
			client = new RMIMessengerClient();
		
		// get terms
		HashMap<String,Float> terms = getCached(word,nsf);
		
		if(terms.size() == 0)
			return null; // no match or error
		
		// actually make query
		return makeQueryFromTerms(terms, field);
	}
	/** Make a term array without boost */
	public Term[] makeTerms(String word, String field, NamespaceFilter nsf){
		if(client == null)
			client = new RMIMessengerClient();
		HashMap<String,Float> terms = getCached(word,nsf);
		if(terms.size() == 0)
			return null;
		
		Term[] ret = new Term[terms.size()];
		int i=0;
		for(String w : terms.keySet())
			ret[i++] = new Term(field,w);
		return ret;
	}
	
	protected HashMap<String,Float> getCached(String word, NamespaceFilter nsf){
		String key = cacheKey(word,nsf);
		HashMap<String,Float> terms = cache.get(key);
		if(terms == null){
			if(cache.size() >= MAX_PER_QUERY){
				return null; // limit number of wildcard queries
			}
			ArrayList<SuggestResult> res = client.getFuzzy(host,iid.toString(),word,nsf);
			terms = new HashMap<String,Float>();
			if(res != null){
				for(SuggestResult r : res){
					terms.put(r.getWord(),getBoost(r));
				}
			}
			cache.put(key,terms);
		}
		return terms;
	}
	
	/** Calculate boost factor for suggest result - larger edit distance = smaller boost */
	protected float getBoost(SuggestResult r){
		int dist = r.getDist()+r.getDistMetaphone();
		double d = r.getDist();
		double l = r.getWord().length();
		// 2^(-dist) * len_prop * 2^E(dist)
		return (float)((1.0/Math.pow(2,dist))*((l-d)/l)*4);	
	}

	private Query makeQueryFromTerms(HashMap<String,Float> terms, String field) {
		DisjunctionMaxQuery q = new DisjunctionMaxQuery(0);
		for(Entry<String,Float> e : terms.entrySet()){
			TermQuery tq = new TermQuery(new Term(field,e.getKey()));
			tq.setBoost(e.getValue());
			q.add(tq);
		}
		return q;		
	}
	
	public boolean hasFuzzy(){
		return cache.size() > 0;
	}
}
