package org.wikimedia.lsearch.suggest;

public class SuggestResult {
	String word;
	int frequency=0;
	int dist=0;
	int distMetaphone=0;
	int distMetaphone2=0;
	double jaro=1;
	
	public SuggestResult(String word, int frequency) {
		super();
		this.word = word;
		this.frequency = frequency;
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
	public double getJaro() {
		return jaro;
	}
	public void setJaro(double jaro) {
		this.jaro = jaro;
	}

	@Override
	public String toString() {
		return "("+word + " dist:" + dist + ",freq:" + frequency+",meta:"+distMetaphone+",meta2:"+distMetaphone2+",jaro: "+jaro+")" ;
	}
	
}
