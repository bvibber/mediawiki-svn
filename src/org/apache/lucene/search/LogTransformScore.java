package org.apache.lucene.search;

/**
 * Transform the score of a query, take log of actual score.
 * 
 * @author rainman
 *
 */
public class LogTransformScore extends CustomBoostQuery {

	public LogTransformScore(Query subQuery) {
		super(subQuery);
	}

	@Override
	public float customScore(int doc, float subQueryScore, float boostScore) {
		return (float) (Math.log10(1+subQueryScore));

	}
	
	@Override
	public Explanation customExplain(int doc, Explanation subQueryExpl, Explanation boostExpl) {
	    Explanation exp = new Explanation( (float)Math.log10(1+subQueryExpl.getValue()), "custom score: log10 of:");
	    exp.addDetail(subQueryExpl);
	    return exp;
	}	

}
