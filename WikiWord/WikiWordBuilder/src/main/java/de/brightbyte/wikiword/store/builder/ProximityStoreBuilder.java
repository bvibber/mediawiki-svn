package de.brightbyte.wikiword.store.builder;

import java.util.HashMap;
import java.util.Map;
import java.util.Properties;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StructuredDataCodec;
import de.brightbyte.wikiword.TweakSet;

public interface ProximityStoreBuilder extends WikiWordStoreBuilder {
	
	public static class ProximityParameters {
		//FIXME: this is all fishy ad-hoc cruft!
		
		//NOTE: since there are usually more link than categories, there's a bias in favor of categories!
		//       number of links grows with article length, number of categories does not!

		public final double selfWeight;
		public final double proximityThreshold;
		
		public final double downWeight;
		public final double downBiasCoef;
		
		public final double upWeight;
		public final double upBiasCoef;
		
		public final double inWeight;
		public final double inBiasCoef;
		
		public final double outWeight;
		public final double outBiasCoef;

		public ProximityParameters() {
			selfWeight =  2;
			proximityThreshold = 0.15;
			
			downWeight = 0.2; //having common children is not very relevant; also, categorization is favored by systemic bias, so tone it down.
			downBiasCoef = 1; //if the parent has many children should be considered
			
			upWeight = 1.2; //having common parents is interesting; note: categorization is favored by systemic bias, but the bias is tuned out here anyway.
			upBiasCoef = 0.1; //if a child has many parents doesn't matter
			
			inWeight = 1.5; //bein referenced from the same place is a strong factor
			inBiasCoef = 1; //if the concept is referenced a lot, co-reference becvomes less relevant
			
			outWeight = 1.0; //referencing the same thing is a good indicator
			outBiasCoef = 0.2; //if the concept has many outgoing links doesn't matter much
		}
		
		public ProximityParameters(String props, ProximityParameters defaults) {
			this(StructuredDataCodec.instance.decodeMap(props), defaults);
		}
		
		public ProximityParameters(Properties props, ProximityParameters defaults) {
			this(StructuredDataCodec.instance.decodeValues(props), defaults);
		}
		
		public ProximityParameters(TweakSet tweaks, ProximityParameters defaults) {
			selfWeight = tweaks.getTweak("proximity.selfWeight", defaults.selfWeight);
			proximityThreshold = tweaks.getTweak("proximity.threshold", defaults.proximityThreshold);
			
			downWeight = tweaks.getTweak("proximity.downWeight", defaults.downWeight); 
			downBiasCoef = tweaks.getTweak("proximity.downBiasCoef", defaults.downBiasCoef);
			
			upWeight = tweaks.getTweak("proximity.upWeight", defaults.upWeight);
			upBiasCoef = tweaks.getTweak("proximity.upBiasCoef", defaults.upBiasCoef); 
			
			inWeight = tweaks.getTweak("proximity.inWeight", defaults.inWeight); 
			inBiasCoef = tweaks.getTweak("proximity.inBiasCoef", defaults.inBiasCoef);
			
			outWeight = tweaks.getTweak("proximity.outWeight", defaults.outWeight);
			outBiasCoef = tweaks.getTweak("proximity.outBiasCoef", defaults.outBiasCoef);
		}

		public ProximityParameters(Map<?, ?> params, ProximityParameters defaults) {
			selfWeight = params.containsKey("selfWeight") ? ((Number)params.get("proximity.selfWeight")).doubleValue() : defaults.selfWeight;
			proximityThreshold = params.containsKey("threshold") ? ((Number)params.get("proximity.threshold")).doubleValue() : defaults.proximityThreshold;
			
			downWeight = params.containsKey("downWeight") ? ((Number)params.get("proximity.downWeight")).doubleValue() : defaults.downWeight; 
			downBiasCoef = params.containsKey("downBiasCoef") ? ((Number)params.get("proximity.downBiasCoef")).doubleValue() : defaults.downWeight; 
			
			upWeight = params.containsKey("upWeight") ? ((Number)params.get("proximity.upWeight")).doubleValue() : defaults.upWeight; 
			upBiasCoef = params.containsKey("upBiasCoef") ? ((Number)params.get("proximity.upBiasCoef")).doubleValue() : defaults.upBiasCoef;
			
			inWeight = params.containsKey("inWeight") ? ((Number)params.get("proximity.inWeight")).doubleValue() : defaults.inWeight; 
			inBiasCoef = params.containsKey("inBiasCoef") ? ((Number)params.get("proximity.inBiasCoef")).doubleValue() : defaults.inBiasCoef; 
			
			outWeight = params.containsKey("outWeight")  ? ((Number)params.get("proximity.outWeight")).doubleValue() : defaults.outWeight; 
			outBiasCoef = params.containsKey("outBiasCoef")  ? ((Number)params.get("proximity.outBiasCoef")).doubleValue() : defaults.outBiasCoef; 
		}
	}
	
	public static final ProximityParameters heuristicParameters = new ProximityParameters();
	
	public static final ProximityParameters flatParameters = new ProximityParameters(
			new HashMap<String, Number>() {
				{
					put("selfWeight", 1);
					
					put("downWeight", 1);
					put("upWeight", 1);
					put("inWeight", 1);
					put("outWeight", 1);

					put("downBiasCoef", 0);
					put("upBiasCoef", 0);
					put("inBiasCoef", 0);
					put("outBiasCoef", 0);
				}
			}, heuristicParameters );
	
	public static final ProximityParameters biasedParameters = new ProximityParameters(
			new HashMap<String, Number>() {
				{
					put("selfWeight", 1);
					
					put("downWeight", 1);
					put("upWeight", 1);
					put("inWeight", 1);
					put("outWeight", 1);

					put("downBiasCoef", 1);
					put("upBiasCoef", 1);
					put("inBiasCoef", 1);
					put("outBiasCoef", 1);
				}
			}, heuristicParameters );
	
	public static final ProximityParameters unbiasedParameters = new ProximityParameters(
			new HashMap<String, Number>() {
				{
					put("downBiasCoef", 0);
					put("upBiasCoef", 0);
					put("inBiasCoef", 0);
					put("outBiasCoef", 0);
				}
			}, heuristicParameters );
	
	public static final ProximityParameters unweightedParameters = new ProximityParameters(
			new HashMap<String, Number>() {
				{
					put("selfWeight", 1);

					put("downWeight", 1);
					put("upWeight", 1);
					put("inWeight", 1);
					put("outWeight", 1);
				}
			}, heuristicParameters );
	
	public void buildFeatures() throws PersistenceException;
	public void buildBaseProximity() throws PersistenceException;
	public void buildExtendedProximity() throws PersistenceException;
	
}