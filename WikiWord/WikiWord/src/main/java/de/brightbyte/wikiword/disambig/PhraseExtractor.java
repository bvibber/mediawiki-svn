package de.brightbyte.wikiword.disambig;

import de.brightbyte.wikiword.model.PhraseOccuranceSet;

public interface PhraseExtractor {

	public PhraseOccuranceSet extractPhrases(CharSequence s, int maxWeight, int maxDepth);

}
