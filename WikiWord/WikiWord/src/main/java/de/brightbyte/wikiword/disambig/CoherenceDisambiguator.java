package de.brightbyte.wikiword.disambig;

import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.LabeledMatrix;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledMatrix;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.data.measure.Similarity;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.WikiWordRanking;
import de.brightbyte.wikiword.store.LocalConceptStore;

public class CoherenceDisambiguator extends AbstractDisambiguator {
	
	protected int frequencyThreshold = 2; //FIXME: use complex cutoff specifier! 
	protected double scoreThreshold = 0.002;
	protected double popularityBias = 0.01;
	protected Similarity<LabeledVector<Integer>> similarityMeasure;
	protected FeatureFetcher<LocalConcept> featureFetcher;
	
	public CoherenceDisambiguator(LocalConceptStore conceptStore, FeatureFetcher<LocalConcept> featureFetcher, Similarity<LabeledVector<Integer>> sim) {
		super(conceptStore);
		
		if (sim==null) throw new NullPointerException();
		if (featureFetcher==null) throw new NullPointerException();
		this.similarityMeasure = sim;
		this.featureFetcher = featureFetcher;
	}
	
	public FeatureFetcher getFeatureFetcher() {
		return featureFetcher;
	}

	public void setFeatureFetcher(FeatureFetcher<LocalConcept> featureFetcher) {
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

	public int getFrequencyThreshold() {
		return frequencyThreshold;
	}

	public void setFrequencyThreshold(int threshold) {
		this.frequencyThreshold = threshold;
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
	public Result disambiguate(List<String> terms) throws PersistenceException {
		Map<String, List<LocalConcept>> meanings = new HashMap<String, List<LocalConcept>>();
		Set<LocalConcept> concepts = new HashSet<LocalConcept>();
		
		for (String t: terms) {
			DataSet<LocalConcept> mm = conceptStore.getMeanings(t);
			List<LocalConcept> m = mm.load();
			
			int maxCard = 0;
			Iterator<LocalConcept> it = m.iterator();
			while (it.hasNext()) {
				//FIXME: keep explicitly defined meanings!
				LocalConcept c = it.next();
				int card = c.getCardinality();
				if (maxCard<card) maxCard = card;
				
				if (card<maxCard) { //XXX: relies on descending order! //FIXME: ugly subsitute for "keep explicit" rule!
					if (card < frequencyThreshold) it.remove();
				}
			}
			
			trace(" - \""+t+"\": "+m.size()+" meanings");
			if (m.size()>0) meanings.put(t, m);
			
			for (LocalConcept c: m) {
				concepts.add(c);
			}
		}
		
		if (concepts.size()==0) {
			return null;
		}
		
		if (meanings.size()==1) {
			String t = meanings.keySet().iterator().next();
			List<LocalConcept> m = meanings.get(t);
			
			Collections.sort(m, WikiWordRanking.byCardinality);
			LocalConcept c = m.get(0);

			Map<String, LocalConcept> disambig = new HashMap<String, LocalConcept>();
			disambig.put(t, c);
			
			Result r = new Result(disambig, c.getCardinality(), -1, c.getCardinality());
			return r;
		}
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		
		List<Map<String, LocalConcept>> combinations = getCombinations(terms, meanings);
		List<Result> rankings = new ArrayList<Result>();
		
		for (Map<String, LocalConcept> combo: combinations) {
			Result r = getScore(combo, similarities);
			if (r.getScore() <= scoreThreshold) continue;
			
			rankings.add(r);
		}
		
		if (rankings.size()==0) {
			//NOTE: use most popular
			Map<String, LocalConcept> combo = new HashMap<String, LocalConcept>();
			
			for (String t: terms) {
				List<LocalConcept> mm = meanings.get(t);
				LocalConcept c = mm.isEmpty() ? null : mm.get(0);
				
				if (c!=null) combo.put(t, c);
			}
			
			Result r = getScore(combo, similarities);
			/*if (r.getScore() <= scoreThreshold) return null;
			else*/ return r;
		}
		
		Collections.sort(rankings);
		Collections.reverse(rankings);
		
		int c = 0;
		double limit = -1;
		for (Result r: rankings) {
			if (limit<0) limit = r.getScore() / 2;
			else if (r.getScore()<limit) break;
			
			trace(" = "+r);
			c++;
			
			if (c>10) break;
		}
		
		//TODO: if result is tight (less than 50% distance), use more popularity score!
		Result r = rankings.get(0);
		return r;
	}

	private List<Map<String, LocalConcept>> getCombinations(List<String> terms, Map<String, List<LocalConcept>> meanings) {
		if (terms.size()==0) {
			List<Map<String, LocalConcept>> combinations = new ArrayList<Map<String, LocalConcept>>();
			Map<String, LocalConcept> e = new HashMap<String, LocalConcept>();
			combinations.add(e);
			return combinations;
		}
		
		String t = terms.get(0);
		List<LocalConcept> m = meanings.get(t);
		
		List<Map<String, LocalConcept>> base = getCombinations(terms.subList(1, terms.size()), meanings);

		if (m==null || m.size()==0) return base;
		
		List<Map<String, LocalConcept>> combinations = new ArrayList<Map<String, LocalConcept>>();
		
		for (Map<String, LocalConcept> be: base) {
			for (LocalConcept c: m) {
				Map<String, LocalConcept> e = new HashMap<String, LocalConcept>();
				e.putAll(be);
				e.put(t, c);

				combinations.add(e);
			}
		}
		
		trace("    ~ "+t+": "+combinations.size()+" combinations");
		return combinations;
	}

	protected Result getScore(Map<String, LocalConcept> combo, LabeledMatrix<LocalConcept, LocalConcept> similarities) throws PersistenceException {
		double sim = 0;
		double pop = 0;

		int i=0, j=0, n=0, c=0;
		for (LocalConcept a: combo.values()) {
			i++;
			j=0;
			for (LocalConcept b: combo.values()) {
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
						ConceptFeatures<LocalConcept> fa = featureFetcher.getFeatures(a);
						ConceptFeatures<LocalConcept> fb = featureFetcher.getFeatures(b);
						
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
		return new Result(combo, score, sim, pop);
	}
	
}
