package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;

public interface ProximityStoreBuilder extends WikiWordStoreBuilder {
	
	public static class FeatureVectorFactors {
		//FIXME: this is all fishy ad-hoc cruft!
		
		//NOTE: since there are usually more link than categories, there's a bias in favor of categories!
		//       number of links grows with article length, number of categories does not!

		public final double selfWeight;
		//public final double weightOffset;
		
		public final double downWeight;
		public final double downBiasCoef;
		
		public final double upWeight;
		public final double upBiasCoef;
		
		public final double inWeight;
		public final double inBiasCoef;
		
		public final double outWeight;
		public final double outBiasCoef;
		
		public FeatureVectorFactors(TweakSet tweaks) {
			selfWeight = tweaks.getTweak("proximity.selfWeight", 2);
			//weightOffset = 1;
			
			downWeight = tweaks.getTweak("proximity.downWeight", 0.2); //having common children is not very relevant; also, categorization is favored by systemic bias, so tone it down.
			downBiasCoef = tweaks.getTweak("proximity.downBiasCoef", 1); //if the parent has many children should be considered
			
			upWeight = tweaks.getTweak("proximity.upWeight", 1.2); //having common parents is interesting; note: categorization is favored by systemic bias, but the bias is tuned out here anyway.
			upBiasCoef = tweaks.getTweak("proximity.upBiasCoef", 0.1); //if a child has many parents doesn't matter
			
			inWeight = tweaks.getTweak("proximity.inWeight", 1.5); //bein referenced from the same place is a strong factor
			inBiasCoef = tweaks.getTweak("proximity.inBiasCoef", 1); //if the concept is referenced a lot, co-reference becvomes less relevant
			
			outWeight = tweaks.getTweak("proximity.outWeight", 1.0); //referencing the same thing is a good indicator
			outBiasCoef = tweaks.getTweak("proximity.outBiasCoef", 0.2); //if the concept has many outgoing links doesn't matter much
		}
	}
	
	public void buildFeatures() throws PersistenceException;
	public void buildProximity() throws PersistenceException;
	
}