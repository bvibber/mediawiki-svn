package de.brightbyte.wikiword.disambig;

import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.measure.Measure;
import de.brightbyte.data.measure.Measure.Comparator;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class PopularityDisambiguator extends AbstractDisambiguator<TermReference, LocalConcept> {
	
	protected Measure<WikiWordConcept> popularityMeasure;
	protected Comparator<WikiWordConcept> popularityComparator;
	
	public PopularityDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher) {
		this(meaningFetcher, WikiWordConcept.theCardinality);
	}
	
	public PopularityDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, Measure<WikiWordConcept> popularityMeasure) {
		super(meaningFetcher);
		
		this.popularityMeasure = popularityMeasure;
		this.popularityComparator = new Measure.Comparator<WikiWordConcept>(popularityMeasure, true);
	}

	public <X extends TermReference>Result<X, LocalConcept> disambiguate(List<X> terms, Map<X, List<? extends LocalConcept>> meanings) {
		Map<X, LocalConcept> disambig = new HashMap<X, LocalConcept>();
		int pop = 0;
		for (X t: terms) {
			List<? extends LocalConcept> m = meanings.get(t);
			if (m==null || m.size()==0) continue;
			
			if (m.size()>0) Collections.sort(m, popularityComparator);
			
			LocalConcept c = m.get(0);
			disambig.put(t, c);

			pop += Math.log(c.getCardinality());
		}

		if (disambig.size()>0) pop = pop / disambig.size();
		
		Result<X, LocalConcept> r = new Result<X, LocalConcept>(disambig, pop, "pop="+pop);
		return r;
	}

}
