package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.FieldSelector;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.MultiSearcherBase;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.Weight;

/** MultiSearcher that can return multiple documents in one method call */
public class MultiSearcherMul extends MultiSearcherBase implements SearchableMul {
	protected CachedDfSource cacheSim;

	public MultiSearcherMul(SearchableMul[] searchables) throws IOException {
		super(searchables);
	}
	
	public Document[] docs(int[] n, FieldSelector sel) throws IOException {
		// searchable -> doc ids 
		int[][] map = new int[searchables.length][n.length];
		// searchable -> number of doc ids
		int[] count = new int[searchables.length];
		// original index (in n) -> searchable
		int[] orderSearcher = new int[n.length];
		// original index (in n) -> document within searchable
		int[] orderDoc = new int[n.length];
		int j=0;
		for(int i : n){
			int si = subSearcher(i);
			int docid = i - starts[si]; // doc id on subsearcher
			orderSearcher[j] = si;
			orderDoc[j++] = count[si];
			map[si][count[si]++] = docid; 
		}
		
		// batch-get 
		Document[][] docs = new Document[searchables.length][n.length];
		for(j=0;j<searchables.length;j++){
			if(count[j]==0)
				continue;
			int[] val = new int[count[j]];
			System.arraycopy( map[j], 0, val, 0, count[j] );
			if(sel == null)
				docs[j] = searchables[j].docs(val);
			else
				docs[j] = searchables[j].docs(val,sel);
		}
		// arrange in original order
		Document[] ret = new Document[n.length];
		for(j=0;j<n.length;j++){
			ret[j] = docs[orderSearcher[j]][orderDoc[j]];
		}
		
		return ret;

	}	
	// inherit javadoc
	public Document[] docs(int[] n) throws IOException {
		return docs(n,null);
	}
	
	  /**
	   * Create weight in multiple index scenario.
	   * 
	   * Distributed query processing is done in the following steps:
	   * 1. rewrite query
	   * 2. extract necessary terms
	   * 3. collect dfs for these terms from the Searchables
	   * 4. create query weight using aggregate dfs.
	   * 5. distribute that weight to Searchables
	   * 6. merge results
	   *
	   * Steps 1-4 are done here, 5+6 in the search() methods
	   *
	   * @return rewritten queries
	   */
	  public Weight createWeight(Query original) throws IOException {
	    // step 1
	    Query rewrittenQuery = rewrite(original);

	    // step 2
	    Set terms = new HashSet();
	    rewrittenQuery.extractTerms(terms);

	    // step3
	    Term[] allTermsArray = new Term[terms.size()];
	    terms.toArray(allTermsArray);
	    int[] aggregatedDfs = new int[terms.size()];
	    for (int i = 0; i < searchables.length; i++) {
	      int[] dfs = searchables[i].docFreqs(allTermsArray);
	      for(int j=0; j<aggregatedDfs.length; j++){
	        aggregatedDfs[j] += dfs[j];
	      }
	    }

	    HashMap dfMap = new HashMap();
	    for(int i=0; i<allTermsArray.length; i++) {
	      dfMap.put(allTermsArray[i], new Integer(aggregatedDfs[i]));
	    }
	    
	    // step4
	    int numDocs = maxDoc();
	    cacheSim = new CachedDfSource(dfMap, numDocs, getSimilarity());

	    return rewrittenQuery.weight(cacheSim);
	  }
	  
	  /** 
	   * Get cached document frequencies from last query. Never use this method
	   * if single instance of multisearcher is shared between threads.
	   * 
	   * @return
	   */
	  public Searcher getLastCachedDfSource(){
		  return cacheSim;
	  }
}
