package de.brightbyte.wikiword.disambig;

import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.store.LocalConceptStore;

public class PopularityDisambiguator extends AbstractDisambiguator {
	public PopularityDisambiguator(LocalConceptStore conceptStore) {
		super(conceptStore);
	}

	public Result disambiguate(List<String> terms) throws PersistenceException {
		Map<String, LocalConcept> disambig = new HashMap<String, LocalConcept>();
		double pop = 0;
		
		for (String t: terms) {
			DataSet<LocalConcept> mm = conceptStore.getMeanings(t);
			List<LocalConcept> m = mm.load();
			if (m.size()==0) continue;
			
			Collections.sort(m, LocalConcept.byCardinality);
			
			LocalConcept c = m.get(0);
			disambig.put(t, c);

			pop += Math.log(c.getCardinality());
		}

		pop = pop / disambig.size();
		
		Result r = new Result(disambig, pop, -1, pop);
		return r;
	}

}
