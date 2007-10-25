package org.apache.lucene.search;

import java.io.Serializable;

/**
 * Set of params, additional scoring, etc.. 
 * for custom phrase query. 
 * 
 * @author rainman
 *
 */
public class QueryOptions implements Serializable {
	protected ScoreValue rankMeta = null;	
	protected PhraseInfo aggregateMeta = null;
	protected Query stemtitle = null;
	protected Query related = null;

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
	protected boolean useStopWordLen = false;
	
	/** use child class constructors */
	private QueryOptions(){
	}
	
	/** Options specific to related fields */
	public static class RelatedOptions extends QueryOptions {
		public RelatedOptions(PhraseInfo relatedInfo){
			aggregateMeta = relatedInfo;
			takeMaxScore = true;
		}
	}
	
	/** Options specific for phrases in contents */
	public static class ContentsSloppyOptions extends QueryOptions {
		public ContentsSloppyOptions(ScoreValue rankValue, Query stemtitleQuery, Query relatedQuery){
			rankMeta = rankValue;
			stemtitle = stemtitleQuery;
			related = relatedQuery;
			useBeginBoost = true;
			exactBoost = 1;
			useStopWordLen = true;
		}
	}
	
	/** Options specific for phrases that match exactly in contents */
	public static class ContentsExactOptions extends QueryOptions {
		public ContentsExactOptions(ScoreValue rankValue, Query stemtitleQuery, Query relatedQuery){
			rankMeta = rankValue;
			stemtitle = stemtitleQuery;
			related = relatedQuery;
			useBeginBoost = true;
			exactBoost = 8;
		}
	}
	
	/** Options for alttitle field */
	public static class AlttitleSloppyOptions extends QueryOptions {
		public AlttitleSloppyOptions(PhraseInfo alttitleInfo){
			aggregateMeta = alttitleInfo;
			takeMaxScore = true;
			wholeNoStopWordsBoost = 300;
			useStopWordLen = true;
		}
	}
	
	/** Options for alttitle field */
	public static class AlttitleExactOptions extends QueryOptions {
		public AlttitleExactOptions(PhraseInfo alttitleInfo){
			aggregateMeta = alttitleInfo;
			takeMaxScore = true;
			wholeBoost = 500;
		}
	}
	
	/** Options for alttitle field */
	public static class AlttitleOptions extends QueryOptions {
		public AlttitleOptions(PhraseInfo alttitleInfo){
			aggregateMeta = alttitleInfo;
			takeMaxScore = true;
			wholeBoost = 10;
		}
	}
}
