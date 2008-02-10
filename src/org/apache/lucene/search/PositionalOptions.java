package org.apache.lucene.search;

import java.io.Serializable;

import org.wikimedia.lsearch.search.AggregateInfoImpl;

public class PositionalOptions implements Serializable {
	protected AggregateInfo aggregateMeta = null;

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
	
	/** Options specific for phrases in contents */
	public static class Sloppy extends PositionalOptions {
		public Sloppy(){
			useBeginBoost = true;
			exactBoost = 1;
			useNoStopWordLen = true;
		}
	}
	
	/** Options specific for phrases that match exactly in contents */
	public static class Exact extends PositionalOptions {
		public Exact(){
			useBeginBoost = true;
			exactBoost = 8;
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
			wholeNoStopWordsBoost = 10000;
			wholeBoost = 500000;
			useNoStopWordLen = true;
		}
	}
}
