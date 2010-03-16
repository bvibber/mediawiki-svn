package org.apache.lucene.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Set;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.util.ToStringUtils;

/**
 * Composite query:
 * 1) main subquery - usually phrase on contents
 * 2) array of relevance measure subqueries 
 * 
 * The final score is product of all relevance
 * scores and the main query score
 * 
 * @author rainman
 *
 */
public class RelevanceQuery extends Query {
	private Query main;
	private ArrayList<Query> relevance = new ArrayList<Query>(); 
	private boolean strict = false; // if true, boosting part of query does not take part in weights normalization.

	public RelevanceQuery(Query main) {
		this(main,null);
	}

	public RelevanceQuery(Query main, Collection<Query> relevance) {
		super();
		this.main = main;
		if(relevance != null)
			this.relevance.addAll(relevance);
	}
	
	public void addRelevanceMeasure(Query r){
		relevance.add(r);
	}

	/*(non-Javadoc) @see org.apache.lucene.search.Query#rewrite(org.apache.lucene.index.IndexReader) */
	public Query rewrite(IndexReader reader) throws IOException {
		main = main.rewrite(reader);

		ArrayList<Query> rewritten = new ArrayList<Query>();
		for(Query r : relevance)
			rewritten.add(r.rewrite(reader));
		relevance = rewritten;

		return this;
	}

	/*(non-Javadoc) @see org.apache.lucene.search.Query#extractTerms(java.util.Set) */
	public void extractTerms(Set terms) {
		main.extractTerms(terms);
		for(Query r : relevance)
			r.extractTerms(terms);
	}

	/*(non-Javadoc) @see org.apache.lucene.search.Query#clone() */
	public Object clone() {
		RelevanceQuery clone = (RelevanceQuery)super.clone();
		clone.main = (Query) main.clone();
		clone.relevance.addAll(relevance);
		return clone;
	}

	/* (non-Javadoc) @see org.apache.lucene.search.Query#toString(java.lang.String) */
	public String toString(String field) {
		StringBuffer sb = new StringBuffer("relevance ([");
		sb.append(main.toString(field));
		sb.append("]");
		for(Query r : relevance)
			sb.append(", ").append(r.toString(field));

		sb.append(")");
		sb.append(strict?" STRICT" : "");
		return sb.toString() + ToStringUtils.boost(getBoost());
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((main == null) ? 0 : main.hashCode());
		result = PRIME * result + ((relevance == null) ? 0 : relevance.hashCode());
		result = PRIME * result + (strict ? 1231 : 1237);
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final RelevanceQuery other = (RelevanceQuery) obj;
		if (main == null) {
			if (other.main != null)
				return false;
		} else if (!main.equals(other.main))
			return false;
		if (relevance == null) {
			if (other.relevance != null)
				return false;
		} else if (!relevance.equals(other.relevance))
			return false;
		if (strict != other.strict)
			return false;
		return true;
	}

	/**
	 * Relevance Weight
	 * 
	 */
	protected class RelevanceWeight implements Weight {
		Weight mainWeight;
		ArrayList<Weight> relevanceWeight = new ArrayList<Weight>();
		boolean qStrict;
		Similarity similarity;

