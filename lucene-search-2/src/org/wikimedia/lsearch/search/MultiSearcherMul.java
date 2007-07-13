package org.wikimedia.lsearch.search;

import java.io.IOException;

import org.apache.lucene.document.Document;
import org.apache.lucene.search.MultiSearcher;
import org.apache.lucene.search.SearchableMul;

/** MultiSearcher that can return multiple documents in one method call */
public class MultiSearcherMul extends MultiSearcher implements SearchableMul {

	public MultiSearcherMul(SearchableMul[] searchables) throws IOException {
		super(searchables);
	}
	
	// inherit javadoc
	public Document[] docs(int[] n) throws IOException {
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
			docs[j] = searchables[j].docs(val);
		}
		// arrange in original order
		Document[] ret = new Document[n.length];
		for(j=0;j<n.length;j++){
			ret[j] = docs[orderSearcher[j]][orderDoc[j]];
		}
		
		return ret;
	}

}
