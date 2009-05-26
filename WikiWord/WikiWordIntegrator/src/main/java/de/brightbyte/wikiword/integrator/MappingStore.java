package de.brightbyte.wikiword.integrator;

import de.brightbyte.util.PersistenceException;

public interface MappingStore {
		public void storeMapping(FeatureSet source, FeatureSet target, FeatureSet props) throws PersistenceException;
}
