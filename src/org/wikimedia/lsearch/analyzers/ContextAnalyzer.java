package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;

import org.wikimedia.lsearch.analyzers.KeywordsAnalyzer.KeywordsTokenStream;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.related.RelatedTitle;

/**
 * Contexts tokenized, with token gaps 
 * 
 * @author rainman
 *
 */
public class ContextAnalyzer extends KeywordsAnalyzer {
	static public int CONTEXT_GROUPS = 2;
	
	static public int TOKEN_GAP = 100;

	public ContextAnalyzer(Title title, Links links, ArrayList<RelatedTitle> related, int[] p, FilterFactory filters, String prefix, boolean exactCase) {
		this.prefix = prefix;
		this.iid = filters.getIndexId();
		tokensBySize = new KeywordsTokenStream[CONTEXT_GROUPS];
		if(related == null || p == null || title == null || links == null){
			// init empty token streams
			for(int i=0; i< CONTEXT_GROUPS; i++){
				tokensBySize[i] = new KeywordsTokenStream(null,filters,exactCase,TOKEN_GAP);			
			}	
			return;
		}
		String key = title.getKey();
		// split-up
		ArrayList<ArrayList<String>> partitions = new ArrayList<ArrayList<String>>();
		for(int i=0;i<CONTEXT_GROUPS;i++){
			ArrayList<String> part = new ArrayList<String>();
			for(int j=p[i];j<p[i+1];j++){
				Title t = related.get(j).getRelated();
				Collection<String> contexts;
				try {
					contexts = links.getContext(t.getKey(),key);
					//System.out.println("CONTEXT "+t.getKey()+" -> "+key+" : "+contexts);
					if(contexts != null)
						part.addAll(contexts);
				} catch (IOException e) {
					log.warn("Cannot fetch context for "+key+" from "+t.getKey()+" : "+e.getMessage());
					e.printStackTrace();
				}
				
			}
			partitions.add(part);			
		}
		for(int i=0; i< CONTEXT_GROUPS; i++){
			tokensBySize[i] = new KeywordsTokenStream(partitions.get(i),filters,exactCase,TOKEN_GAP);			
		}
	}

}
