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
import de.brightbyte.util.SanityException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;

public class CoherenceDisambiguator<T extends TermReference, C extends WikiWordConcept> extends AbstractDisambiguator<T, C> {

	public static class CoherenceDisambiguation<T extends TermReference, C extends WikiWordConcept> extends Disambiguator.Disambiguation<T, C> {
		protected LabeledVector<Integer> centroid;
		protected Map<Integer, ConceptFeatures<C, Integer>> features;

		public CoherenceDisambiguation(Interpretation<T, C> interpretation, Map<Integer, ConceptFeatures<C, Integer>> features, LabeledVector<Integer> centroid, double score, String description) {
			super(interpretation, score, description);
			this.centroid = centroid;
			this.features = features;
		}

		public CoherenceDisambiguation(Map<T, C> meanings, List<T> sequence, Map<Integer, ConceptFeatures<C, Integer>> features, LabeledVector<Integer> centroid, double score, String description) {
			super(meanings, sequence, score, description);
			this.centroid = centroid;
			this.features = features;
		}

		public LabeledVector<Integer> getCentroid() {
			return centroid;
		}

		public Map<Integer, ConceptFeatures<C, Integer>> getFeatures() {
			return features;
		}
		
		public ConceptFeatures<C, Integer> getFeature(int concept) {
			return getFeatures().get(concept);
		}
		
	}
	
	protected int minPopularity = 2; //FIXME: use complex cutoff specifier!
	protected int maxMeanings = 8; //FIXME: magic...
	
	protected double minScore = 0.1; //FIXME: magic number. should "somehow" match popularityNormalizer and similarityNormalizer
	//protected double popularityBias = 0.2; //FIXME: magic number. should "somehow" match popularityNormalizer and similarityNormalizer
	//protected double weightBias = 0.5; //FIXME: magic number. should "somehow" match popularityNormalizer 
	
	protected FeatureFetcher<C, Integer> featureFetcher;

	protected Similarity<LabeledVector<Integer>> similarityMeasure;
	protected Measure<? super C> popularityMeasure;
	protected PopularityDisambiguator<T, C> popularityDisambiguator;
	protected Comparator<? super C> popularityComparator;
	
	private Functor.Double popularityNormalizer = new Functor.Double() { //NOTE: must map [0:inf] to [0:1] and grow monotonously
		public double apply(double pop) {
			if (pop<0.5) return 0;
			if (pop<1) pop=1;
			
			double n = 1 - 1/(Math.sqrt(Math.log(pop))+1); //XXX: black voodoo magic ad hoc formula with no deeper meaing.
			return n;  
		}
	};
	
	private Functor.Double similarityNormalizer = new Functor.Double() { //NOTE: must map [0:1] to [0:1] and grow monotonously
		public double apply(double sim) {
			return Math.sqrt(Math.sqrt(sim));  //XXX: black voodoo magic ad hoc formula with no deeper meaing.
		}
	};

	//protected Functor2.Double scoreCombiner = new LinearCombiner(0.8);
	protected Functor2.Double scoreCombiner = ProductCombiner.instance;
	protected Functor2.Double weightCombiner = ProductCombiner.instance;
	protected Functor.Double weightBooster = SquareBooster.instance; 
	
	public CoherenceDisambiguator(MeaningFetcher<? extends C> meaningFetcher, FeatureFetcher<C, Integer> featureFetcher, int cacheCapacity) {
		this(meaningFetcher, featureFetcher, cacheCapacity, null, null);  
	}
	
	public CoherenceDisambiguator(MeaningFetcher<? extends C> meaningFetcher, FeatureFetcher<C, Integer> featureFetcher, int cacheCapacity, Measure<? super C> popularityMeasure, Similarity<LabeledVector<Integer>> sim) {
		super(meaningFetcher, cacheCapacity);
		
		if (popularityMeasure==null) popularityMeasure = WikiWordConcept.theCardinality;
		if (sim==null) sim = featureFetcher.getFeaturesAreNormalized() ? ScalarVectorSimilarity.<Integer>getInstance() : CosineVectorSimilarity.<Integer>getInstance(); //if pre-normalized, use scalar to calc cosin
		if (featureFetcher==null) throw new NullPointerException();
		
		if (cacheCapacity>0) featureFetcher = new CachingFeatureFetcher<C, Integer>(featureFetcher, cacheCapacity);
		
		this.featureFetcher = featureFetcher; 
		this.popularityDisambiguator = new PopularityDisambiguator(getMeaningFetcher(), 0, popularityMeasure);
		
		this.setPopularityMeasure(popularityMeasure);
		this.setSimilarityMeasure(sim);
	}
	
