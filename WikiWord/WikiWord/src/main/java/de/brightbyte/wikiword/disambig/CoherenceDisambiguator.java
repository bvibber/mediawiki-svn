package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.LabeledMatrix;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledMatrix;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.LocalConcept;

public class CoherenceDisambiguator<K> extends AbstractDisambiguator {
	
	protected int minPopularity = 2; //FIXME: use complex cutoff specifier! 
	protected double scoreThreshold = 0.002;
	protected double popularityBias = 0.01;
	protected Similarity<LabeledVector<K>> similarityMeasure;
	protected FeatureFetcher<LocalConcept, K> featureFetcher;
	protected Measure<LocalConcept> popularityMeasure;
	protected PopularityDisambiguator popularityDisambiguator;
	
	public CoherenceDisambiguator(MeaningFetcher<LocalConcept> meaningFetcher, FeatureFetcher<LocalConcept, K> featureFetcher, Measure<LocalConcept> popularityMeasure, Similarity<LabeledVector<K>> sim) {
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
		if (meanings.size()==1) {
			return popularityDisambiguator.disambiguate(terms, meanings);
		}
		
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
			List<Map<String, LocalConcept>> combinations = new ArrayList<Map<String, LocalConcept>>();
			Map<String, LocalConcept> e = new HashMap<String, LocalConcept>();
			combinations.add(e);
			return combinations;
		}
		
		String t = terms.get(0);
		List<LocalConcept> m = meanings.get(t);
		
		List<Map<String, LocalConcept>> base = getInterpretations(terms.subList(1, terms.size()), meanings);

		if (m==null || m.size()==0) return base;
		
		List<Map<String, LocalConcept>> interpretations = new ArrayList<Map<String, LocalConcept>>();
		
		for (Map<String, LocalConcept> be: base) {
			for (LocalConcept c: m) {
				Map<String, LocalConcept> e = new HashMap<String, LocalConcept>();
				e.putAll(be);
				e.put(t, c);

				interpretations.add(e);
			}
		}
		
		trace("    ~ "+t+": "+interpretations.size()+" combinations");
		return interpretations;
	}

	protected Result getScore(Map<String, LocalConcept> interp, LabeledMatrix<LocalConcept, LocalConcept> similarities, FeatureCache features) throws PersistenceException {
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
						
						d = similarityMeasure.similarity(fa.getFeatureVector(), fb.getFeatureVector());
						similarities.set(a, b, d);
					}
				}
				
				if (d<0) throw new IllegalArgumentException("encountered negative similarity score ("+d+") for "+a+" / "+b);
				sim += d;
				n ++; //should add up to combo.size*(combo.size()-1)/2, according to Gauss
			}
			
			int card = a.getCardinality(); //XXX: this may be local cardinality (indegree), we want the frequency of the meaning-assignment!
			if (card<=0) card= 1;
			pop += card; 
			c ++;
		}
		
		//normalize
		sim = sim / n; 
		pop = pop / c;
		
		double popf = 1 - 1/(Math.sqrt(pop)+1);  //converge against 1
		
		double score =  popf * popularityBias + sim * ( 1 - popularityBias );
		return new Result(interp, score, sim, pop);
	}
	
}
