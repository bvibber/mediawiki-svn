package de.brightbyte.wikiword.disambig;

import java.util.List;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.store.LocalConceptStore;

public class StoredMeaningFetcher implements MeaningFetcher<LocalConcept> {
	protected LocalConceptStore  store; 
	
	public StoredMeaningFetcher(LocalConceptStore  store) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
	}

	public List<LocalConcept> getMeanings(String term) throws PersistenceException {
		DataSet<LocalConcept> m = store.getMeanings(term); //FIXME: filter/cut-off rules, sort order! //XXX: relevance value?
		return m.load();
	}

}