	public FeatureFetcher<C, Integer> getFeatureFetcher() {
		return featureFetcher;
	}

	
	public Functor.Double getPopularityNormalizer() {
		return popularityNormalizer;
	}

	public void setPopularityNormalizer(Functor.Double popularityFactor) {
		this.popularityNormalizer = popularityFactor;
	}

	public Measure<? super C> getPopularityMeasure() {
		return popularityMeasure;
	}

	public Functor.Double getWeightBooster() {
		return weightBooster;
	}

	public void setWeightBooster(Functor.Double weightBooster) {
		this.popularityDisambiguator.setWeightBooster(weightBooster);
		this.weightBooster = weightBooster;
	}

	public Functor2.Double getWeightCombiner() {
		return weightCombiner;
	}

	public void setPopularityMeasure(Measure<? super C> popularityMeasure) {
		this.popularityMeasure = popularityMeasure;
		this.popularityDisambiguator.setPopularityMeasure(popularityMeasure);
		this.popularityComparator = new Measure.Comparator<C>(popularityMeasure, true);
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

	public void setFeatureFetcher(FeatureFetcher<C, Integer> featureFetcher) {
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

	protected FeatureFetcher<C, Integer> getFeatureCache(Map<? extends T, List<? extends C>> meanings, Collection<? extends C> context) throws PersistenceException {
		//NOTE: pre-fetch all features in one go
		List<C> concepts = new ArrayList<C>(meanings.size()*10);
		for (List<? extends C> m: meanings.values()) {
			if (m!=null) concepts.addAll(m);
		}
		
		if (context!=null) concepts.addAll(context);
		featureFetcher.getFeatures(concepts);
		
		return featureFetcher;
	}
	
	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public <X extends T>CoherenceDisambiguation<X, C> disambiguate(PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context) throws PersistenceException {
		if (meanings.isEmpty()) return new CoherenceDisambiguation<X, C>(Collections.<X, C>emptyMap(), Collections.<X>emptyList(), Collections.<Integer, ConceptFeatures<C, Integer>>emptyMap(), ConceptFeatures.newIntFeaturVector(1), 0.0, "no terms or meanings");
		
		LabeledMatrix<C, C> similarities = new MapLabeledMatrix<C, C>(true);
		FeatureFetcher<C, Integer> features = getFeatureCache(meanings, context); 

		int sz = meanings.size();
		if (context!=null) sz += context.size();
		
		if (sz<2) { 
			Disambiguation<X, C> r = popularityDisambiguator.disambiguate(root, meanings, context);
			return getScore(r.getInterpretation(), context, similarities, features); 
		}
		
		sz = meanings.size();
		if (context!=null) sz += context.size();
		if (sz <2) {
			Disambiguation<X, C> r = popularityDisambiguator.disambiguate(root, meanings, context);
			return getScore(r.getInterpretation(), context, similarities, features); 
		}
		
		Collection<List<X>> sequences = getSequences(root, Integer.MAX_VALUE);
		return disambiguate(sequences, root, meanings, context);
	}
	
	public <X extends T>CoherenceDisambiguation<X, C> disambiguate(Collection<List<X>> sequences, PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context) throws PersistenceException {
		LabeledMatrix<C, C> similarities = new MapLabeledMatrix<C, C>(true);
		FeatureFetcher<C, Integer> features = getFeatureCache(meanings, context); 

		return disambiguate(sequences, root, meanings, context, similarities, features);
	}
	
	private <X extends T>CoherenceDisambiguation<X, C> disambiguate(Collection<List<X>> sequences, PhraseNode<X> root, Map<X, List<? extends C>> meanings, Collection<? extends C> context, LabeledMatrix<C, C> similarities, FeatureFetcher<C, Integer> features) throws PersistenceException {
		
		//CAVEAT: because the map disambig can contain only one meaning per term, the same term can not occur with two meanings within the same term sequence.
		
		Collection<Disambiguator.Interpretation<X, C>> interpretations = getInterpretations(sequences, meanings);

		return getBestInterpretation(root, meanings, context, interpretations, similarities, features);
	}
	
	protected <X extends T>Map<X, List<? extends C>> getMeanings(Collection<X> terms) throws PersistenceException {
		Map<X, List<? extends C>> meanings = super.getMeanings(terms);
		pruneMeanings(meanings);
		return meanings;
	}
	
	protected void pruneMeanings(Map<? extends T, List<? extends C>> meanings) {
		Iterator<?> eit = meanings.entrySet().iterator();
		while (eit.hasNext()) {
			Entry<T, List<? extends C>> e = (Entry<T, List<? extends C>>) eit.next(); //XXX: ugly cast. got confused about generics. ugh.
			List<? extends C> m = e.getValue();
			if (m==null) continue;
			
			if (minPopularity>0) {
				Iterator<? extends C> cit = m.iterator();
				while (cit.hasNext()) {
					C c = cit.next();
					double p = popularityMeasure.measure(c);
					
					if (p<minPopularity) {
						trace("pruning unpopular meaning of "+e.getKey()+" (pop: "+p+" < "+minPopularity+"): "+c.getName());
						
						if (m.size()==1) {
							eit.remove();
							break;
						} else {
							cit.remove();
						}
					}
				}
			}
			
			if (m.size()==0) eit.remove();
			else if (m.size()>maxMeanings) {
				Collections.sort(m, popularityComparator);
				
				trace("pruning least popular meanings of "+e.getKey()+" (keeping top "+maxMeanings+"): "+m.subList(maxMeanings, m.size()));
				
				m = m.subList(0, maxMeanings);
				e.setValue(m);
			}
		}
	}

	protected <X extends T>CoherenceDisambiguation<X, C> getBestInterpretation(PhraseNode<X> root, Map<X, List<? extends C>> meanings, 
			Collection<? extends C> context, Collection<Disambiguator.Interpretation<X, C>> interpretations, 
			LabeledMatrix<C, C> similarities, FeatureFetcher<C, Integer> features) throws PersistenceException {

		CoherenceDisambiguation<X, C>  best = null;
		double bestScore = 0;
		
		if ( interpretations.size() == 0 ) return null;
		else if ( interpretations.size() == 1 ) {
			Disambiguator.Interpretation<X, C> interp = interpretations.iterator().next();
			CoherenceDisambiguation<X, C> r = getScore(interp, context, similarities, features);

			trace("only one interpretation available: "+r);
			return r;
		}
		
		trace("finding best of "+interpretations.size()+" interpretations.");
		
		for (Disambiguator.Interpretation<X, C> interp: interpretations) {
			CoherenceDisambiguation<X, C> r = getScore(interp, context, similarities, features);
			double score = r.getScore();
			//trace("       ~ score "+score+": "+r.getMeanings());
			
				if ( ( best == null &&  score> 0 && !Double.isNaN(score)) 
						|| (score > bestScore && !Double.isNaN(score)) ) {
					best = r;
					bestScore = r.getScore();
					
					trace("  - best so far (score "+bestScore+"): "+best);
				}
		}
		
		if (best==null || bestScore<minScore || Double.isNaN(bestScore)) {
			trace("best score is not good enough ("+bestScore+"<"+minScore+"), using popularity disambiguator.");
			
			Disambiguation<X, C> p = popularityDisambiguator.disambiguate(root, meanings, context);
			CoherenceDisambiguation<X, C> r = getScore(p.getInterpretation(), context, similarities, features);
			
			trace("best of "+interpretations.size()+" interpretations by popularity: "+r);
			return r;
		}
		
		trace("best of "+interpretations.size()+" interpretations (score "+bestScore+"): "+best);
		
		//XXX: if result is tight (less than 50% distance), use more popularity score??
		return best;
	}

	public <X extends T>Collection<Disambiguator.Interpretation<X, C>> getInterpretations(Collection<List<X>> sequences, Map<X, List<? extends C>> meanings) {
		List<Disambiguator.Interpretation<X, C>> interpretations = new ArrayList<Disambiguator.Interpretation<X, C>>();
		for (List<X> sq: sequences) {
			if (sq.isEmpty()) continue;
			Collection<Disambiguator.Interpretation<X, C>> sqint = getSequenceInterpretations(sq, meanings);
			interpretations.addAll(sqint);
		}
		
		return interpretations;
	}
	
	public <X extends T>Collection<Disambiguator.Interpretation<X, C>> getSequenceInterpretations(List<X> sequence, Map<X, List<? extends C>> meanings) {
		if (sequence.size()==0) {
			return Collections.singletonList(new Disambiguator.Interpretation<X, C>(Collections.<X, C>emptyMap(), sequence));
		}
		
		X t = sequence.get(0);
		List<? extends C> m = meanings.get(t);
		
		Collection<Disambiguator.Interpretation<X, C>> base = getSequenceInterpretations(sequence.subList(1, sequence.size()), meanings);

		List<Disambiguator.Interpretation<X, C>> interpretations = new ArrayList<Disambiguator.Interpretation<X, C>>();
		
		for (Disambiguator.Interpretation<X, C> be: base) {
			if (m==null || m.isEmpty()) {
				Disambiguator.Interpretation<X, C>interp = new Disambiguator.Interpretation<X, C>(be.getMeanings(), sequence);
				interpretations.add(interp);
			} else {
				for (C c: m) {
					Map<X, C> e = new HashMap<X, C>();
					e.putAll(be.getMeanings());
					e.put(t, c);
	
					Disambiguator.Interpretation<X, C>interp = new Disambiguator.Interpretation<X, C>(e, sequence);
					interpretations.add(interp);
				}
			}
		}
		
		trace("    ~ "+t+": "+(m==null ? "no": m.size())+" meanings; collected "+interpretations.size()+" combinations");
		return interpretations;
	}

	protected <X extends TermReference>CoherenceDisambiguation<X, C> getScore(Disambiguator.Interpretation<X, C> interp, Collection<? extends C> context, LabeledMatrix<C, C> similarities, FeatureFetcher<C, Integer> features) throws PersistenceException {
		Map<TermReference, C> concepts;
		
		if (context!=null || interp.getMeanings().size()!=interp.getSequence().size()) {
			concepts = new HashMap<TermReference, C>();
			
			for (TermReference t: interp.getSequence()) {
				C m = interp.getMeanings().get(t);
				((Map<TermReference, C>)concepts).put(t, m);
			}

			if (context != null) {
				for (C con: context) {
					if (con!=null)((Map<TermReference, C>)concepts).put(new Term(con.getName(), 1), con);
				}
			}
		} else {
			concepts = (Map<TermReference, C>)interp.getMeanings();
		}
		
		int c = concepts.size();

		if (c == 0) {
			CoherenceDisambiguation<X, C> r = new CoherenceDisambiguation<X, C>(interp.getMeanings(), interp.getSequence(), Collections.<Integer, ConceptFeatures<C, Integer>>emptyMap(), ConceptFeatures.newIntFeaturVector(1), 0, "empty");
			return r;
		} 

		for (X t: interp.getSequence()) { //also count "missing" concepts.
			if (!concepts.containsKey(t)) c++; 
		}

		int n = (c-1)*c/2; //Number of distinct pairs of different concepts, including "missing" concepts
		int simCount = 0; //simCount should be <= n in the end. count and check.
		
		LabeledVector<Integer> sum = ConceptFeatures.newIntFeaturVector( concepts.size() * 200 ); //XXX: magic number
		Map<Integer, ConceptFeatures<C, Integer>> disambigFeatures = new HashMap<Integer, ConceptFeatures<C, Integer>>();
		double sim = 0, pop = 0, weight = 0;
		int i=0, j=0;
		for (Map.Entry<TermReference, C> ea: concepts.entrySet()) {
			C a = ea.getValue();
			TermReference term = ea.getKey(); 
			
			i++;
			if (a==null) continue;
			
			ConceptFeatures<C, Integer> fa = features.getFeatures(a);
			disambigFeatures.put(a.getId(), fa);
			sum.add(fa.getFeatureVector());

			j=0;
			for (Map.Entry<TermReference, C> eb: concepts.entrySet()) {
				C b = eb.getValue();

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
						ConceptFeatures<C, Integer> fb = features.getFeatures(b);
						
						if (fa==null || fb==null) d = 0;
						else {
								//force relevance/cardinality to the figures from the meaning lookup
								//not strictly necessary, but nice to keep it consistent.
								fa.getConcept().setCardinality(a.getCardinality());
								fa.getConcept().setRelevance(a.getRelevance());
								fb.getConcept().setCardinality(b.getCardinality());
								fb.getConcept().setRelevance(b.getRelevance());
								
								//double lena = fa.getFeatureVector().getLength();
								//double lenb = fb.getFeatureVector().getLength();

								//if (lena<0 || lena>1.000000001) throw new SanityException("encountered bad length ("+lena+") for "+a+"; ooops!");
								//if (lenb<0 || lenb>1.000000001) throw new SanityException("encountered bad length ("+lenb+") for "+b+"; ooops!");
								
								
								d = similarityMeasure.similarity(fa.getFeatureVector(), fb.getFeatureVector());
						}
						
						//trace( String.format("      + sim(%s, %s) = %07.5f", a, b, d) );
						similarities.set(a, b, d);
					}
				}
				
				d = doubleSanity(d, "normal similarity score for "+a+" / "+b, "check similarityMeasure!", 0, 0.1, 1, 0.1);
				
				sim += d;
				simCount ++;
			}
			
			double p = popularityMeasure.measure(a); 
			double w = term.getWeight();
			w = weightBooster.apply(w);
			
			if (p<1) p= 1;
			if (w<1) w= 1;
			
			p = weightCombiner.apply(p, w);
			
			pop += p; //XXX: keep raw and processed pop 
			weight += w; 
		}
		
		//normalize
		LabeledVector<Integer> centroid = sum;
		centroid.compact();
		centroid.scale(1.0/i);
		
		if (n<simCount) {
			throw new SanityException("sim-count mismatches calculated number of permutations: counted "+simCount+", caclulated "+n);
		}
		
		sim = n == 0 ? 0 : sim / n; //scale
		pop = c == 0 ? 0 : pop / c; //scale
		weight = c == 0 ? 0 : weight / c; //scale
		
		pop = doubleSanity(pop, "normal popularity", "check popularityMeasure!", 0, 0.1, Double.MAX_VALUE, 0);
		sim = doubleSanity(sim, "normal average simility", "ooops!", 0, 0.1, 1, 0.1);
		
		double popf = popularityNormalizer.apply(pop);
		double simf = similarityNormalizer.apply(sim);

		popf = doubleSanity(popf, "normal popularity", "check popularityNormalizer!", 0, 0.1, 1, 0.1);
		simf = doubleSanity(simf, "normal similarity", "check similarityNormalizer!", 0, 0.1, 1, 0.1);
		
		double score = scoreCombiner.apply(simf, popf);
		score = doubleSanity(score, "score", "check scoreCombiner!", 0, 0.1, 1, 0.1);
		
		CoherenceDisambiguation<X, C> r = new CoherenceDisambiguation<X, C>(interp.getMeanings(), interp.getSequence(), disambigFeatures, centroid, score, "simf="+simf+", popf="+popf+", sim="+sim+", pop="+pop+", weight="+weight);
		return r;
	}
	
	protected double doubleSanity(double value, String name, String hint, double minValue, double minTollerance, double maxValue, double maxTollerance) {
		if ( Double.isNaN(value) ) {
			this.trace("Warning: encountered NaN value for "+name);
			value = minValue;
		}
		
		if (value<(minValue-minTollerance) || value>(maxValue+maxTollerance)) {
			String m = "encountered insane "+name+": "+value;
			if (hint != null) m += "; " + hint;
			throw new SanityException(m);
		}
		
		if (value<minValue) value= minValue;
		if (value>maxValue) value= maxValue;
		
		return value;
	}

}
