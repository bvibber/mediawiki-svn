package org.wikimedia.lsearch.search;

import org.apache.lucene.search.HitCollector;
import org.apache.lucene.search.TopDocCollector;
import org.apache.lucene.search.TopDocs;

public class RelatedHitCollector extends HitCollector {
	protected float[] scores;
	protected float[] additional;
	protected TopDocCollector topdoc;	
	protected RelatedMap map;
	
	public RelatedHitCollector(RelatedMap map, int maxDoc, int numHits){
		this.map = map;
		scores = new float[maxDoc];
		additional = new float[maxDoc];
		topdoc = new TopDocCollector(numHits);
	}
	@Override
	public void collect(int doc, float score) {
		scores[doc] = score;		
	}
	
	public TopDocs getTopDocs(){
		for(int i=0;i<scores.length;i++){
			if(scores[i] == 0)
				continue;
			map.calcScore(i,scores,additional);
		}
		// collect top
		for(int i=0;i<additional.length;i++){
			if(scores[i] == 0)
				continue;
			topdoc.collect(i,map.getFinalScore(i,scores,additional));
		}
		return topdoc.topDocs();
	}

}
