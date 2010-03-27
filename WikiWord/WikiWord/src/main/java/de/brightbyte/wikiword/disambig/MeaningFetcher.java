package de.brightbyte.wikiword.disambig;

import java.util.List;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface MeaningFetcher<C extends WikiWordConcept> {
	public List<C> getMeanings(String term) throws PersistenceException;
}
