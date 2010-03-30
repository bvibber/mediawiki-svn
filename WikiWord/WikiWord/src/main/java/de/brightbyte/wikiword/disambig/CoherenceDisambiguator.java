package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;

import de.brightbyte.data.Functor;
import de.brightbyte.data.LabeledMatrix;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledMatrix;
import de.brightbyte.data.measure.CosineVectorSimilarity;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.data.measure.ScalarVectorSimilarity;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class CoherenceDisambiguator extends AbstractDisambiguator<TermReference, LocalConcept> {
	
	protected int minPopularity = 2; //FIXME: use complex cutoff specifier!
	protected int maxMeanings = 8; //FIXME: magic...
	
	protected double minScore = 0.1; //FIXME: magic number. should "somehow" match popularityFactor and similarityFactor
	protected double popularityBias = 0.2; //FIXME: magic number. should "somehow" match popularityFactor and similarityFactor
	
	protected Similarity<LabeledVector<Integer>> similarityMeasure;
	protected FeatureFetcher<LocalConcept, Integer> featureFetcher;
	protected Measure<WikiWordConcept> popularityMeasure;
	protected PopularityDisambiguator popularityDisambiguator;
	
	private Functor.Double popularityFactor = new Functor.Double() { //NOTE: must map [0:inf] to [0:1] and grow monotonously
	
		public double apply(double pop) {
			return 1 - 1/(Math.sqrt(Math.log(pop))+1);  //XXX: black voodoo magic ad hoc formula with no deeper meaing.
		}
	
	};
	
	private Functor.Double similarityFactor = new Functor.Double() { //NOTE: must map [0:1] to [0:1] and grow monotonously
		public double apply(double sim) {
			return Math.sqrt(Math.sqrt(sim));  //XXX: black voodoo magic ad hoc formula with no deeper meaing.
		}
	};
	
	public CoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, boolean featuresAreNormalized) {
		this(meaningFetcher, featureFetcher, WikiWordConcept.theCardinality, 
					featuresAreNormalized ? ScalarVectorSimilarity.<Integer>getInstance() : CosineVectorSimilarity.<Integer>getInstance());  //if pre-normalized, use scalar to calc cosin
	}
	
	public CoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, Measure<WikiWordConcept> popularityMeasure, Similarity<LabeledVector<Integer>> sim) {
		super(meaningFetcher);
		
		if (popularityMeasure==null) throw new NullPointerException();
		if (sim==null) throw new NullPointerException();
		if (featureFetcher==null) throw new NullPointerException();
		this.popularityMeasure = popularityMeasure;
		this.similarityMeasure = sim;
		this.featureFetcher = featureFetcher;
		this.popularityDisambiguator = new PopularityDisambiguator(meaningFetcher, popularityMeasure);
	}
	
	public FeatureFetcher getFeatureFetcher() {
		return featureFetcher;
	}

	public void setFeatureFetcher(FeatureFetcher<LocalConcept, Integer> featureFetcher) {
		this.featureFetcher = featureFetcher;
	}

	public Similarity<LabeledVector<Integer>> getSimilarityMeasure() {
		return similarityMeasure;
	}

	public void setSimilarityMeasure(
			Similarity<LabeledVector<Integer>> similarityMeasure) {
		if (similarityMeasure==null) throw new NullPointerException();
		this.similarityMeasure = similarityMeasure;
	}

	public double getPopularityBias() {
		return popularityBias;
	}

	public void setPopularityBias(double popularityBias) {
		this.popularityBias = popularityBias;
	}

	public int getMinPopularity() {
		return minPopularity;
	}

	public void setMinPopularity(int min) {
		this.minPopularity = min;
	}

	public double getMinScore() {
		return minScore;
	}

	public void setMinScore(double threshold) {
		this.minScore = threshold;
	}

	public int getMaxMeanings() {
		return maxMeanings;
	}

	public void setMaxMeanings(int maxMeanings) {
		this.maxMeanings = maxMeanings;
	}

	protected FeatureCache<LocalConcept, Integer> getFeatureCache(Map<? extends TermReference, List<? extends LocalConcept>> meanings) throws PersistenceException {
		//TODO: keep a chain of n caches, resulting in LRU logic.
		FeatureCache<LocalConcept, Integer> features = new FeatureCache<LocalConcept, Integer>(featureFetcher);
		
		//NOTE: pre-fetch all features in one go
		List<LocalConcept> concepts = new ArrayList<LocalConcept>(meanings.size()*10);
		for (List<? extends LocalConcept> m: meanings.values()) {
			concepts.addAll(m);
		}
		
		features.getFeatures(concepts);
		
		return features;
	}
	
	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public <X extends TermReference>Disambiguator.Result<X, LocalConcept> disambiguate(List<X> terms, Map<X, List<? extends LocalConcept>> meanings) throws PersistenceException {
		if (terms.size()<2 || meanings.size()<2) 
				return popularityDisambiguator.disambiguate(terms, meanings);
		
		pruneMeanings(meanings);
		
		if (meanings.size()<2) 
			return popularityDisambiguator.disambiguate(terms, meanings);
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.

		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureCache<LocalConcept, Integer> features = getFeatureCache(meanings); 
		
		List<Map<X, LocalConcept>> interpretations = getInterpretations(terms, meanings);

		return getBestInterpretation(terms, meanings, interpretations, similarities, features);
	}
	
	protected void pruneMeanings(Map<? extends TermReference, List<? extends LocalConcept>> meanings) {
		if (minPopularity<=1) return; //nothing to do
		
		Iterator<?> eit = meanings.entrySet().iterator();
		while (eit.hasNext()) {
			Entry<TermReference, List<? extends LocalConcept>> e = (Entry<TermReference, List<? extends LocalConcept>>) eit.next(); //XXX: ugly cast. got confused about generics. ugh.
			List<? extends LocalConcept> m = e.getValue();
			
			Iterator<? extends LocalConcept> cit = m.iterator();
			while (cit.hasNext()) {
				LocalConcept c = cit.next();
				double p = popularityMeasure.measure(c);
				
				if (p<minPopularity) {
					cit.remove();
				}
			}
			
			if (m.size()==0) eit.remove();
			else if (m.size()>maxMeanings) {
				Collections.sort(m, WikiWordConcept.byCardinality);
				m = m.subList(0, maxMeanings);
				e.setValue(m);
			}
		}
	}

	protected <X extends TermReference>Result<X, LocalConcept> getBestInterpretation(List<X> terms, Map<X, List<? extends LocalConcept>> meanings, 
			List<Map<X, LocalConcept>> interpretations, 
			LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureCache<LocalConcept, Integer> features) throws PersistenceException {
		
		List<Result<X, LocalConcept>> rankings = new ArrayList<Result<X, LocalConcept>>();
		
		double traceLimit = -1;
		for (Map<X, LocalConcept> interp: interpretations) {
			Result<X, LocalConcept> r = getScore(interp, similarities, features);
			
			if (r.getScore() >= minScore) {
				rankings.add(r);

				if (traceLimit<0) traceLimit = r.getScore() / 2;
				if (r.getScore() >= traceLimit && rankings.size()<=10) trace(" = "+r);
			}
		}
		
		if (rankings.size()==0) {
			return popularityDisambiguator.disambiguate(terms, meanings);
		}
		
		Collections.sort(rankings);
		Collections.reverse(rankings);
		
		//TODO: if result is tight (less than 50% distance), use more popularity score!
		Result<X, LocalConcept> r = rankings.get(0);
		return r;
	}

	protected <X extends TermReference>List<Map<X, LocalConcept>> getInterpretations(List<X> terms, Map<X, List<? extends LocalConcept>> meanings) {
		if (terms.size()==0) {
			return Collections.singletonList(Collections.<X, LocalConcept>emptyMap());
		}
		
		X t = terms.get(0);
		List<? extends LocalConcept> m = meanings.get(t);
		
		List<Map<X, LocalConcept>> base = getInterpretations(terms.subList(1, terms.size()), meanings);

		if (m==null || m.size()==0) return base;
		
		List<Map<X, LocalConcept>> interpretations = new ArrayList<Map<X, LocalConcept>>();
		
		for (Map<X, LocalConcept> be: base) {
			for (LocalConcept c: m) {
				Map<X, LocalConcept> e = new HashMap<X, LocalConcept>();
				e.putAll(be);
				e.put(t, c);

				interpretations.add(e);
			}
		}
		
		trace("    ~ "+t+": "+m.size()+" meanings; collected "+interpretations.size()+" combinations");
		return interpretations;
	}

	protected <X extends TermReference>Result<X, LocalConcept> getScore(Map<X, LocalConcept> interp, LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureCache<LocalConcept, Integer> features) throws PersistenceException {
		double sim = 0;
		double pop = 0;

		int i=0, j=0, n=0, c=0;
		for (LocalConcept a: interp.values()) {
			i++;
			j=0;
			for (LocalConcept b: interp.values()) {
				j++;
				if (i==j) break;
				
				double d;
				
				if (a==b || a.equals(b)) {
					d = 1;
				}
				else {
					if (similarities.contains(a, b)) {
						d = similarities.get(a, b);
					}
					else {
						ConceptFeatures<LocalConcept, Integer> fa = features.getFeatures(a);
						ConceptFeatures<LocalConcept, Integer> fb = features.getFeatures(b);
						
						//force relevance/cardinality to the figures from the meaning lookup
						//not strictly necessary, but nice to keep it consistent.
						fa.getConcept().setCardinality(a.getCardinality());
						fa.getConcept().setRelevance(a.getRelevance());
						fb.getConcept().setCardinality(b.getCardinality());
						fb.getConcept().setRelevance(b.getRelevance());
						
						d = similarityMeasure.similarity(fa.getFeatureVector(), fb.getFeatureVector());
						similarities.set(a, b, d);
					}
				}
				
				if (d<0) throw new IllegalArgumentException("encountered negative similarity score ("+d+") for "+a+" / "+b);
				sim += d;
				n ++; //should add up to interp.size*(combo.size()-1)/2, according to Gauss
			}
			
			double p = popularityMeasure.measure(a); 
			if (p<1) p= 1;
			pop += p; 
			c ++;
		}
		
		//normalize
		sim = sim / n; //normalize
		pop = pop / c; //normalize
		
		double popf = popularityFactor.apply(pop);
		double simf = similarityFactor.apply(sim);
		
		//FIXME: functor!
		double score =  popf * popularityBias + simf * ( 1 - popularityBias );
		//double score =  Math.sqrt( popf * simf ); //FIXME: functor!
		
		return new Result<X, LocalConcept>(interp, score, "simf="+simf+", popf="+popf+", sim="+sim+", pop="+pop);
	}

}
