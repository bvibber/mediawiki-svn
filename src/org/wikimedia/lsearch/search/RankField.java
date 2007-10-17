package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.Collection;
import java.util.WeakHashMap;

import org.apache.log4j.Logger;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexReader.FieldOption;

/**
 * Provide rank information for a document, cached from
 * stored field in the index
 * 
 * @author rainman
 *
 */
public class RankField {
	static Logger log = Logger.getLogger(RankField.class);
	protected static WeakHashMap<IndexReader,RankFieldSource> cache = new WeakHashMap<IndexReader,RankFieldSource>();
	protected static Object lock = new Object();
	
	/** Get a cached field source 
	 * @throws IOException */
	public static RankFieldSource getCachedSource(IndexReader reader) throws IOException{
		synchronized(lock){
			RankFieldSource s = cache.get(reader);
			if(s != null)
				return s;
			else{
				s = new RankFieldSource(reader);
				cache.put(reader,s);
				return s;
			}
		}
	}
	
	static public class RankFieldSource {
		protected int[] ranks = null;
		
		protected RankFieldSource(IndexReader reader) throws IOException{
			Collection fields = reader.getFieldNames(FieldOption.ALL);
			if(!fields.contains("rank"))
				return; // index doesn't have ranking info
			
			log.info("Caching rank source for "+reader.directory());
			int maxdoc = reader.maxDoc();
			ranks = new int[maxdoc];
			for(int i=0;i<maxdoc;i++){
				try{
					ranks[i] = Integer.parseInt(reader.document(i).get("rank"));
				} catch(NumberFormatException e){
					log.error("Error for docid = "+i);
					e.printStackTrace();
				}
			}
			log.info("Finished caching rank source for "+reader.directory());
		}
		
		/** Get number of references to docid (rank) */
		public int get(int docid){
			if(ranks != null)
				return ranks[docid];
			else
				return 0;
		}
	}

}
