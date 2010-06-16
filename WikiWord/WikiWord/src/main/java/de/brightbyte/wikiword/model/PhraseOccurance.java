package de.brightbyte.wikiword.model;

import java.io.Serializable;

public class PhraseOccurance implements Serializable, Comparable<PhraseOccurance>, TermReference {

		private static final long serialVersionUID = 241753475865301115L;
	
		protected String phrase;
		protected int weight;
		protected int offset;
		protected int length;
		
		public PhraseOccurance(String phrase, int weight, int offset, int length) {
			if (length < 0) throw new IllegalArgumentException("bad length: "+length);
			
			this.phrase = phrase;
			this.weight = weight;
			this.offset = offset;
			this.length = length;
		}
		
		public int getLength() {
			return length;
		}

		public int getOffset() {
			return offset;
		}

		public int getEndOffset() {
			return getOffset() + getLength();
		}

		public String getPhrase() {
			return phrase;
		}

		public String getTerm() {
			return getPhrase();
		}

		public double getWeight() {
			return weight;
		}
		
		public String toString() {
			return "\"" + getPhrase() + "\" @[" + getOffset() + ":" + getEndOffset() + "]#"+weight;
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + length;
			result = PRIME * result + offset;
			result = PRIME * result + ((phrase == null) ? 0 : phrase.hashCode());
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
			final PhraseOccurance other = (PhraseOccurance) obj;
			if (length != other.length)
				return false;
			if (offset != other.offset)
				return false;
			if (phrase == null) {
				if (other.phrase != null)
					return false;
			} else if (!phrase.equals(other.phrase))
				return false;
			return true;
		}
		
		public boolean overlaps(PhraseOccurance other) {
			if (getEndOffset() <= other.getOffset()) return false;
			if (getOffset() >= other.getEndOffset()) return false;
			
			return true;
		}

		public int compareTo(PhraseOccurance other) {
			int o = getOffset() - other.getOffset();
			if (o!=0) return o; //by offset...
			
			int e = getEndOffset() - other.getEndOffset();
			if (e!=0) return -e; //but longest first!
			
			return 0;
		}

}
