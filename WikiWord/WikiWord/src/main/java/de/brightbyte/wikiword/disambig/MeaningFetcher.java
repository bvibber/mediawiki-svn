package de.brightbyte.wikiword.disambig;

import java.util.Collection;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public interface MeaningFetcher<C extends WikiWordConcept> {
	public List<C> getMeanings(String term) throws PersistenceException;
	
	public <X extends TermReference>Map<X, List<C>> getMeanings(Collection<X> terms) throws PersistenceException;
}
