package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.FieldSelector;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.Explanation;
import org.apache.lucene.search.Filter;
import org.apache.lucene.search.HitCollector;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.Sort;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.search.TopFieldDocs;
import org.apache.lucene.search.Weight;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;

/**
 * A generalized index searcher, supports various index
 * architectures.
 * 
 * Depending on the structure of the index (single file, 
 * mainsplit, split) makes either a local 
 * <code>IndexSearcher</code>, or a <code>MultiSearcher</code> 
 * with <code>SearchableMul</code> objects distributed via RMI.
 * 
 * The local searchers are always preferred (groups are ignored
 * for local searchers to avoid ambiguity).
 * 
 * 
 *  The actual searchers are pulled from {@link SearcherCache}.
 * 
 * @author rainman
 *
 */
public class WikiSearcher extends Searcher implements SearchableMul {
	static org.apache.log4j.Logger log = Logger.getLogger(WikiSearcher.class);
	protected SearchableMul searcher;
	protected SearcherCache cache;
	/** parts of the multisearcher, dbrole -> searchable */
	protected Hashtable<String,Searchable> searcherParts = new Hashtable<String,Searchable>();
	protected MultiSearcherMul ms = null;
	protected Searcher cachedDfs = null;
	/** searchable -> host */
	protected Hashtable<Searchable,String> searchableHost = new Hashtable<Searchable,String>();
	
	public static final boolean INVALIDATE_CACHE = true;
	
	protected MultiSearcherMul makeMultiSearcher(Collection<IndexId> iids) throws IOException{
		ArrayList<Searchable> ss = new ArrayList<Searchable>();
		for(IndexId iid : iids){
			Searchable s = null;
			String host = cache.getRandomHost(iid);
			if(host != null){
				if(RMIMessengerClient.isLocal(host))
					s = cache.getLocalSearcher(iid);
				else
					s = cache.getRemoteSearcher(iid,host);
			}
						
			if(s != null){
				ss.add(s);
				searcherParts.put(iid.toString(),s);
				searchableHost.put(s,host);
			} else
				log.warn("Cannot get a search index (nor local or remote) for "+iid);				
		}
		if(ss.size() == 0)
			return null;
		return new MultiSearcherMul(ss.toArray(new SearchableMul[]{}));
		
	}
	
	/** New object from cache */
	public WikiSearcher(IndexId iid) throws IOException {
		cache = SearcherCache.getInstance();
				
		if(iid.isSingle()){ // is always local 
			searcher = cache.getLocalSearcher(iid);
			searcherParts.put(iid.toString(),searcher);
		} else {
			ArrayList<IndexId> parts = iid.getPhysicalIndexIds();
			searcher = ms = makeMultiSearcher(parts);
		}
		
		if(searcher == null)
			throw new IOException("Error constructing searcher for "+iid);		
	}
	
	/** New object only with required parts */
	public WikiSearcher(Collection<IndexId> parts) throws IOException {
		cache = SearcherCache.getInstance();
		searcher = ms = makeMultiSearcher(parts);
		if(searcher == null)
			throw new IOException("Error constructing searcher for "+parts);
	}

	/** Got host for the iid within this multi searcher */
	public String getHost(IndexId iid){
		Searchable s = searcherParts.get(iid.toString());
		if(s == null)
			return null;
		else
			return searchableHost.get(s);
	}
	
	/** Get map iid->host for of all parts in this searcher */
	public HashMap<String,String> getAllHosts(){
		HashMap<String,String> ret = new HashMap<String,String>();
		for(Entry<String,Searchable> e : searcherParts.entrySet()){
			ret.put(e.getKey(),searchableHost.get(e.getValue()));
		}
		return ret;
	}

	@Override
	public void close() throws IOException {
	}

	@Override
	public Document doc(int i) throws IOException {
		return searcher.doc(i);
	}

	/** Note: for split indexes, the value is always cached (from last query) */
	@Override
	public int docFreq(Term term) throws IOException {
		if(ms != null)
			return ms.getLastCachedDfSource().docFreq(term);
		else
			return searcher.docFreq(term);
	}
	
	/** Note: for split indexes, these values are always cached (from last query) */
	@Override
	public int[] docFreqs(Term[] terms) throws IOException {
		if(ms != null)
			return ms.getLastCachedDfSource().docFreqs(terms);
		else
			return searcher.docFreqs(terms);
	}

	@Override
	public Explanation explain(Weight weight, int doc) throws IOException {
		return searcher.explain(weight,doc);
	}

	@Override
	public int maxDoc() throws IOException {
		return searcher.maxDoc();
	}

	@Override
	public Query rewrite(Query query) throws IOException {
		return searcher.rewrite(query);
	}

	@Override
	public void search(Weight weight, Filter filter, HitCollector results)
			throws IOException {
		searcher.search(weight,filter,results);
	}

	@Override
	public TopDocs search(Weight weight, Filter filter, int n)
			throws IOException {
		return searcher.search(weight,filter,n);
	}

	@Override
	public TopFieldDocs search(Weight weight, Filter filter, int n, Sort sort)
			throws IOException {
		return searcher.search(weight,filter,n,sort);
	}

	public Document[] docs(int[] i) throws IOException {
		return searcher.docs(i);
	}

	@Override
	public Weight createWeight(Query query) throws IOException {
		if(ms == null)
			return super.createWeight(query);
		else
			return ms.createWeight(query);
	}

	@Override
	public String toString() {
		if(ms != null)
			return Arrays.toString(ms.getSearchables());
		else 
			return searcher.toString();
	}

	public Document[] docs(int[] i, FieldSelector sel) throws IOException {
		return searcher.docs(i,sel);
	}

	public Document doc(int i, FieldSelector sel) throws CorruptIndexException, IOException {
		return searcher.doc(i,sel);
	}
	
	
}
