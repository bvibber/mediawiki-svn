package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface ProximityStoreBuilder extends WikiWordStoreBuilder {
	
	public static class FeatureVectorFactors {
		//FIXME: this is all fishy ad-hoc cruft!
		
		//NOTE: since there are usually more link than categories, there's a bias in favor of categories!
		//       number of links grows with article length, number of categories does not!

		public final double selfWeight = 4;
		//public final double weightOffset = 1;
		
		public final double downWeight = 0.2; //having common children is not very relevant; also, categorization is favored by systemic bias, so tone it down.
		public final double downBiasCoef = 1; //if the parent has many children should be considered
		
		public final double upWeight = 1.2; //having common parents is interesting; note: categorization is favored by systemic bias, but the bias is tuned out here anyway.
		public final double upBiasCoef = 0.1; //if a child has many parents doesn't matter
		
		public final double inWeight = 1.5; //bein referenced from the same place is a strong factor
		public final double inBiasCoef = 1; //if the concept is referenced a lot, co-reference becvomes less relevant
		
		public final double outWeight = 1.0; //referencing the same thing is a good indicator
		public final double outBiasCoef = 0.2; //if the concept has many outgoing links doesn't matter much
	}
	
	public void buildFeatures() throws PersistenceException;
	public void buildProximity() throws PersistenceException;
	
}