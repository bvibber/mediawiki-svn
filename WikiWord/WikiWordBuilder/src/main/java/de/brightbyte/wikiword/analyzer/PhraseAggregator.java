package de.brightbyte.wikiword.analyzer;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.regex.Matcher;

public class PhraseAggregator {
	public class PhraseBuilder {
			protected StringBuilder phrase;
			protected int weight;
			protected int lastWeight;
			protected int offset;
			
			public PhraseBuilder(int offset) {
				this.phrase = new StringBuilder();
				this.weight = 0;
				this.offset = offset;
			}
	
			public int getLength() {
				return phrase.length();
			}
	
			public int getOffset() {
				return offset;
			}
	
			public int getEndOffset() {
				return getOffset() + getLength();
			}
	
			public String getPhrase() {
				return phrase.toString();
			}

			public int getWeight() {
				return weight;
			}

			public int getLastWeight() {
				return lastWeight;
			}
			
			public PhraseOccurance toPhraseOccurance() {
				return new PhraseOccurance(getPhrase(), getWeight(), getOffset(), getLength());
			}
	
			public String toString() {
				return "\"" + getPhrase() + "\" @[" + getOffset() + ":" + getEndOffset() + "]";
			}

			public void push(CharSequence w, int weight) {
				phrase.append(w);
				if (weight>0) this.weight+= weight;
				this.lastWeight = weight;
			}
	}

	private int offset = 0;
	private int maxWeight = 0;
	
	private Matcher phraseBreakeMatcher;
	private ArrayList<PhraseBuilder> phrases = new ArrayList<PhraseBuilder>(); 

	public PhraseAggregator(Matcher phraseBreakeMatcher) {
		super();
		this.phraseBreakeMatcher = phraseBreakeMatcher;
	}

	public void reset(int offset, int maxWeight) {
		this.offset = offset;
		this.maxWeight = maxWeight;
		clear();
	}

	public void clear() {
		phrases.clear();
	}

	public void update(int index, CharSequence word, int weight,  Collection<PhraseOccurance> into) {
		if (weight<0) {
			phraseBreakeMatcher.reset(word);
			if (phraseBreakeMatcher.matches()) {
				this.clear();
				return;
			}
		}
		
		this.push(index, word, weight);
		this.commit(into);
	}

	public void push(int index, CharSequence word, int weight) {
		if (weight >= 0) phrases.add(new PhraseBuilder(index+offset));

		Iterator<PhraseBuilder> it = phrases.iterator();
		while (it.hasNext()) {
			PhraseBuilder b = it.next();
			b.push(word, weight);
			if (b.getWeight() > maxWeight) it.remove();
		}
	}

	public void commit(Collection<PhraseOccurance> into) {
		for (PhraseBuilder b: phrases) {
			if (b.getWeight() > 0 && b.getLastWeight() > 0) into.add(b.toPhraseOccurance());
		}
	}

}
