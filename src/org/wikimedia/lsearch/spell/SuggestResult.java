package org.wikimedia.lsearch.spell;

public class SuggestResult {
	String word;
	int frequency=0;
	int dist=0;
	int distMetaphone=0;
	int distMetaphone2=0;
	
	static class Comparator implements java.util.Comparator<SuggestResult> {
		public int compare(SuggestResult o1, SuggestResult o2){					
			if(o1.dist == o2.dist)
				return o2.getFrequency() - o1.getFrequency();
			else 
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
	
}
