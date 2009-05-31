package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureSet;

public interface MappingStore {
		public void storeMapping(FeatureSet source, FeatureSet target, FeatureSet props) throws PersistenceException;
}
