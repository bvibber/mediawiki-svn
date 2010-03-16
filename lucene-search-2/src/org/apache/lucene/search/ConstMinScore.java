package org.apache.lucene.search;

public class ConstMinScore extends CustomBoostQuery {
	float minScore;
	public ConstMinScore(Query subQuery, float minScore) {
		super(subQuery);
		this.minScore = minScore;
	}

	@Override
	public float customScore(int doc, float subQueryScore, float boostScore) {
		if(subQueryScore == 0)
			return 0;
		else if(subQueryScore < minScore)
			return minScore;
		else
			return subQueryScore;

	}
	
	@Override
	public Explanation customExplain(int doc, Explanation subQueryExpl, Explanation boostExpl) {
	    Explanation exp = new Explanation( customScore(0,subQueryExpl.getValue(),0), "custom score: const min "+minScore+" of:");
	    exp.addDetail(subQueryExpl);
	    return exp;
	}	
}
