package org.apache.lucene.search;

import java.io.IOException;
import java.util.HashSet;
import java.util.Set;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.CustomPhraseQuery.CustomPhraseWeight;
import org.apache.lucene.search.*;

public class CombinedPhraseQuery extends Query {
	QueryOptions options;
	QueryOptions sloppyOptions;
	QueryOptions exactOptions;
	CustomPhraseQuery sloppy, exact;
	HashSet<String> stopWords;
	
	public CombinedPhraseQuery(QueryOptions combinedOptions, QueryOptions sloppyOptions, QueryOptions exactOptions, HashSet<String> stopWords){
		this.options = combinedOptions;
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
		String s = "[combined: "+sloppy.toString(f)+" "+exact.toString(f);
		if(options.stemtitle != null)
			s += ", with "+options.stemtitle;
		if(options.related != null)
			s += ", with "+options.related;
		return s +"]";
	}
	
	protected class CombinedPhraseWeight implements Weight {
		Weight sloppyWeight, exactWeight;
		Weight stemtitleWeight = null, relatedWeight = null;
		Similarity similarity;
		
		CombinedPhraseWeight(Searcher searcher) throws IOException{
			similarity = searcher.getSimilarity();
			sloppyWeight = sloppy.createWeight(searcher);
			exactWeight = exact.createWeight(searcher);
			// init additional weights
			if(options.stemtitle != null)
				stemtitleWeight = options.stemtitle.createWeight(searcher);
			if(options.related != null)
				relatedWeight = options.related.createWeight(searcher);			
		}

		public Explanation explain(IndexReader reader, int doc) throws IOException {
	      Explanation e = new Explanation();
	      e.setDescription("weight(combined["+getQuery()+"] in "+doc+"), product of:");
	      Explanation prod = scorer(reader).explain(doc);
	      if(prod.getDetails().length > 0){
	      	Explanation sum = prod.getDetails()[prod.getDetails().length-1];
	      	Explanation sl = sloppyWeight.explain(reader,doc);
	      	if(sl.isMatch())
	      		sum.addDetail(sl);
	      	Explanation ex = exactWeight.explain(reader,doc);
	      	if(ex.isMatch())
	      		sum.addDetail(ex);
	      }
	      e.addDetail(prod);
	      Explanation b = new Explanation(getBoost(),"boost factor");
	      e.addDetail(b);
	      e.setValue(prod.getValue()*b.getValue());
	      return e;
		}

		public Query getQuery() {
			return CombinedPhraseQuery.this;
		}

		public float getValue() {
			return getBoost();
		}

		public void normalize(float norm) {
			if(stemtitleWeight != null)
				stemtitleWeight.normalize(norm);
			if(relatedWeight != null)
				relatedWeight.normalize(norm);
			sloppyWeight.normalize(norm);
			exactWeight.normalize(norm);
		}

		public Scorer scorer(IndexReader reader) throws IOException {
			// additional scorers
			Scorer stemtitleScorer = null;
			Scorer relatedScorer = null;
			if(stemtitleWeight != null)
				stemtitleScorer = stemtitleWeight.scorer(reader);
			if(relatedWeight != null)
				relatedScorer = relatedWeight.scorer(reader);
			return new CombinedPhraseScorer(this, similarity, sloppyWeight.scorer(reader), exactWeight.scorer(reader), 
					stemtitleScorer, relatedScorer);
		}

		public float sumOfSquaredWeights() throws IOException {			
			if(stemtitleWeight != null)
				stemtitleWeight.sumOfSquaredWeights();
			if(relatedWeight != null)
				relatedWeight.sumOfSquaredWeights();
			return sloppyWeight.sumOfSquaredWeights()
			+ exactWeight.sumOfSquaredWeights();
		}
		
	}
	
	
}