		public RelevanceWeight(Searcher searcher) throws IOException {
			mainWeight = main.weight(searcher);
			for(Query r : relevance)
				relevanceWeight.add(r.weight(searcher));
			this.qStrict = strict;
			this.similarity = getSimilarity(searcher);
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Weight#getQuery() */
		public Query getQuery() {
			return RelevanceQuery.this;
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Weight#getValue() */
		public float getValue() {
			return getBoost();
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Weight#sumOfSquaredWeights() */
		public float sumOfSquaredWeights() throws IOException {
			float sum = mainWeight.sumOfSquaredWeights();
			for(Weight rw : relevanceWeight){
				if(qStrict)
					rw.sumOfSquaredWeights();
				else
					sum += rw.sumOfSquaredWeights();
			}
			sum *= getBoost() * getBoost();
			return sum;
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Weight#normalize(float) */
		public void normalize(float norm) {
			// norm *= getBoost(); // incorporate boost
			mainWeight.normalize(norm);
			for(Weight rw : relevanceWeight){
				if(qStrict)
					rw.normalize(1);
				else
					rw.normalize(norm);
			}
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Weight#scorer(org.apache.lucene.index.IndexReader) */
		public Scorer scorer(IndexReader reader) throws IOException {
			Scorer mainScorer = mainWeight.scorer(reader);
			ArrayList<Scorer> relSc = new ArrayList<Scorer>();
			for(Weight wr : relevanceWeight)
				relSc.add(wr.scorer(reader));			
			return new RelevanceScorer(similarity, reader, this, mainScorer, relSc, relevanceWeight);
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Weight#explain(org.apache.lucene.index.IndexReader, int) */
		public Explanation explain(IndexReader reader, int doc) throws IOException {
			return scorer(reader).explain(doc);
		}
	}


	//=========================== S C O R E R ============================

	/**
	 * A scorer that applies a (callback) function on scores of the subQuery.
	 */
	static protected class RelevanceScorer extends Scorer {
		private final RelevanceWeight weight;
		private final float qWeight;
		private Scorer mainScorer;
		private ArrayList<Scorer> relevanceScorer;
		private ArrayList<Weight> relevanceWeight;
		private IndexReader reader;

		// constructor
		protected RelevanceScorer(Similarity similarity, IndexReader reader, RelevanceWeight w,
				Scorer mainScorer, ArrayList<Scorer> relevanceScorer, ArrayList<Weight> relevanceWeight) throws IOException {
			super(similarity);
			this.weight = w;
			this.qWeight = w.getValue();
			this.mainScorer = mainScorer;
			this.relevanceScorer = relevanceScorer;
			this.relevanceWeight = relevanceWeight;
			this.reader = reader;
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Scorer#next() */
		public boolean next() throws IOException {
			boolean hasNext = mainScorer.next();

			if(hasNext){
				for(Scorer rs : relevanceScorer)
					rs.skipTo(doc());
			}
			return hasNext;
		}
		
		/*(non-Javadoc) @see org.apache.lucene.search.Scorer#skipTo(int) */
		public boolean skipTo(int target) throws IOException {
			boolean hasNext = mainScorer.skipTo(target);

			if(hasNext){
				for(Scorer rs : relevanceScorer)
					rs.skipTo(doc());
			}
			return hasNext;
		}


		/*(non-Javadoc) @see org.apache.lucene.search.Scorer#doc() */
		public int doc() {
			return mainScorer.doc();
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Scorer#score() */
		public float score() throws IOException {
			float sc = mainScorer.score();
			for(Scorer rs : relevanceScorer){
				if(rs.doc() == mainScorer.doc())
					sc *=  1 + rs.score();
			}
			return qWeight * sc;
		}

		/*(non-Javadoc) @see org.apache.lucene.search.Scorer#explain(int) */
		public Explanation explain(int doc) throws IOException {
			Explanation mainExpl = weight.mainWeight.explain(reader,doc);
			if (!mainExpl.isMatch()) {
				return mainExpl;
			}
			
			// match
			float score = mainExpl.getValue();
			Explanation res = new ComplexExplanation(true,0,weight.getQuery().toString()+", product of: (for doc="+doc+")");
			res.addDetail(mainExpl);
			for(int i=0;i<relevanceScorer.size();i++){
				Explanation r = relevanceWeight.get(i).explain(reader,doc);
				if(r.isMatch()){
					Explanation mod = new Explanation(r.getValue()+1,"1 + relevance measure");
					mod.addDetail(r);
					score *= mod.getValue();
					res.addDetail(mod);
				}
			}
			res.addDetail(new Explanation(qWeight, "queryBoost"));
			score *= qWeight;
			res.setValue(score);
			return res;
		}
	}

	/*(non-Javadoc) @see org.apache.lucene.search.Query#createWeight(org.apache.lucene.search.Searcher) */
	protected Weight createWeight(Searcher searcher) throws IOException {
		return new RelevanceWeight(searcher);
	}

	/**
	 * Checks if this is strict custom scoring (no normalization for relevance queries)
	 */
	public boolean isStrict() {
		return strict;
	}

	/**
	 * Set the strict mode of this query. 
	 * @param strict The strict mode to set.
	 * @see #isStrict()
	 */
	public void setStrict(boolean strict) {
		this.strict = strict;
	}
}
