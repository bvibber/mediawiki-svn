package org.wikimedia.lsearch.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.storage.RelatedStorage;
/**
 * Provide scoring for a custom related map
 * 
 * @author rainman
 *
 */
public class RelatedMap {
	static Logger log = Logger.getLogger(RelatedMap.class);
	protected int[] indexes;
	protected int[] docids;
	protected float[] related;
	protected IndexId iid;
	protected final int report = 5000;
	protected String[] keys;
	protected int[] ranks;
	
	public static final float WEIGHT = 1f;
	
	protected static Hashtable<String,RelatedMap> scorers = new Hashtable<String,RelatedMap>();

	/** Get a shared Scorer instances 
	 * @throws IOException */
	static public RelatedMap getMap(IndexId iid) throws IOException{
		synchronized(iid){
			RelatedMap sc = scorers.get(iid.toString());
			if(sc != null)
				return sc;
			else{
				sc = new RelatedMap(iid);
				scorers.put(iid.toString(),sc);
				return sc;
			}
		}
	}
	
	/** Create new scorer for iid 
	 * @throws IOException */
	private RelatedMap(IndexId iid) throws IOException {		
		this.iid = iid;
		RelatedStorage st = new RelatedStorage(iid);
		
		IndexReader ir = SearcherCache.getInstance().getLocalSearcher(iid).getIndexReader();
		int maxdoc = ir.maxDoc();
		indexes = new int[maxdoc];
		keys = new String[maxdoc];
		ranks = new int[maxdoc];
		int size = 0;
		HashMap<String,Integer> cache = new HashMap<String,Integer>();
		log.info("Making cache...");
		for(int i=0;i<maxdoc;i++){			
			Document d = ir.document(i);
			String key = d.get("namespace")+":"+d.get("title");
			cache.put(key,i);
			indexes[i] = size;
			keys[i] = key;
			ranks[i] = Integer.parseInt(d.get("rank"));
			size += st.getRelated(key).size();
			if((i+1) % report == 0)
				log.info("Cached "+(i+1)+" entries");
		}
		// make the mapping
		docids = new int[size];
		related = new float[size];
		int bad=0;
		log.info("Making related mapping..");
		for(int i=0;i<maxdoc;i++){
			Document d = ir.document(i);
			String key = d.get("namespace")+":"+d.get("title");
			ArrayList<RelatedTitle> rel = st.getRelated(key);
			int offset = indexes[i];
			for(RelatedTitle t : rel){
				String rk = t.getRelated().getKey();
				Integer docid = cache.get(rk); 
				if(docid == null) bad++;
				docids[offset] = docid == null? -1 : docid;
				related[offset] = transformScore(t.getScore());
				offset++;
			}
			if((i+1) % report == 0)
				log.info("Processed "+(i+1));
		}
		log.info("Finished, got "+size+" related entries ("+bad+" bad)");
	}
	
	protected float transformScore(double score){
		return (float)Math.log10(1+9*score);
	}
	
	public float calcScore(int docid, float[] scores, float[] additional){
		float sc = 0;
		if(docid<0)
			throw new IllegalArgumentException("docid cannot be negative (docid="+docid+")");
		if(docid>docids.length)
			return scores[docid];
		
		sc = scores[docid];
		int start = indexes[docid];
		int end = (docid == indexes.length-1)? indexes.length : indexes[docid+1];
		//System.out.println("SCORE for ["+keys[docid]+"] base: "+sc);
		double max = 0;
		String maxexp = "";
		for(int i=start;i<end;i++){
			int d = docids[i]; // docid of related article
			if(d == -1 || scores[d]==0)
				continue;
			//String explain ="["+WEIGHT+" * "+scores[d]+" * "+related[i]+" ("+keys[d]+")]"; 
			//double s = WEIGHT * related[i] * scores[d];
			double s = sc * WEIGHT * related[i] / ranks[docid];
			additional[d] += s;
			System.out.println("ADD ["+keys[d]+"] "+s);
			
			/*if(s > max){
				max = s;
				maxexp = explain;
			} */
		}
		//sc += max;
		//System.out.println("FINAL SCORE for ["+keys[docid]+"] : "+sc+" via "+maxexp);
		//System.out.println();
		
		return sc;		
	}
	
	public float getFinalScore(int docid, float[] scores, float[] additional){
		return scores[docid]+additional[docid];
	}
}
