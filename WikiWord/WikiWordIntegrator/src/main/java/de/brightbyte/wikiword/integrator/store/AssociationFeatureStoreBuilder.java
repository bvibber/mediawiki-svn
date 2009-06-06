package de.brightbyte.wikiword.integrator.store;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public interface AssociationFeatureStoreBuilder extends WikiWordStoreBuilder, WikiWordConceptStoreBase {
		public void storeMapping(FeatureSet foreign, FeatureSet concept, FeatureSet props) throws PersistenceException;
}
