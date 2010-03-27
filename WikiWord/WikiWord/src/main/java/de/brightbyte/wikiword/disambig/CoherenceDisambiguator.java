package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

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
import de.brightbyte.wikiword.model.WikiWordRanking;

public class CoherenceDisambiguator<K> extends AbstractDisambiguator {
	
	protected int minPopularity = 2; //FIXME: use complex cutoff specifier! 
	protected double scoreThreshold = 0.002; //FIXME: magic number
	protected double popularityBias = 0.01; //FIXME: magic number
	
	protected Similarity<LabeledVector<K>> similarityMeasure;
	protected FeatureFetcher<LocalConcept, K> featureFetcher;
	protected Measure<WikiWordRanking> popularityMeasure;
	protected PopularityDisambiguator popularityDisambiguator;
	
	public CoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, K> featureFetcher, boolean featuresAreNormalized) {
		this(meaningFetcher, featureFetcher, WikiWordRanking.theCardinality, 
					featuresAreNormalized ? ScalarVectorSimilarity.<K>getInstance() : CosineVectorSimilarity.<K>getInstance());  //if pre-normalized, use scalar to calc cosin
	}
	
	public CoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, K> featureFetcher, Measure<WikiWordRanking> popularityMeasure, Similarity<LabeledVector<K>> sim) {
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

	public void setFeatureFetcher(FeatureFetcher<LocalConcept, K> featureFetcher) {
		this.featureFetcher = featureFetcher;
	}

	public Similarity<LabeledVector<K>> getSimilarityMeasure() {
		return similarityMeasure;
	}

	public void setSimilarityMeasure(
			Similarity<LabeledVector<K>> similarityMeasure) {
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

	public double getScoreThreshold() {
		return scoreThreshold;
	}

	public void setScoreThreshold(double threshold) {
		this.scoreThreshold = threshold;
	}

	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.disambig.Disambiguator#disambiguate(java.util.List)
	 */
	public Result disambiguate(List<String> terms, Map<String, List<LocalConcept>> meanings) throws PersistenceException {
		if (terms.size()<2 || meanings.size()<2) 
				return popularityDisambiguator.disambiguate(terms, meanings);
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		FeatureCache<LocalConcept, K> features = new FeatureCache<LocalConcept, K>(featureFetcher); //TODO: keep a chain of n caches, resulting in LRU logic.
		
		List<Map<String, LocalConcept>> interpretations = getInterpretations(terms, meanings);

		return getBestInterpretation(terms, meanings, interpretations, similarities, features);
	}
	
	protected Result getBestInterpretation(List<String> terms, Map<String, List<LocalConcept>> meanings, 
			List<Map<String, LocalConcept>> interpretations, 
			LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureCache<LocalConcept, K> features) throws PersistenceException {
		
		List<Result> rankings = new ArrayList<Result>();
		
		for (Map<String, LocalConcept> interp: interpretations) {
			Result r = getScore(interp, similarities, features);
			if (r.getScore() <= scoreThreshold) continue;
			
			rankings.add(r);
		}
		
		if (rankings.size()==0) {
			return popularityDisambiguator.disambiguate(terms, meanings);
		}
		
		Collections.sort(rankings);
		Collections.reverse(rankings);
		
		if (trace!=null) {
			int c = 0;
			double limit = -1;
			for (Result r: rankings) {
				if (limit<0) limit = r.getScore() / 2;
				else if (r.getScore()<limit) break;
				
				trace(" = "+r);
				c++;
				
				if (c>10) break;
			}
		}
		
		//TODO: if result is tight (less than 50% distance), use more popularity score!
		Result r = rankings.get(0);
		return r;
	}

	protected List<Map<String, LocalConcept>> getInterpretations(List<String> terms, Map<String, List<LocalConcept>> meanings) {
		if (terms.size()==0) {
			return Collections.singletonList(Collections.<String, LocalConcept>emptyMap());
		}
		
		String t = terms.get(0);
		List<LocalConcept> m = meanings.get(t);
		
		List<Map<String, LocalConcept>> base = getInterpretations(terms.subList(1, terms.size()), meanings);

		if (m==null || m.size()==0) return base;
		
		List<Map<String, LocalConcept>> interpretations = new ArrayList<Map<String, LocalConcept>>();
		
		for (Map<String, LocalConcept> be: base) {
			for (LocalConcept c: m) {
				double p = popularityMeasure.measure(c); 
				if (p<minPopularity) continue;
				
				Map<String, LocalConcept> e = new HashMap<String, LocalConcept>();
				e.putAll(be);
				e.put(t, c);

				interpretations.add(e);
			}
		}
		
		trace("    ~ "+t+": "+interpretations.size()+" combinations");
		return interpretations;
	}

	protected Result getScore(Map<String, LocalConcept> interp, LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureCache<LocalConcept, K> features) throws PersistenceException {
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
						ConceptFeatures<LocalConcept, K> fa = features.getFeatures(a);
						ConceptFeatures<LocalConcept, K> fb = features.getFeatures(b);
						
						//force relevance/cardinality to the figures from the meaning lookup
						//not strictly necessary, but nice to keep it consistent.
						fa.getConceptReference().setCardinality(a.getCardinality());
						fa.getConceptReference().setRelevance(a.getRelevance());
						fb.getConceptReference().setCardinality(b.getCardinality());
						fb.getConceptReference().setRelevance(b.getRelevance());
						
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
		sim = sim / n; 
		pop = pop / c;
		
		double popf = 1 - 1/(Math.sqrt(pop)+1);  //converge against 1 //XXX: black voodoo magic ad hoc formula with no deeper meaing.
		
		double score =  popf * popularityBias + sim * ( 1 - popularityBias );
		return new Result(interp, score, sim, pop);
	}
	
}
