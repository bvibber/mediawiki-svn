package org.apache.lucene.search;

import java.io.Serializable;

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
	
	/** Options specific for phrases in contents */
	public static class Sloppy extends PositionalOptions {
		public Sloppy(){
			rankMeta = new RankInfo();
			useBeginBoost = true;
			exactBoost = 1;
			useNoStopWordLen = true;
		}
	}
	
	/** Options specific for phrases that match exactly in contents */
	public static class Exact extends PositionalOptions {
		public Exact(){
			rankMeta = new RankInfo();
			useBeginBoost = true;
			exactBoost = 8;
			//beginTable = new float[] { 256, 64, 4, 2 }; 
		}
	}
	
	/** Options for alttitle field */
	public static class Alttitle extends PositionalOptions {
		public Alttitle(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			wholeBoost = 10;
		}
	}
	/** Match only whole entries on an aggregate field */
	public static class AlttitleWhole extends PositionalOptions {
		public AlttitleWhole(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
			wholeBoost = 10000;
			wholeNoStopWordsBoost = 1000;
			//onlyWholeMatch = true;
		}
	}
	
	/** Options specific to related fields */
	public static class Related extends PositionalOptions {
		public Related(){
			aggregateMeta = new AggregateInfoImpl();
			takeMaxScore = true;
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
			// these are so large because we take their sqrt and multiply with query norm
			wholeNoStopWordsBoost = 100000000;
			wholeBoost = 500000000;
			useNoStopWordLen = true;
			useRankForWholeMatch = true;
		}
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + Float.floatToIntBits(exactBoost);
		result = PRIME * result + (onlyWholeMatch ? 1231 : 1237);
		result = PRIME * result + (takeMaxScore ? 1231 : 1237);
		result = PRIME * result + (useBeginBoost ? 1231 : 1237);
		result = PRIME * result + (useNoStopWordLen ? 1231 : 1237);
		result = PRIME * result + Float.floatToIntBits(wholeBoost);
		result = PRIME * result + Float.floatToIntBits(wholeNoStopWordsBoost);
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
		final PositionalOptions other = (PositionalOptions) obj;
		if (Float.floatToIntBits(exactBoost) != Float.floatToIntBits(other.exactBoost))
			return false;
		if (onlyWholeMatch != other.onlyWholeMatch)
			return false;
		if (takeMaxScore != other.takeMaxScore)
			return false;
		if (useBeginBoost != other.useBeginBoost)
			return false;
		if (useNoStopWordLen != other.useNoStopWordLen)
			return false;
		if (Float.floatToIntBits(wholeBoost) != Float.floatToIntBits(other.wholeBoost))
			return false;
		if (Float.floatToIntBits(wholeNoStopWordsBoost) != Float.floatToIntBits(other.wholeNoStopWordsBoost))
			return false;
		return true;
	}
	
	
	
	
}
