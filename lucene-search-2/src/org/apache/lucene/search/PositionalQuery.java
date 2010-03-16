package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermPositions;

/**
 * Phrase query with 
 * 1) extra boost for different position ranges within the document 
 * 2) ability to use aggregate positional information if available 
 * 
 * @author rainman
 *
 */
public class PositionalQuery extends PhraseQuery {
	protected PositionalOptions options; 
	protected int stopWordCount = 0;
	
	public PositionalQuery(PositionalOptions options){
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
		add(term,position);
	}
	
	public String toString(String f) {
		String s = super.toString(f);
		return "(P "+s+")";
	}
	
	protected Weight createWeight(Searcher searcher) throws IOException {
		return new PositionalWeight(searcher);
	}
	
	/**
	 * Weight
	 * 
	 * @author rainman
	 *
	 */
	protected class PositionalWeight extends PhraseWeight{		
		public PositionalWeight(Searcher searcher) throws IOException{
			super(searcher);
		}
		
		public Scorer scorer(IndexReader reader) throws IOException {
			if (terms.size() == 0)			  // optimize zero-term case
				return null;
						
			TermPositions[] tps = new TermPositions[terms.size()];
			for (int i = 0; i < terms.size(); i++) {
				TermPositions p = reader.termPositions((Term)terms.elementAt(i));
				if (p == null)
					return null;
				tps[i] = p;
			}			
			
			// init aggregate meta field if any
			if(options.aggregateMeta != null)
				options.aggregateMeta.init(reader,field);
			
			if(options.rankMeta != null)
				options.rankMeta.init(reader,field);

			if( terms.size() == 1)
				return new PositionalScorer.TermScorer(this, tps, getPositions(), stopWordCount, 
						similarity,reader.norms(field), options);
			else if( slop == 0 )				 
				return new PositionalScorer.ExactScorer(this, tps, getPositions(), stopWordCount,
						similarity,	reader.norms(field), options);
			else
				return new PositionalScorer.SloppyScorer(this, tps, getPositions(), stopWordCount, 
						similarity, slop,	reader.norms(field), options);
		}
		
	    public Explanation explain(IndexReader reader, int doc)
	      throws IOException {

	      Explanation result = new Explanation();
	      result.setDescription("weight(custom["+getQuery()+"] in "+doc+"), product of:");

	      StringBuffer docFreqs = new StringBuffer();
	      StringBuffer query = new StringBuffer();
	      query.append('\"');
	      for (int i = 0; i < terms.size(); i++) {
	        if (i != 0) {
	          docFreqs.append(" ");
	          query.append(" ");
	        }

	        Term term = (Term)terms.elementAt(i);

	        docFreqs.append(term.text());
	        docFreqs.append("=");
	        docFreqs.append(reader.docFreq(term));

	        query.append(term.text());
	      }
	      query.append('\"');

	      Explanation idfExpl =
	        new Explanation(idf, "idf(" + field + ": " + docFreqs + ")");

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
	      Explanation fieldExpl = new Explanation();
	      fieldExpl.setDescription("fieldWeight("+field+":"+query+" in "+doc+
	                               "), product of:");

	      Explanation tfExpl = scorer(reader).explain(doc);
	      fieldExpl.addDetail(tfExpl);
	      
	      fieldExpl.addDetail(idfExpl);

	      Explanation fieldNormExpl = new Explanation();
	      //byte[] fieldNorms = reader.norms(field);
	      //fieldNorms!=null ? Similarity.decodeNorm(fieldNorms[doc]) : 0.0f;
	      float fieldNorm = 1;
	      fieldNormExpl.setValue(fieldNorm);
	      fieldNormExpl.setDescription("fieldNorm(field="+field+", doc="+doc+")");
	      fieldExpl.addDetail(fieldNormExpl);

	      fieldExpl.setValue(tfExpl.getValue() *
	                         idfExpl.getValue() *
	                         fieldNormExpl.getValue());

	      result.addDetail(fieldExpl);

	      // combine them
	      result.setValue(queryExpl.getValue() * fieldExpl.getValue());

	      if (queryExpl.getValue() == 1.0f)
	        return fieldExpl;

	      return result;
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
		final PositionalQuery other = (PositionalQuery) obj;
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
