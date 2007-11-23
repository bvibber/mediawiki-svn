package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.TermPositions;
import org.apache.lucene.search.CombinedPhraseQuery.CombinedPhraseWeight;

/**
 * Combine two phrase scorers, one with exact match 
 * (with all the stop words), and other with a sloppy
 * matcher (without stop words)
 * 
 * @author rainman
 *
 */
public class CombinedPhraseScorer extends Scorer {
	protected CustomPhraseScorer exact;
	protected CustomPhraseScorer sloppy;
	protected Scorer stemtitle, related;
	protected float boost; 
	protected boolean firstTime = true;
	
   public CombinedPhraseScorer(CombinedPhraseWeight weight, Similarity similarity, Scorer sloppy, Scorer exact, 
   		Scorer stemtitle, Scorer related) {
		super(similarity);
		this.boost = weight.getValue();
		this.sloppy = (CustomPhraseScorer) sloppy;
		this.exact = (CustomPhraseScorer) exact;
		this.stemtitle = stemtitle;
		this.related = related;
	}

	@Override
	public int doc() {
		return sloppy.doc();
	}

	@Override
	public Explanation explain(int doc) throws IOException {
		while (next() && doc() < doc) {}
		
		float sloppyScore = 0;
		float exactScore = 0;
		if(doc() == doc){
			sloppyScore = sloppy.score();
			exactScore = sloppy.doc() == exact.doc()? exact.score() : 0;
		}
		float score = 0.2f*sloppyScore+0.3f*exactScore;
		
		Explanation e = new Explanation(score,"Product of:");					
		
		if(stemtitle != null && stemtitle.doc() == doc){
			Explanation eTitle = new Explanation();
			eTitle.setValue(1+stemtitle.score());
			eTitle.setDescription("Stemtitle (raw score="+stemtitle.score()+")");
			System.out.println(eTitle);
			e.addDetail(eTitle);
			e.setValue(e.getValue()*(1+stemtitle.score()));
		}
		if(related != null && related.doc() == doc){
			Explanation eRelated = new Explanation();			
			eRelated.setValue(1+related.score());
			eRelated.setDescription("Related (raw score="+related.score()+")");
			e.addDetail(eRelated);
			e.setValue(e.getValue()*(1+related.score()));
		}
		
		Explanation sum = new Explanation(0.2f*sloppyScore+0.3f*exactScore,"combined score,weighted (0.2,0.3) sum of:");
		e.addDetail(sum);
		
		return e;
	}

	@Override
	public boolean next() throws IOException {
		// documents that match exact phase are subset of those matching sloppy!
		boolean hasNext = false;
		if(firstTime){
			firstTime = false;
			hasNext = sloppy.next();
			exact.next();
			if(stemtitle != null)
				stemtitle.next();
			if(related != null)
				related.next();			
		} else{
			hasNext = sloppy.next();
		}
		
		if(hasNext){
			if(exact.doc() < sloppy.doc())
				exact.skipTo(sloppy.doc());
			if(stemtitle!=null && stemtitle.doc() < doc())
				stemtitle.skipTo(doc());
			if(related!=null && related.doc() < doc())
				related.skipTo(doc());
		}
		return hasNext;
	}

	@Override
	public float score() throws IOException {
		float score = 0.2f*sloppy.score();
		if(exact.doc() == sloppy.doc())
			score += 0.3f*exact.score();
		float sc = score * boost;
		if(stemtitle != null && stemtitle.doc() == doc()){
			sc *= 1 + stemtitle.score();
		}
		if(related != null && related.doc() == doc()){			
			sc *= 1 + related.score();
		}
		return sc;
	}

	@Override
	public boolean skipTo(int target) throws IOException {
		if(firstTime){
			firstTime = false;
			boolean succ = sloppy.skipTo(target);
			exact.skipTo(target);
			if(stemtitle != null)
				stemtitle.skipTo(target);
			if(related != null)
				related.skipTo(target);
			return succ;
		} else{
			boolean succ =	sloppy.skipTo(target);
			if(exact.doc() < target)				
				exact.skipTo(target);
			if(stemtitle!=null && stemtitle.doc() < target)
				stemtitle.skipTo(target);
			if(related!=null && related.doc() < target)
				related.skipTo(target);
			return succ;
		}
	}

}
