package de.brightbyte.wikiword.store.builder;

import de.brightbyte.util.PersistenceException;

public interface ProximityStoreBuilder extends WikiWordStoreBuilder {
	
	public static class FeatureVectorFactors {
		//FIXME: this is all fishy ad-hoc cruft!
		
		//NOTE: since there are usually more link than categories, there's a bias in favor of categories!
		//       number of links grows with article length, number of categories does not!

		public final double selfWeight = 1;
		public final double weightOffset = 1;
		
		public final double downWeight = 0.5; //having common children is not very relevant
		public final double downBiasCoef = 0; //if a child has many parents doesn't matter
		
		public final double upWeight = 1.2; //having common parents is interesting
		public final double upBiasCoef = 1; //if the parent has many children should be considered
		
		public final double inWeight = 1.2; //bein referenced from the same place is a string factor
		public final double inBiasCoef = 0.2; //if the link's origin contains a lot of links is not so important; note: dampen link bias
		
		public final double outWeight = 0.5; //referencing the same thing isn't so very important
		public final double outBiasCoef = 0.5; //if the link's target is used a lot is a major factor; note: dampen link bias
	}
	
	public void buildFeatures() throws PersistenceException;
	public void buildProximity() throws PersistenceException;
	
}