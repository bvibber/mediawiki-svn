package org.wikimedia.lsearch.spell.api;

public interface Dictionary {
	public static class Word {
		protected String word;
		protected int frequency;
		public Word(String word, int frequency) {
			super();
			this.word = word;
			this.frequency = frequency;
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
		public String toString(){
			return word+" : "+frequency;
		}
		
	}
	/** Get next term or null if there is no more terms */
	public Word next();
}
