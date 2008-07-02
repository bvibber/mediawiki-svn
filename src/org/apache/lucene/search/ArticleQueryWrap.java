package org.apache.lucene.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.CustomBoostQuery.CustomWeight;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.RankField;

/**
 * Wrapper for queries to provide scaling based 
 * on article's meta data, e.g. subpage status or
 * how old it is 
 * 
 * @author rainman
 *
 */
public class ArticleQueryWrap extends CustomBoostQuery {
	ArticleInfo article;
	ArticleScaling scale;	
	AggregateInfo rank;
	/** scale coeff for subpages */
	final float SUBPAGE = 0.5f;
	
	public ArticleQueryWrap(Query subQuery, ArticleInfo article, ArticleScaling scale, AggregateInfo rank) {
		super(subQuery);
		this.article = article;
		this.scale = scale;
		this.rank = rank;
	}
	
	@Override
	public float customScore(int doc, float subQueryScore, float boostScore) throws IOException {
		float sub = 1;
		if(article.isSubpage(doc))
			sub = SUBPAGE;
		
		float r = 1;
		if(rank != null)
			r = rank.rank(doc);
		
		return sub * r * scale.score(subQueryScore,article.daysOld(doc));		
	}
	
	@Override
	public Explanation customExplain(int doc, Explanation subQueryExpl, Explanation boostExpl) throws IOException {
		Explanation sub = new Explanation(article.isSubpage(doc)? SUBPAGE : 1, "subpage");
		Explanation scl = new Explanation(scale.score(subQueryExpl.getValue(),article.daysOld(doc)),scale.explain(subQueryExpl.getValue(),article.daysOld(doc))+" (age scalling for "+article.daysOld(doc)+" days)");
		Explanation ran = new Explanation(rank==null? 1 : rank.rank(doc), "additional rank");
		
		Explanation exp = new Explanation( sub.getValue()*scl.getValue()*ran.getValue(), "article-scaled score, transformation of:");
		exp.addDetail(subQueryExpl);
		exp.addDetail(sub);
		exp.addDetail(scl);
		exp.addDetail(ran);
		return exp;
	}
	
	protected Weight createWeight(Searcher searcher) throws IOException {
	    return new ArticleQueryWeight(searcher);
	}
	
	protected class ArticleQueryWeight extends CustomWeight {
		public ArticleQueryWeight(Searcher searcher) throws IOException{
			super(searcher);
		}
		public Scorer scorer(IndexReader reader) throws IOException {
			if(article != null)
				article.init(reader);
			if(rank != null)
				rank.init(reader,"alttitle");
			return super.scorer(reader);
	    }
	}
	
}
