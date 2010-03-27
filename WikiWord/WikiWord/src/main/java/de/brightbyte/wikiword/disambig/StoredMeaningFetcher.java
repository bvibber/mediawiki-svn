package de.brightbyte.wikiword.disambig;

import java.util.List;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.store.LocalConceptStore;

public class StoredMeaningFetcher implements MeaningFetcher<LocalConcept> {
	protected LocalConceptStore  store; 
	protected ConceptType type;
	
	public StoredMeaningFetcher(LocalConceptStore  store) {
		this(store, null);
	}
	
	public StoredMeaningFetcher(LocalConceptStore  store, ConceptType type) {
		if (store==null) throw new NullPointerException();
		
		this.store = store;
		this.type = type;
	}

	public List<LocalConcept> getMeanings(String term) throws PersistenceException {
		DataSet<LocalConcept> m = store.getMeanings(term, type); //FIXME: filter/cut-off rules, sort order! //XXX: relevance value?
		return m.load();
	}

}
