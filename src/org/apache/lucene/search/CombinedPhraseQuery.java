package org.apache.lucene.search;

import java.io.IOException;
import java.util.HashSet;
import java.util.Set;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.CustomPhraseQuery.CustomPhraseWeight;
import org.apache.lucene.search.*;

public class CombinedPhraseQuery extends Query {
	QueryOptions sloppyOptions;
	QueryOptions exactOptions;
	CustomPhraseQuery sloppy, exact;
	HashSet<String> stopWords;
	
	public CombinedPhraseQuery(QueryOptions sloppyOptions, QueryOptions exactOptions, HashSet<String> stopWords){
		this.sloppyOptions = sloppyOptions;
		this.exactOptions = exactOptions;
		this.stopWords = stopWords;
		this.exact = new CustomPhraseQuery(exactOptions);
		this.sloppy = new CustomPhraseQuery(sloppyOptions);
	}

	public void add(Term term, int position) {
		// don't add stop words to sloppy query
		if(!stopWords.contains(term.text()))
			sloppy.add(term,position);
		exact.add(term,position);
	}

	public void add(Term term) {
		exact.add(term);		
		if(!stopWords.contains(term.text()))
			sloppy.add(term,(Integer)exact.positions.lastElement()); // maintain gaps
		
	}

	@Override
	protected Weight createWeight(Searcher searcher) throws IOException {
		return new CombinedPhraseWeight(searcher);
	}

	@Override
	public void extractTerms(Set queryTerms) {
		exact.extractTerms(queryTerms);
		sloppy.extractTerms(queryTerms);
	}

	public int getSlop() {
		return sloppy.getSlop();
	}
	
	public void setSlop(int s) {
		sloppy.setSlop(s);
	}
	
	@Override
	public String toString(String f) {
		return "[combined: "+sloppy.toString(f)+" "+exact.toString(f)+"]";
	}
	
	protected class CombinedPhraseWeight implements Weight {
		Weight sloppyWeight, exactWeight;
		Weight stemtitleWeight = null, relatedWeight = null;
		Similarity similarity;
		
		CombinedPhraseWeight(Searcher searcher) throws IOException{
			similarity = searcher.getSimilarity();
			sloppyWeight = sloppy.createWeight(searcher);
			exactWeight = exact.createWeight(searcher);
		}

		public Explanation explain(IndexReader reader, int doc) throws IOException {
	      Explanation e = new Explanation();
	      e.setDescription("weight(combined["+getQuery()+"] in "+doc+"), product of:");
	      Explanation sum = scorer(reader).explain(doc);
	      Explanation sl = sloppyWeight.explain(reader,doc);
	      if(sl.isMatch())
	      	sum.addDetail(sl);
	      Explanation ex = exactWeight.explain(reader,doc);
	      if(ex.isMatch())
	      	sum.addDetail(ex);
	      e.addDetail(sum);
	      Explanation b = new Explanation(getBoost(),"boost factor");
	      e.addDetail(b);
	      e.setValue(sum.getValue()*b.getValue());
	      return e;
		}

		public Query getQuery() {
			return CombinedPhraseQuery.this;
		}

		public float getValue() {
			return getBoost();
		}

		public void normalize(float norm) {
			sloppyWeight.normalize(norm);
			exactWeight.normalize(norm);
		}

		public Scorer scorer(IndexReader reader) throws IOException {
			return new CombinedPhraseScorer(this, similarity, sloppyWeight.scorer(reader), exactWeight.scorer(reader));
		}

		public float sumOfSquaredWeights() throws IOException {
			return sloppyWeight.sumOfSquaredWeights()
			+ exactWeight.sumOfSquaredWeights();
		}
		
	}
	
	
}
