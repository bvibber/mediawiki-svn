package de.brightbyte.wikiword.disambig;

import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.measure.Measure;
import de.brightbyte.data.measure.Measure.Comparator;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.WikiWordRanking;

public class PopularityDisambiguator extends AbstractDisambiguator {
	
	protected Measure<WikiWordRanking> popularityMeasure;
	protected Comparator<WikiWordRanking> popularityComparator;
	
	public PopularityDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher) {
		this(meaningFetcher, WikiWordRanking.theCardinality);
	}
	
	public PopularityDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, Measure<WikiWordRanking> popularityMeasure) {
		super(meaningFetcher);
		
		this.popularityMeasure = popularityMeasure;
		this.popularityComparator = new Measure.Comparator<WikiWordRanking>(popularityMeasure, true);
	}

	public Result disambiguate(List<String> terms, Map<String, List<LocalConcept>> meanings) {
		Map<String, LocalConcept> disambig = new HashMap<String, LocalConcept>();
		int pop = 0;
		for (String t: terms) {
			List<LocalConcept> m = meanings.get(t);
			if (m.size()==0) continue;
			
			if (m.size()>0) Collections.sort(m, popularityComparator);
			
			LocalConcept c = m.get(0);
			disambig.put(t, c);

			pop += Math.log(c.getCardinality());
		}

		pop = pop / disambig.size();
		
		Result r = new Result(disambig, pop, -1, pop);
		return r;
	}

}
