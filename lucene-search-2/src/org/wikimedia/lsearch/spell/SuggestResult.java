package org.wikimedia.lsearch.spell;

import java.io.Serializable;
import java.util.HashSet;

import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.ranks.StringList;

public class SuggestResult implements Serializable {
	String word;
	String decomposed;
	int frequency=0;
	int dist=0;
	int distMetaphone=0;
	int distMetaphone2=0;
	String meta1 = null;
	String meta2 = null;
	boolean sameLetters=false;
	boolean exactMatch=false;
	String serializedContext = null;
	HashSet<String> context = null;
	
	public static class SimilarResult extends SuggestResult {
		String key;
		String redirectTo;
		public SimilarResult(String word, int frequency, int dist, String key, String redirectTo) {
			super(word, frequency, dist);
			this.key = key;
			this.redirectTo = redirectTo;
		}
		
	}
	
	static class Comparator implements java.util.Comparator<SuggestResult> {
		public int compare(SuggestResult o1, SuggestResult o2){
			if(o1.dist - o2.dist == -1 && o1.frequency * 100 < o2.frequency)
				return 1;
			else if(o1.dist - o2.dist == 1 && o2.frequency * 100 < o1.frequency)
				return -1;
			else if(o1.dist == o2.dist){			
				if(!o1.sameLetters && o2.sameLetters)
					return 1;
				else if(o1.sameLetters && !o2.sameLetters)
					return -1;
				else
					return o2.getFrequency() - o1.getFrequency();
			} else 
				return o1.dist - o2.dist;					
		}
	}
	
	static class ComparatorForTitles implements java.util.Comparator<SuggestResult> {
		public int compare(SuggestResult o1, SuggestResult o2){	
			if(o1.dist - o2.dist == -1 && o1.frequency * 30 < o2.frequency)
				return 1;
			else if(o1.dist - o2.dist == 1 && o2.frequency * 30 < o1.frequency)
				return -1;
			else if(o1.dist == o2.dist){				
				if(!o1.sameLetters && o2.sameLetters)
					return 1;
				else if(o1.sameLetters && !o2.sameLetters)
					return -1;
				else
					return o2.getFrequency() - o1.getFrequency();
			} else 
				return o1.dist - o2.dist;					
		}
	}
	
	static class ComparatorNoCommonMisspell implements java.util.Comparator<SuggestResult> {
		public int compare(SuggestResult o1, SuggestResult o2){	
			if(o1.dist == o2.dist){
				if(!o1.sameLetters && o2.sameLetters)
					return 1;
				else if(o1.sameLetters && !o2.sameLetters)
					return -1;
				else
					return o2.getFrequency() - o1.getFrequency();
			} else 
				return o1.dist - o2.dist;					
		}
	}
	
	/** Init main attributes (metaphone distances default to 0) */
	public SuggestResult(String word, int frequency, int dist) {
		this.word = word;
		this.decomposed = FastWikiTokenizerEngine.decompose(word);
		this.frequency = frequency;
		this.dist = dist;
		this.exactMatch = true;
	}
	
	/** Initialize all attributes using suggestion metrics */
	public SuggestResult(String word, int frequency, Suggest.Metric metric, String meta1, String meta2) {
		this(word,frequency,metric,meta1,meta2,null);
	}
	
	/** Initiliaze using metric and serialized context */
	public SuggestResult(String word, int frequency, Suggest.Metric metric, String meta1, String meta2, String serializedContext) {
		this.word = word;
		this.decomposed = FastWikiTokenizerEngine.decompose(word);
		this.frequency = frequency;
		this.dist = metric.distance(decomposed);
		this.distMetaphone = metric.sdmeta1!=null? metric.sdmeta1.getDistance(meta1) : 0;
		this.distMetaphone2 = metric.sdmeta2!=null? metric.sdmeta2.getDistance(meta2) : 0;
		this.sameLetters = metric.hasSameLetters(word);
		this.serializedContext = serializedContext;
		this.meta1 = meta1;
		this.meta2 = meta2;
		this.exactMatch = decomposed.equals(metric.decomposed);
	}
	
	/** 
	 * Get modified edit distance of decomposed words, words with dist==0 can still be different,
	 * as deleting/adding repeated chars doesn't increase edit distance, (e.g. oh and ooooooh).
	 * @return
	 */
	public int getDist() {
		return dist;
	}

	public int getDistMetaphone() {
		return distMetaphone;
	}

	public int getDistMetaphone2() {
		return distMetaphone2;
	}

	public int getFrequency() {
		return frequency;
	}

	public String getWord() {
		return word;
	}

	public String getSerializedContext() {
		return serializedContext;
	}
	
	public HashSet<String> getContext(){
		if(context == null && serializedContext != null)
			context = new StringList(serializedContext).toHashSet();
		return context;
	}
	
	public boolean inContext(String w){
		HashSet<String> set = getContext();
		if(set == null)
			return false;
		else
			return set.contains(w);
	}
	
	/** If target and this word have same decomposed variants.
	 *  NOTE: distance==0 doesn't imply an exact match
	 * */
	public boolean isExactMatch() {
		return exactMatch;
	}

	@Override
	public String toString() {
		return "("+word + " dist:" + dist + ",freq:" + frequency+",metaD:"+distMetaphone+",metaD2:"+distMetaphone2+",meta1:"+meta1+",meta2:"+meta2+")" ;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((word == null) ? 0 : word.hashCode());
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
		final SuggestResult other = (SuggestResult) obj;
		if (word == null) {
			if (other.word != null)
				return false;
		} else if (!word.equals(other.word))
			return false;
		return true;
	}
	
}
