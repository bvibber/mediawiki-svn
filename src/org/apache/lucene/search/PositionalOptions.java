package org.apache.lucene.search;

import java.io.Serializable;
import java.util.HashMap;

import org.wikimedia.lsearch.search.AggregateInfoImpl;
import org.wikimedia.lsearch.search.AggregateInfoImpl.RankInfo;
import org.wikimedia.lsearch.test.RankingTest;

public class PositionalOptions implements Serializable {
	protected AggregateInfo aggregateMeta = null;
	protected RankInfo rankMeta = null;

	/** use additional boosts when phrase if at the beginning of document */
	protected boolean useBeginBoost = false;	
	/** boost when whole phrase matches */
	protected float wholeBoost = 1;
	/** boost when phrases match when stop words are excluded */
	protected float wholeNoStopWordsBoost = 1;
	/** boost if the phrase matches with slop 0 */
	protected float exactBoost = 8;
	/** take max score from multiple phrase in the document instead of summing them up */
	protected boolean takeMaxScore = false;
	/** wether to use the stop words proportion */
	protected boolean useNoStopWordLen = false;
	/** nonzero score only on whole match (either all words, or all words without stopwords */
	protected boolean onlyWholeMatch = false;
	/** table of boost values for first 25, 50, 200, 500 tokens */
	protected float beginTable[] = { 16, 4, 4, 2 };
	/** useful for redirects - use main rank as whole-match boost */
	protected boolean useRankForWholeMatch = false;
	/** for sloppy phrases if we find a sloppy phrase at the very beginning */
	protected float beginExactBoost = 8;
	/** namespace-specific boost values */
	protected NamespaceBoost nsWholeBoost = null;
	/** when all tokens *and* aliases match (happens only when title and query are identical - same accents, etc..) */ 
	protected float completeBoost = 1;
	/** use complete number of tokens (with completeBoost) only for scoring */
	protected boolean useCompleteOnly = false;
	/** act exactly as a phrase query without any positional or such optimizations */
	protected boolean phraseQueryFallback = false;
	/** if single words can be whole matches */
	protected boolean allowSingleWordWholeMatch = false;
	/** extra boost if whole alttitle (and not anchor) is matched */
	protected float alttileWholeExtraBoost = 1;

	
	/** Options specific for phrases in contents */
	public static class Sloppy extends PositionalOptions {
		public Sloppy(){
			rankMeta = new RankInfo();
			useBeginBoost = true;
			exactBoost = 4;
			beginExactBoost = 8;
		}
	}
	
	/** Options specific for phrases that match exactly in contents */
	/* public static class Exact extends PositionalOptions {
		public Exact(){
			rankMeta = new RankInfo();
			useBeginBoost = true;
			exactBoost = 4;
			//beginTable = new float[] { 256, 64, 4, 2 }; 
		}
	} */
	
	/** Options for alttitle field */
	public static class Alttitle extends PositionalOptions {
		public Alttitle(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			//exactBoost = 2;
			//wholeBoost = 10;
		}
	}
	/** Match only whole entries on an aggregate field */
	public static class AlttitleWhole extends PositionalOptions {
		public AlttitleWhole(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			wholeBoost = 1000;
			alttileWholeExtraBoost = 100;
			//wholeNoStopWordsBoost = 1000;
			//onlyWholeMatch = true;
		}
	}
	
	public static class AlttitleWholeSloppy extends PositionalOptions {
		public AlttitleWholeSloppy(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			wholeBoost = 100;
			alttileWholeExtraBoost = 10;
			//exactBoost = 10;
			allowSingleWordWholeMatch = true;
			//wholeNoStopWordsBoost = 1000;
			//onlyWholeMatch = true;
		}
	}
	
	/** Options specific to related fields */
	public static class Related extends PositionalOptions {
		public Related(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			//exactBoost = 2;
		}
	}
	
	public static class RelatedWhole extends PositionalOptions {
		public RelatedWhole(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			// exactBoost = 4;
			allowSingleWordWholeMatch = true;
		}
	}
	
	/** Options for additional alttitle query */
	public static class AlttitleSloppy extends PositionalOptions {
		public AlttitleSloppy(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			wholeNoStopWordsBoost = 300;
			useNoStopWordLen = true;
		}
	}
	
	/** Options for additional alttitle query */
	public static class AlttitleExact extends PositionalOptions {
		public AlttitleExact(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			wholeBoost = 10000;
		}
	}
	
	/** alttitle query to match redirects */
	public static class RedirectMatch extends PositionalOptions {
		public RedirectMatch(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			wholeNoStopWordsBoost = 10000;
			wholeBoost = 50000;
			useRankForWholeMatch = true;
			nsWholeBoost = new NamespaceBoost.DefaultBoost();
			allowSingleWordWholeMatch = true;
			exactBoost = 100;
			alttileWholeExtraBoost = 100;
		}
	}
	
	/** alttitle query to match complete titles */
	public static class RedirectComplete extends PositionalOptions {
		public RedirectComplete(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			completeBoost = 500000000;
			useCompleteOnly = true;
			useRankForWholeMatch = true;
			nsWholeBoost = new NamespaceBoost.DefaultBoost();
		}
	}
	
	/** Options for sections field */
	public static class Sections extends PositionalOptions {
		public Sections(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			//wholeBoost = 8;
		}
	}
	
	/** Options for sections field */
	public static class SectionsWhole extends PositionalOptions {
		public SectionsWhole(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			// wholeBoost = 8;
			allowSingleWordWholeMatch = true;
		}
	}
	/** Fallback to phasequery-type behaviour, no positional info */
	public static class PhraseQueryFallback extends PositionalOptions {
		public PhraseQueryFallback(){
			phraseQueryFallback = true;
		}
	}
	
	/** Near match phrases, when more than 50% of nonstopwords are matched */
	public static class AlttitleNearMatch extends PositionalOptions {
		public AlttitleNearMatch(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
		}
	}
	
	public abstract static class NamespaceBoost implements Serializable {
		public abstract float getBoost(int namespace);
		
		public static class DefaultBoost extends NamespaceBoost {
			public float getBoost(int namespace){
				if(namespace % 2 == 1) // talk pages
					return 0.75f;  
				if(namespace == 10) // templates
					return 0.5f;
				return 1f;
			}
		}
	}

	

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		return true;
	}
	
	
	
}
