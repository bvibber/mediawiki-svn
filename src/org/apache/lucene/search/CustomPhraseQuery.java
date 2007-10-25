package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermPositions;
import org.apache.lucene.search.*;
import org.apache.lucene.search.PhraseQuery.PhraseWeight;

/**
 * PhraseQuery where score is based on the position of phrase in the document
 * 
 * @author rainman
 *
 */
public class CustomPhraseQuery extends PhraseQuery {
	protected QueryOptions options; 
	/**  for combined queries */
	protected QueryOptions additionalOptions;
	protected int stopWordCount = 0;
	
	public CustomPhraseQuery(QueryOptions options){
		this.options = options;
	}
	
	public CustomPhraseQuery(QueryOptions sloppyOptions, QueryOptions exactOptions){
		this.options = sloppyOptions;
		this.additionalOptions = exactOptions;
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
	
	@Override
	public Query rewrite(IndexReader reader) throws IOException {
		if(options.stemtitle != null)
			options.stemtitle.rewrite(reader);
		if(options.related != null)
			options.related.rewrite(reader);
		return this;
	}

	public String toString(String f) {
		String s = super.toString(f);
		String stem="", rel="";
		if(options.stemtitle != null)
			stem = options.stemtitle.toString();
		if(options.related != null)
			rel = options.related.toString();
		return "(custom "+s+ ((stem.length()+rel.length()>0)? " with "+stem+" "+rel : "")+")";
	}
	
	protected Weight createWeight(Searcher searcher) throws IOException {
		return new CustomPhraseWeight(searcher);
	}
	
	protected class CustomPhraseWeight extends PhraseWeight{
		protected Weight stemtitleWeight = null;
		protected Weight relatedWeight = null;
		
		public CustomPhraseWeight(Searcher searcher) throws IOException{
			super(searcher);
			// init additional weights
			if(options.stemtitle != null)
				stemtitleWeight = options.stemtitle.createWeight(searcher);
			if(options.related != null)
				relatedWeight = options.related.createWeight(searcher);
		}
		
		public float sumOfSquaredWeights() throws IOException {
			if(stemtitleWeight != null)
				stemtitleWeight.sumOfSquaredWeights();
			if(relatedWeight != null)
				relatedWeight.sumOfSquaredWeights();
			queryWeight = idf * getBoost();             // compute query weight
			return queryWeight * queryWeight;           // square it
		}
	    
		public void normalize(float queryNorm) {
			if(stemtitleWeight != null)
				stemtitleWeight.normalize(queryNorm);
			if(relatedWeight != null)
				relatedWeight.normalize(queryNorm);
			this.queryNorm = queryNorm;
			queryWeight *= queryNorm;                   // normalize query weight
			value = queryWeight * idf;                  // idf for document 
		}
		
		public Scorer scorer(IndexReader reader) throws IOException {
			if (terms.size() == 0)			  // optimize zero-term case
				return null;
			
			// init meta info for this local indexreader
			if(options.rankMeta != null)
				options.rankMeta.init(reader);
			if(options.aggregateMeta != null)
				options.aggregateMeta.init(reader,field);
			
			TermPositions[] tps = new TermPositions[terms.size()];
			for (int i = 0; i < terms.size(); i++) {
				TermPositions p = reader.termPositions((Term)terms.elementAt(i));
				if (p == null)
					return null;
				tps[i] = p;
			}
			
			// additional scorers
			Scorer stemtitleScorer = null;
			Scorer relatedScorer = null;
			if(stemtitleWeight != null)
				stemtitleScorer = stemtitleWeight.scorer(reader);
			if(relatedWeight != null)
				relatedScorer = relatedWeight.scorer(reader);

			if( terms.size() == 1)
				return new TermCustomPhraseScorer(this, tps, getPositions(), stopWordCount, 
						similarity,reader.norms(field), options, stemtitleScorer, relatedScorer);
			else if( slop == 0 )				  // optimize exact case
				return new ExactCustomPhraseScorer(this, tps, getPositions(), stopWordCount,
						similarity,	reader.norms(field), options, stemtitleScorer, relatedScorer);
			else
				return new SloppyCustomPhraseScorer(this, tps, getPositions(), stopWordCount, 
						similarity, slop,	reader.norms(field), options, stemtitleScorer, relatedScorer);
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
	      // additional scorers
	      /*Explanation add = new Explanation(1,"Additional explanations:");
	      if(stemtitleWeight != null)
	      	add.addDetail(stemtitleWeight.scorer(reader).explain(doc));
	      if(relatedWeight != null)
	      	add.addDetail(relatedWeight.scorer(reader).explain(doc));
	      fieldExpl.addDetail(add); */
	      
	      fieldExpl.addDetail(idfExpl);

	      Explanation fieldNormExpl = new Explanation();
	      //byte[] fieldNorms = reader.norms(field);
	      float fieldNorm = 1;
	        //fieldNorms!=null ? Similarity.decodeNorm(fieldNorms[doc]) : 0.0f;
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
}
