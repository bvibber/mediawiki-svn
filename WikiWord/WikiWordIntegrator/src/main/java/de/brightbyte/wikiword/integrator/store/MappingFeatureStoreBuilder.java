package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface MappingFeatureStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
		public void storeMapping(FeatureSet source, FeatureSet target, FeatureSet props) throws PersistenceException;
}
