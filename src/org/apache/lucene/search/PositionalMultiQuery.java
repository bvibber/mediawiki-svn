package org.apache.lucene.search;

import java.io.IOException;
import java.util.Arrays;
import java.util.Iterator;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.MultipleTermPositions;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermPositions;

/**
 * MultiPhraseQuery with positional info
 * 
 * @author rainman
 *
 */
public class PositionalMultiQuery extends MultiPhraseQuery {
	protected PositionalOptions options; 
	protected int stopWordCount = 0;

	public PositionalMultiQuery(PositionalOptions options){
		this.options = options;
	}

	/** Add to end of phrase */
	public void add(Term term, boolean isStopWord) {
		if(isStopWord)
			stopWordCount++;
		add(term);
	}
	/** Add to position in phrase */
	public void add(Term term, int position, boolean isStopWord) {
		if(isStopWord)
			stopWordCount++;
		add(new Term[]{term},position);
	}

	public String toString(String f) {
		String s = super.toString(f);
		return "(P "+s+")";
	}
	
	protected Weight createWeight(Searcher searcher) throws IOException {
		return new PositionalMultiWeight(searcher);
	}
	/** 
	 * Weight 
	 * 
	 * @author rainman
	 *
	 */
	protected class PositionalMultiWeight extends MultiPhraseWeight {
		public PositionalMultiWeight(Searcher searcher) throws IOException {
			super(searcher);
			this.similarity = getSimilarity(searcher);
			this.idf = 0;
	      // compute idf - take average when multiple terms
	      Iterator i = termArrays.iterator();
	      while (i.hasNext()) {
	        Term[] terms = (Term[])i.next();
	        float av = 0;
	        for (int j=0; j<terms.length; j++) {
	          av += getSimilarity(searcher).idf(terms[j], searcher);
	        }
	        idf += av / terms.length;
	      }
		}

		public Scorer scorer(IndexReader reader) throws IOException {
			if (termArrays.size() == 0)                  // optimize zero-term case
				return null;

			TermPositions[] tps = new TermPositions[termArrays.size()];
			for (int i=0; i<tps.length; i++) {
				Term[] terms = (Term[])termArrays.get(i);

				TermPositions p;
				if (terms.length > 1)
					p = new MultipleTermPositions(reader, terms);
				else
					p = reader.termPositions(terms[0]);

				if (p == null)
					return null;

				tps[i] = p;
			}

			// init aggregate meta field if any
			if(options.aggregateMeta != null)
				options.aggregateMeta.init(reader,field);

			if(options.rankMeta != null)
				options.rankMeta.init(reader,field);

			if( tps.length == 1)
				return new PositionalScorer.TermScorer(this, tps, getPositions(), stopWordCount, 
						similarity,reader.norms(field), options);
			else if( slop == 0 )				 
				return new PositionalScorer.ExactScorer(this, tps, getPositions(), stopWordCount,
						similarity,	reader.norms(field), options);
			else
				return new PositionalScorer.SloppyScorer(this, tps, getPositions(), stopWordCount, 
						similarity, slop,	reader.norms(field), options);
		}

		public Explanation explain(IndexReader reader, int doc) throws IOException {
			ComplexExplanation result = new ComplexExplanation();
			result.setDescription("weight("+getQuery()+" in "+doc+"), product of:");

			Explanation idfExpl = new Explanation(idf, "idf("+getQuery()+")");

			// explain query weight
			Explanation queryExpl = new Explanation();
			queryExpl.setDescription("queryWeight(" + getQuery() + "), product of:");

			Explanation boostExpl = new Explanation(getBoost(), "boost");
			if (getBoost() != 1.0f)
				queryExpl.addDetail(boostExpl);

			queryExpl.addDetail(idfExpl);

			Explanation queryNormExpl = new Explanation(queryNorm,"queryNorm");
			queryExpl.addDetail(queryNormExpl);

			queryExpl.setValue(boostExpl.getValue() *
					idfExpl.getValue() *
					queryNormExpl.getValue());

			result.addDetail(queryExpl);

			// explain field weight
			ComplexExplanation fieldExpl = new ComplexExplanation();
			fieldExpl.setDescription("fieldWeight("+getQuery()+" in "+doc+
			"), product of:");

			Explanation tfExpl = scorer(reader).explain(doc);
			fieldExpl.addDetail(tfExpl);
			fieldExpl.addDetail(idfExpl);

			Explanation fieldNormExpl = new Explanation();
			float fieldNorm = 1; // NO NORMS
			fieldNormExpl.setValue(fieldNorm);
			fieldNormExpl.setDescription("fieldNorm(field="+field+", doc="+doc+")");
			fieldExpl.addDetail(fieldNormExpl);

			fieldExpl.setMatch(Boolean.valueOf(tfExpl.isMatch()));
			fieldExpl.setValue(tfExpl.getValue() *
					idfExpl.getValue() *
					fieldNormExpl.getValue());

			result.addDetail(fieldExpl);
			result.setMatch(fieldExpl.getMatch());

			// combine them
			result.setValue(queryExpl.getValue() * fieldExpl.getValue());

			if (queryExpl.getValue() == 1.0f)
				return fieldExpl;

			return result;
		}

	}
	
	public Query rewrite(IndexReader reader) {
	    if (termArrays.size() == 1) {                 // optimize one-term case
	      Term[] terms = (Term[])termArrays.get(0);
	      if(terms.length == 1){
	      	PositionalQuery pq = new PositionalQuery(options);
	      	pq.add(terms[0]);
	      	pq.setBoost(getBoost());
	      	return pq;
	      } else{
	      	BooleanQuery boq = new BooleanQuery(true);
	      	for (int i=0; i<terms.length; i++) {
	      		PositionalQuery pq = new PositionalQuery(options);
	      		pq.add(terms[i]);	      	
	      		boq.add(pq, BooleanClause.Occur.SHOULD);
	      	}
	      	boq.setBoost(getBoost());
	      	return boq;
	      }
	    } else {
	      return this;
	    }
	  }
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = super.hashCode();
		result = PRIME * result + ((options == null) ? 0 : options.hashCode());
		result = PRIME * result + stopWordCount;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (!super.equals(obj))
			return false;
		if (getClass() != obj.getClass())
			return false;
		final PositionalMultiQuery other = (PositionalMultiQuery) obj;
		if (options == null) {
			if (other.options != null)
				return false;
		} else if (!options.equals(other.options))
			return false;
		if (stopWordCount != other.stopWordCount)
			return false;
		return true;
	}
	
	
	
	
}
