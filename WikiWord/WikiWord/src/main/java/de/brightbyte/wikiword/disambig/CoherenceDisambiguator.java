package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;

import de.brightbyte.data.Functor;
import de.brightbyte.data.Functor2;
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
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class CoherenceDisambiguator extends AbstractDisambiguator<TermReference, LocalConcept> {
	
	protected int minPopularity = 2; //FIXME: use complex cutoff specifier!
	protected int maxMeanings = 8; //FIXME: magic...
	
	protected double minScore = 0.1; //FIXME: magic number. should "somehow" match popularityNormalizer and similarityNormalizer
	//protected double popularityBias = 0.2; //FIXME: magic number. should "somehow" match popularityNormalizer and similarityNormalizer
	//protected double weightBias = 0.5; //FIXME: magic number. should "somehow" match popularityNormalizer 
	
	protected FeatureCache.Manager<LocalConcept, Integer> featureCacheManager;

	protected Similarity<LabeledVector<Integer>> similarityMeasure;
	protected Measure<WikiWordConcept> popularityMeasure;
	protected PopularityDisambiguator popularityDisambiguator;
	protected Comparator<LocalConcept> popularityComparator;
	
	private Functor.Double popularityNormalizer = new Functor.Double() { //NOTE: must map [0:inf] to [0:1] and grow monotonously
		public double apply(double pop) {
			return 1 - 1/(Math.sqrt(Math.log(pop))+1);  //XXX: black voodoo magic ad hoc formula with no deeper meaing.
		}
	};
	
	private Functor.Double similarityNormalizer = new Functor.Double() { //NOTE: must map [0:1] to [0:1] and grow monotonously
		public double apply(double sim) {
			return Math.sqrt(Math.sqrt(sim));  //XXX: black voodoo magic ad hoc formula with no deeper meaing.
		}
	};

	protected Functor2.Double scoreCombiner = new LinearCombiner(0.8);
	protected Functor2.Double weightCombiner = ProductCombiner.instance;
	
	public CoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, boolean featuresAreNormalized) {
		this(meaningFetcher, featureFetcher, WikiWordConcept.theCardinality, 
					featuresAreNormalized ? ScalarVectorSimilarity.<Integer>getInstance() : CosineVectorSimilarity.<Integer>getInstance());  //if pre-normalized, use scalar to calc cosin
	}
	
	public CoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, Integer> featureFetcher, Measure<WikiWordConcept> popularityMeasure, Similarity<LabeledVector<Integer>> sim) {
		super(meaningFetcher);
		
		if (popularityMeasure==null) throw new NullPointerException();
		if (sim==null) throw new NullPointerException();
		if (featureFetcher==null) throw new NullPointerException();
		
		this.featureCacheManager = new FeatureCache.Manager<LocalConcept, Integer>(featureFetcher, 10); //TODO: depth
		this.popularityDisambiguator = new PopularityDisambiguator(meaningFetcher, popularityMeasure);
		
		this.setPopularityMeasure(popularityMeasure);
		this.setSimilarityMeasure(sim);
	}
	
	public Functor.Double getPopularityNormalizer() {
		return popularityNormalizer;
	}

	public void setPopularityNormalizer(Functor.Double popularityFactor) {
		this.popularityNormalizer = popularityFactor;
	}

	public Measure<WikiWordConcept> getPopularityMeasure() {
		return popularityMeasure;
	}

	public void setPopularityMeasure(Measure<WikiWordConcept> popularityMeasure) {
		this.popularityMeasure = popularityMeasure;
		this.popularityDisambiguator.setPopularityMeasure(popularityMeasure);
		this.popularityComparator = new Measure.Comparator<LocalConcept>(popularityMeasure, true);
	}

	public void setWeightCombiner(Functor2.Double weightCombiner) {
		this.weightCombiner = weightCombiner;
		this.popularityDisambiguator.setWeightCombiner(weightCombiner);
	}

	public Functor2.Double getScoreCombiner() {
		return scoreCombiner;
	}

	public void setScoreCombiner(Functor2.Double scoreCombiner) {
		this.scoreCombiner = scoreCombiner;
	}

	public Functor.Double getSimilarityNormalizer() {
		return similarityNormalizer;
	}

	public void setSimilarityNormalizer(Functor.Double similarityFactor) {
		this.similarityNormalizer = similarityFactor;
	}

	public void setFeatureFetcher(FeatureFetcher<LocalConcept, Integer> featureFetcher) {
		this.featureCacheManager = new FeatureCache.Manager<LocalConcept, Integer>(featureFetcher, 10); //FIXME: depth
	}

	public Similarity<LabeledVector<Integer>> getSimilarityMeasure() {
		return similarityMeasure;
	}

	public void setSimilarityMeasure(
			Similarity<LabeledVector<Integer>> similarityMeasure) {
		if (similarityMeasure==null) throw new NullPointerException();
		this.similarityMeasure = similarityMeasure;
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

	protected FeatureFetcher<LocalConcept, Integer> getFeatureCache(Map<? extends TermReference, List<? extends LocalConcept>> meanings, Collection<? extends LocalConcept> context) throws PersistenceException {
		FeatureFetcher<LocalConcept, Integer> features = featureCacheManager.newCache();
		
		//NOTE: pre-fetch all features in one go
		List<LocalConcept> concepts = new ArrayList<LocalConcept>(meanings.size()*10);
		for (List<? extends LocalConcept> m: meanings.values()) {
			if (m!=null) concepts.addAll(m);
		}
		
		if (context!=null) concepts.addAll(context);
		features.getFeatures(concepts);
		
		return features;
	}
	
	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public <X extends TermReference>Disambiguator.Result<X, LocalConcept> disambiguate(PhraseNode<X> root, Map<X, List<? extends LocalConcept>> meanings, Collection<? extends LocalConcept> context) throws PersistenceException {
		if (meanings.isEmpty()) return new Disambiguator.Result<X, LocalConcept>(Collections.<X, LocalConcept>emptyMap(), Collections.<X>emptyList(), 0.0, "no terms or meanings");
		
		int sz = meanings.size();
		if (context!=null) sz += context.size();
		
		if (sz<2) { 
				return popularityDisambiguator.disambiguate(root, meanings, context);
		}
		
		pruneMeanings(meanings);
		
		sz = meanings.size();
		if (context!=null) sz += context.size();
		if (sz <2) {
			return popularityDisambiguator.disambiguate(root, meanings, context);
		}
		
		Collection<List<X>> sequences = getSequences(root, Integer.MAX_VALUE);
		return disambiguate(sequences, root, meanings, context);
	}
	
	public <X extends TermReference>Disambiguator.Result<X, LocalConcept> disambiguate(Collection<List<X>> sequences, PhraseNode<X> root, Map<X, List<? extends LocalConcept>> meanings, Collection<? extends LocalConcept> context) throws PersistenceException {
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.

		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureFetcher<LocalConcept, Integer> features = getFeatureCache(meanings, context); 
		
		Collection<Disambiguator.Interpretation<X, LocalConcept>> interpretations = getInterpretations(sequences, meanings);

		return getBestInterpretation(root, meanings, context, interpretations, similarities, features);
	}
	
	protected void pruneMeanings(Map<? extends TermReference, List<? extends LocalConcept>> meanings) {
		if (minPopularity<=1) return; //nothing to do
		
		Iterator<?> eit = meanings.entrySet().iterator();
		while (eit.hasNext()) {
			Entry<TermReference, List<? extends LocalConcept>> e = (Entry<TermReference, List<? extends LocalConcept>>) eit.next(); //XXX: ugly cast. got confused about generics. ugh.
			List<? extends LocalConcept> m = e.getValue();
			if (m==null) continue;
			
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
				Collections.sort(m, popularityComparator);
				m = m.subList(0, maxMeanings);
				e.setValue(m);
			}
		}
	}

	protected <X extends TermReference>Result<X, LocalConcept> getBestInterpretation(PhraseNode<X> root, Map<X, List<? extends LocalConcept>> meanings, 
			Collection<? extends LocalConcept> context, Collection<Disambiguator.Interpretation<X, LocalConcept>> interpretations, 
			LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureFetcher<LocalConcept, Integer> features) throws PersistenceException {
		
		List<Result<X, LocalConcept>> rankings = new ArrayList<Result<X, LocalConcept>>();
		
		double traceLimit = -1;
		for (Disambiguator.Interpretation<X, LocalConcept> interp: interpretations) {
			Result<X, LocalConcept> r = getScore(interp, context, similarities, features);
			
			if (r.getScore() >= minScore) {
				rankings.add(r);

				if (traceLimit<0) traceLimit = r.getScore() / 2;
				if (r.getScore() >= traceLimit && rankings.size()<=10) trace(" = "+r);
			}
		}
		
		if (rankings.size()==0) {
			return popularityDisambiguator.disambiguate(root,  meanings, context);
		}
		
		Collections.sort(rankings);
		Collections.reverse(rankings);
		
		//TODO: if result is tight (less than 50% distance), use more popularity score!
		Result<X, LocalConcept> r = rankings.get(0);
		return r;
	}

	public <X extends TermReference>Collection<Disambiguator.Interpretation<X, LocalConcept>> getInterpretations(Collection<List<X>> sequences, Map<X, List<? extends LocalConcept>> meanings) {
		List<Disambiguator.Interpretation<X, LocalConcept>> interpretations = new ArrayList<Disambiguator.Interpretation<X, LocalConcept>>();
		for (List<X> sq: sequences) {
			if (sq.isEmpty()) continue;
			Collection<Disambiguator.Interpretation<X, LocalConcept>> sqint = getSequenceInterpretations(sq, meanings);
			interpretations.addAll(sqint);
		}
		
		return interpretations;
	}
	
	public <X extends TermReference>Collection<Disambiguator.Interpretation<X, LocalConcept>> getSequenceInterpretations(List<X> sequence, Map<X, List<? extends LocalConcept>> meanings) {
		if (sequence.size()==0) {
			return Collections.singletonList(new Disambiguator.Interpretation<X, LocalConcept>(Collections.<X, LocalConcept>emptyMap(), sequence));
		}
		
		X t = sequence.get(0);
		List<? extends LocalConcept> m = meanings.get(t);
		
		Collection<Disambiguator.Interpretation<X, LocalConcept>> base = getSequenceInterpretations(sequence.subList(1, sequence.size()), meanings);

		if (m==null || m.size()==0) return base;
		
		List<Disambiguator.Interpretation<X, LocalConcept>> interpretations = new ArrayList<Disambiguator.Interpretation<X, LocalConcept>>();
		
		for (Disambiguator.Interpretation<X, LocalConcept> be: base) {
			for (LocalConcept c: m) {
				Map<X, LocalConcept> e = new HashMap<X, LocalConcept>();
				e.putAll(be.getMeanings());
				e.put(t, c);

				if (!sequence.isEmpty()) {
					Disambiguator.Interpretation<X, LocalConcept>interp = new Disambiguator.Interpretation<X, LocalConcept>(e, sequence);
					interpretations.add(interp);
				}
			}
		}
		
		trace("    ~ "+t+": "+m.size()+" meanings; collected "+interpretations.size()+" combinations");
		return interpretations;
	}

	protected <X extends TermReference>Result<X, LocalConcept> getScore(Disambiguator.Interpretation<X, LocalConcept> interp, Collection<? extends LocalConcept> context, LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureFetcher<LocalConcept, Integer> features) throws PersistenceException {
		Map<? extends TermReference, LocalConcept> concepts;
		if (context!=null) {
			concepts = new HashMap<TermReference, LocalConcept>();
			
			for (Map.Entry<X, LocalConcept> e: interp.getMeanings().entrySet()) {
				((HashMap<TermReference, LocalConcept>)concepts).put(e.getKey(), e.getValue());
			}
			
			for (LocalConcept c: context) {
				((HashMap<TermReference, LocalConcept>)concepts).put(new Term("", 1), c);
			}
		} else {
			concepts = interp.getMeanings();
		}
		
		double sim = 0, pop = 0, weight = 0;
		int i=0, j=0, n=0, c=0;
		for (Map.Entry<? extends TermReference, LocalConcept> ea: concepts.entrySet()) {
			LocalConcept a = ea.getValue();
			TermReference term = ea.getKey();

			i++;
			if (a==null) continue;
			
			j=0;
			for (Map.Entry<? extends TermReference, LocalConcept> eb: concepts.entrySet()) {
				LocalConcept b = eb.getValue();
				j++;
				if (i==j) break;
				if (b==null) continue;
				
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
						
						if (fa==null || fb==null) d = 0;
						else {
								//force relevance/cardinality to the figures from the meaning lookup
								//not strictly necessary, but nice to keep it consistent.
								fa.getConcept().setCardinality(a.getCardinality());
								fa.getConcept().setRelevance(a.getRelevance());
								fb.getConcept().setCardinality(b.getCardinality());
								fb.getConcept().setRelevance(b.getRelevance());
								
								d = similarityMeasure.similarity(fa.getFeatureVector(), fb.getFeatureVector());
						}
						
						similarities.set(a, b, d);
					}
				}
				
				if (d<0) throw new IllegalArgumentException("encountered negative similarity score ("+d+") for "+a+" / "+b);
				sim += d;
				n ++; //should add up to interp.size*(combo.size()-1)/2, according to Gauss
			}
			
			double p = popularityMeasure.measure(a); 
			double w = term.getWeight();
			if (p<1) p= 1;
			if (w<1) w= 1;
			
			p = weightCombiner.apply(p, w);
			
			pop += p; //XXX: keep raw and processed pop 
			weight += w; 
			c ++;
		}
		
		//normalize
		sim = sim / n; //scale
		pop = pop / c; //scale
		weight = weight / c; //scale
		
		double popf = popularityNormalizer.apply(pop);
		double simf = similarityNormalizer.apply(sim);

		double score = scoreCombiner.apply(popf, simf);
		
		return new Result<X, LocalConcept>(interp.getMeanings(), interp.getSequence(), score, "simf="+simf+", popf="+popf+", sim="+sim+", pop="+pop+", weight="+weight);
	}

}
