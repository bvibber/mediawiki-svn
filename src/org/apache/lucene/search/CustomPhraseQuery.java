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
	protected ScoreValue val = null;
	protected PhraseInfo phInfo = null;
	protected boolean beginBoost;
	protected Query stemtitle;
	protected Query related;
	protected boolean max; 
	
	public CustomPhraseQuery(){
		this((ScoreValue)null);
	}	
	
	public CustomPhraseQuery(ScoreValue val){
		this(val,true,null);
	}
	
	public CustomPhraseQuery(ScoreValue val, boolean beginBoost){
		this(val,beginBoost,null);
	}
	
	public CustomPhraseQuery(PhraseInfo phInfo, boolean max){
		this(null,false,phInfo,null,null,max);
	}
	
	public CustomPhraseQuery(ScoreValue val, PhraseInfo phInfo){
		this(val,false,phInfo);
	}
	
	public CustomPhraseQuery(ScoreValue val, boolean beginBoost, PhraseInfo phInfo){
		this(val,beginBoost,phInfo,null,null);
	}
	
	public CustomPhraseQuery(ScoreValue val, boolean beginBoost, PhraseInfo phInfo, Query stemtitle, Query related){
		this(val,beginBoost,phInfo,stemtitle,related,false);
	}
	
	public CustomPhraseQuery(ScoreValue val, boolean beginBoost, PhraseInfo phInfo, Query stemtitle, Query related, boolean max){
		this.val = val;
		this.beginBoost = beginBoost;
		this.phInfo = phInfo;
		this.stemtitle = stemtitle;
		this.related = related;
		this.max = max;
	}

	@Override
	public Query rewrite(IndexReader reader) throws IOException {
		if(stemtitle != null)
			stemtitle.rewrite(reader);
		if(related != null)
			related.rewrite(reader);
		return this;
	}

	protected Weight createWeight(Searcher searcher) throws IOException {
		/*if (terms.size() == 1) {			  // optimize one-term case
			Term term = (Term)terms.elementAt(0);
			Query termQuery = new TermQuery(term);
			termQuery.setBoost(getBoost());
			return termQuery.createWeight(searcher);
		}*/
		return new CustomPhraseWeight(searcher);
	}
	
	protected class CustomPhraseWeight extends PhraseWeight{
		protected Weight stemtitleWeight = null;
		protected Weight relatedWeight = null;
		
		public CustomPhraseWeight(Searcher searcher) throws IOException{
			super(searcher);
			// init additional weights
			if(stemtitle != null)
				stemtitleWeight = stemtitle.createWeight(searcher);
			if(related != null)
				relatedWeight = related.createWeight(searcher);
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
			
			// init the field value for local reader
			if(val != null)
				val.init(reader);
			
			// init phrase info
			if(phInfo != null)
				phInfo.init(reader,field);
			
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
				return new TermCustomPhraseScorer(this, tps, getPositions(), similarity,
						reader.norms(field), val, beginBoost, phInfo, stemtitleScorer, 
						relatedScorer, stemtitleWeight, relatedWeight, max);
			else if( slop == 0 )				  // optimize exact case
				return new ExactCustomPhraseScorer(this, tps, getPositions(), similarity,
						reader.norms(field), val, beginBoost, phInfo, stemtitleScorer, relatedScorer, 
						stemtitleWeight, relatedWeight, max);
			else
				return new SloppyCustomPhraseScorer(this, tps, getPositions(), similarity, slop,
						reader.norms(field), val, beginBoost, phInfo, stemtitleScorer, relatedScorer, 
						stemtitleWeight, relatedWeight, max);

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
