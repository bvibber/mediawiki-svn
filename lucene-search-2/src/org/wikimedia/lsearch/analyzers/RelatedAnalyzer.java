package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;

import org.apache.lucene.analysis.Token;
import org.wikimedia.lsearch.analyzers.KeywordsAnalyzer.KeywordsTokenStream;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.util.MathFunc;

/**
 * Store related fields, containing related article titles
 * 
 * @author rainman
 *
 */
public class RelatedAnalyzer extends KeywordsAnalyzer {
	/** number of related fields in the index, first has the top-scored, etc, last everything else */
	static public int RELATED_GROUPS = 5;
	
	static public int TOKEN_GAP = 20;

	public RelatedAnalyzer(ArrayList<RelatedTitle> related, int[] p, FilterFactory filters, String prefix, boolean exactCase) {
		this.prefix = prefix;
		this.iid = filters.getIndexId();
		tokensBySize = new KeywordsTokenStream[RELATED_GROUPS];
		if(related == null || p == null){
			// init empty token streams
			for(int i=0; i< RELATED_GROUPS; i++){
				tokensBySize[i] = new KeywordsTokenStream(null,filters,exactCase,RelatedAnalyzer.TOKEN_GAP);			
			}	
			return;
		}
		// split-up
		ArrayList<ArrayList<String>> partitions = new ArrayList<ArrayList<String>>();
		for(int i=0;i<RELATED_GROUPS;i++){
			ArrayList<String> part = new ArrayList<String>();
			for(int j=p[i];j<p[i+1];j++)
				part.add(related.get(j).getRelated().getTitle());
			partitions.add(part);			
		}
		for(int i=0; i< RELATED_GROUPS; i++){
			tokensBySize[i] = new KeywordsTokenStream(partitions.get(i),filters,exactCase,RelatedAnalyzer.TOKEN_GAP);			
		}
	}
}
