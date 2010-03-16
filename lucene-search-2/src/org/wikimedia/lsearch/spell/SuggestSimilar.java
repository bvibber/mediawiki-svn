package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashSet;

import org.apache.lucene.document.Document;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Filter;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TopDocs;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.NamespaceCache;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.spell.SuggestResult.SimilarResult;
import org.wikimedia.lsearch.spell.api.NgramIndexer.Type;
import org.wikimedia.lsearch.spell.dist.EditDistance;

/**
 * Suggest similar titles
 * 
 * @author rainman
 *
 */
public class SuggestSimilar {
	protected static SearcherCache cache;
	protected IndexId iid;
	protected IndexSearcherMul searcher;
	static final int POOL_SIZE = 400;
	
	public SuggestSimilar(IndexId iid) throws IOException {
		this(iid,null);
	}
	
	public SuggestSimilar(IndexId iid, IndexSearcherMul searcher) throws IOException {
		iid = iid.getTitleNgram();
		this.iid = iid;
		if(cache == null)
			cache = SearcherCache.getInstance();
		this.searcher = searcher==null? cache.getLocalSearcher(iid) : searcher;
	}
	
	/** @return ns:title keys of similar titles */
	public ArrayList<String> getSimilarTitles(String title, NamespaceFilter nsf, int maxdist) throws IOException {		
		ArrayList<SimilarResult> results = new ArrayList<SimilarResult>();
		int calls = 0;
		// fetch in main
		if(nsf.isAll() || nsf.contains(0)){
			results.addAll(suggestSimilar(title,null,maxdist));
			calls++;
		}
		// fetch in other if any
		if(nsf.isAll() || nsf.cardinality()>1 || !nsf.contains(0)){
			results.addAll(suggestSimilar(title,nsf,maxdist));
			calls++;
		}
		
		if(calls > 1) // resort
			Collections.sort(results,new SuggestResult.ComparatorForTitles());
		
		// filter out redirects to better-ranked suggestions
		HashSet<String> selected = new HashSet<String>();
		ArrayList<String> keys = new ArrayList<String>();		
		for(SimilarResult r : results){
			if(!selected.contains(r.key) && (r.redirectTo==null || !selected.contains(r.redirectTo))){
				keys.add(r.key);
				selected.add(r.key);
				selected.add(r.redirectTo);
			}
		}
		
		return keys;
	}

	
	protected ArrayList<SimilarResult> suggestSimilar(String title, NamespaceFilter nsf, int maxdist) throws IOException {
		EditDistance ed = new EditDistance(title);
		ArrayList<SimilarResult> res = new ArrayList<SimilarResult>();
		// distance heuristics - maybe should be configurable
		//int dist = Math.min(title.length()/2,maxdist);
		int dist = maxdist;
		
		if(dist == 0) // too short word
			return res; 

		BooleanQuery bq = new BooleanQuery();		
		String field = nsf==null? "title0" : "title";
		bq.add(Suggest.makeQuery(title,field,Type.TITLE_NGRAM),BooleanClause.Occur.SHOULD);
		
		Filter filter = null;
		if(nsf != null)
			filter = NamespaceCache.get(nsf);
		TopDocs docs = searcher.search(bq,filter,POOL_SIZE);			

		// fetch results, calculate various edit distances
		for(ScoreDoc sc : docs.scoreDocs){		
			Document d = searcher.doc(sc.doc);
			String w = d.get(field);			
			int editDist = ed.getDistance(w);
			if(editDist <= dist){
				String key = d.get("key");
				String redirectTo = d.get("redirect");				
				int rank = Integer.parseInt(d.get("rank"));
				res.add(new SimilarResult(w,rank,editDist,key,redirectTo));
			}
		}
		// sort
		Collections.sort(res,new SuggestResult.ComparatorForTitles());
		return res;
	}
}
