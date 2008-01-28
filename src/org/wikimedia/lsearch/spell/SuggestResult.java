package org.wikimedia.lsearch.spell;

public class SuggestResult {
	String word;
	int frequency=0;
	int dist=0;
	int distMetaphone=0;
	int distMetaphone2=0;
	boolean sameLetters=false;
	
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
		this.frequency = frequency;
		this.dist = dist;
	}
	
	/** Initialize all atributes using suggestion metrics */
	public SuggestResult(String word, int frequency, Suggest.Metric metric) {
		this.word = word;
		this.frequency = frequency;
		this.dist = metric.distance(word);
		this.distMetaphone = metric.meta1Distance(word);
		this.distMetaphone2 = metric.meta2Distance(word);
		this.sameLetters = metric.hasSameLetters(word);
	}
	
	/** Initialize all atributes using suggestion metrics */
	public SuggestResult(String word, int frequency, Suggest.Metric metric, String meta1, String meta2) {
		this.word = word;
		this.frequency = frequency;
		this.dist = metric.distance(word);
		this.distMetaphone = metric.sdmeta1.getDistance(meta1);
		this.distMetaphone2 = metric.sdmeta2.getDistance(meta2);
		this.sameLetters = metric.hasSameLetters(word);
	}
	
	public int getDist() {
		return dist;
	}

	public void setDist(int dist) {
		this.dist = dist;
	}

	public int getDistMetaphone() {
		return distMetaphone;
	}

	public void setDistMetaphone(int distMetaphone) {
		this.distMetaphone = distMetaphone;
	}

	public int getDistMetaphone2() {
		return distMetaphone2;
	}

	public void setDistMetaphone2(int distMetaphone2) {
		this.distMetaphone2 = distMetaphone2;
	}

	public int getFrequency() {
		return frequency;
	}

	public void setFrequency(int frequency) {
		this.frequency = frequency;
	}

	public String getWord() {
		return word;
	}

	public void setWord(String word) {
		this.word = word;
	}

	@Override
	public String toString() {
		return "("+word + " dist:" + dist + ",freq:" + frequency+",meta:"+distMetaphone+",meta2:"+distMetaphone2+")" ;
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
